<?php
/**
 * Created by mXaln
 */

namespace App\Controllers;

use App\Models\NewsModel;
use App\Models\ApiModel;
use App\Repositories\Event\IEventRepository;
use App\Repositories\Member\IMemberRepository;
use Helpers\Arrays;
use Support\Collection;
use Support\Facades\View;
use Config\Config;
use Helpers\Url;
use Helpers\Gump;
use Helpers\Session;
use App\Core\Controller;
use Support\Facades\Cache;
use App\Models\EventsModel;
use App\Models\TranslationsModel;
use Helpers\Constants\EventSteps;
use Helpers\Constants\EventCheckSteps;
use Helpers\Constants\EventStates;
use Helpers\Constants\EventMembers;
use Mailer;

class ManageController extends Controller {
    private $_model;
    private $_translationModel;
    private $_apiModel;
    private $_newsModel;
    private $_notifications;
    private $_news;
    private $_newNewsCount;

    protected $memberRepo = null;
    protected $eventRepo = null;
    private $_member;

    public function __construct(
        IMemberRepository $memberRepo,
        IEventRepository $eventRepo
    ) {
        parent::__construct();

        $this->memberRepo = $memberRepo;
        $this->eventRepo = $eventRepo;

        if (Config::get("app.isMaintenance")
            && !in_array($_SERVER['REMOTE_ADDR'], Config::get("app.ips"))) {
            Url::redirect("maintenance");
        }

        if (!Session::get('memberID')) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $response["errorType"] = "logout";
                $response["error"] = __("not_loggedin_error");
                echo json_encode($response);
                exit;
            } else {
                Url::redirect('members/login');
            }
        }

        if (!preg_match("/^\\/events\\/demo|\\/events\\/faq/", $_SERVER["REQUEST_URI"])) {

            $this->_member = $this->memberRepo->get(Session::get('memberID'));

            if (!$this->_member) Url::redirect("members/login");

            if (!$this->_member->verified) {
                Url::redirect("members/error/verification");
            } elseif (!$this->_member->profile->complete) {
                Url::redirect("members/profile");
            }

            $this->_model = new EventsModel($this->eventRepo);
            $this->_translationModel = new TranslationsModel();
            $this->_apiModel = new ApiModel();
            $this->_newsModel = new NewsModel();

            $this->_notifications = $this->_model->getNotifications();
            $this->_notifications = Arrays::append($this->_notifications, $this->_model->getNotificationsOther());
            $this->_notifications = Arrays::append($this->_notifications, $this->_model->getNotificationsL2());
            $this->_notifications = Arrays::append($this->_notifications, $this->_model->getNotificationsL3());
            $this->_notifications = Arrays::append($this->_notifications, $this->_model->getNotificationsSun());
            $this->_notifications = Arrays::append($this->_notifications, $this->_model->getNotificationsRadio());

            $this->_news = $this->_newsModel->getNews();
            $this->_newNewsCount = 0;
            foreach ($this->_news as $news) {
                if (!isset($_COOKIE["newsid" . $news->id]))
                    $this->_newNewsCount++;
            }
        }
    }

    public function manage($eventID)
    {
        $event = $this->eventRepo->get($eventID);

        if ($event) {
            if (!$this->isAdmin($event)) {
                Url::redirect("events");
            }

            if ($event->project->bookProject == "tw") {
                Url::redirect("events/manage-tw/" . $event->eventID);
            }

            $tmpChapters = [];
            if ($event->project->bookProject == "tn")
                $tmpChapters[0] = [];

            for ($i = 1; $i <= $event->bookInfo->chaptersNum; $i++) {
                $tmpChapters[$i] = [];
            }

            $chapters = $this->_model->getChapters($event->eventID);
            foreach ($chapters as $chapter) {
                $tmp["trID"] = $chapter["trID"];
                $tmp["memberID"] = $chapter["memberID"];
                $tmp["chunks"] = json_decode($chapter["chunks"], true);
                $tmp["done"] = $chapter["done"];
                $tmp["verbCheck"] = (array)json_decode($chapter["verbCheck"], true);
                $tmp["kwCheck"] = (array)json_decode($chapter["kwCheck"], true);
                $tmp["crCheck"] = (array)json_decode($chapter["crCheck"], true);
                $tmp["peerCheck"] = (array)json_decode($chapter["peerCheck"], true);
                $tmp["otherCheck"] = (array)json_decode($chapter["otherCheck"], true);
                $tmp["step"] = $chapter["step"];
                $tmp["currentChapter"] = $chapter["currentChapter"];

                $tmpChapters[$chapter["chapter"]] = $tmp;
            }

            if ($event->project->sourceBible == "odb") {
                $data["odb"] = $this->_apiModel->getOtherSource("odb", $event->bookCode, $event->project->sourceLangID);
            } elseif ($event->project->bookProject == "rad") {
                $data["rad"] = $this->_apiModel->getOtherSource("rad", $event->bookCode, $event->project->sourceLangID);
            }

            $members = $event->translators;

            if (isset($_POST) && !empty($_POST)) {
                if (!empty(array_filter($tmpChapters))) {
                    $updated = $this->_model->updateEvent(
                        array(
                            "state" => EventStates::TRANSLATING,
                            "dateFrom" => date("Y-m-d H:i:s", time())),
                        array("eventID" => $eventID));
                    if ($updated)
                        Url::redirect("events/manage/" . $eventID);
                } else {
                    $error[] = __("event_chapters_error");
                }
            }
        } else {
            $error[] = __("empty_or_not_permitted_event_error");
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;

        $page = $event->langInput ? "ManageLangInput" : "Manage";

        return View::make('Events/' . $page)
            ->shares("title", __("manage_event"))
            ->shares("data", $data)
            ->shares("event", $event)
            ->shares("chapters", $tmpChapters)
            ->shares("members", $members)
            ->shares("error", @$error);
    }

    public function manageTw($eventID)
    {
        $event = $this->eventRepo->get($eventID);

        if ($event) {
            if (!$this->isAdmin($event)) {
                Url::redirect("events");
            }

            $data["tw_groups"] = $this->_model->getTwGroups(["eventID" => $event->eventID]);
            $data["words_in_groups"] = [];

            foreach ($data["tw_groups"] as $group) {
                $words = (array)json_decode($group->words, true);
                $data["words_in_groups"] = Arrays::append($data["words_in_groups"], $words);
            }

            $tmpChapters = [];

            foreach ($data["tw_groups"] as $group) {
                $tmpChapters[$group->groupID] = [];
            }

            $data["words"] = $this->getTranslationWordsByCategory(
                $event->bookInfo->name,
                $event->project->resLangID,
                true
            );

            $chapters = $this->_model->getChapters($event->eventID, null, null, $event->project->bookProject);
            foreach ($chapters as $chapter) {
                $tmp["trID"] = $chapter["trID"];
                $tmp["memberID"] = $chapter["memberID"];
                $tmp["chunks"] = json_decode($chapter["chunks"], true);
                $tmp["done"] = $chapter["done"];
                $tmp["otherCheck"] = (array)json_decode($chapter["otherCheck"], true);
                $tmp["peerCheck"] = (array)json_decode($chapter["peerCheck"], true);

                $tmpChapters[$chapter["chapter"]] = $tmp;
            }

            $members = $event->translators;

            if (isset($_POST) && !empty($_POST)) {
                if (!empty(array_filter($tmpChapters))) {
                    $updated = $this->_model->updateEvent(
                        array(
                            "state" => EventStates::TRANSLATING,
                            "dateFrom" => date("Y-m-d H:i:s", time())),
                        array("eventID" => $eventID));
                    if ($updated)
                        Url::redirect("events/manage-tw/" . $eventID);
                } else {
                    $error[] = __("event_chapters_error");
                }
            }
        } else {
            $error[] = __("empty_or_not_permitted_event_error");
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;

        return View::make('Events/ManageTw')
            ->shares("title", __("manage_event"))
            ->shares("event", $event)
            ->shares("members", $members)
            ->shares("chapters", $tmpChapters)
            ->shares("data", $data)
            ->shares("error", @$error);
    }

    public function manageL2($eventID)
    {
        $event = $this->eventRepo->get($eventID);

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;

        if ($event) {
            if (!$this->isAdmin($event)) {
                Url::redirect("events");
            }

            if ($event->state != EventStates::L2_RECRUIT &&
                $event->state != EventStates::L2_CHECK &&
                $event->state != EventStates::L2_CHECKED) {
                Url::redirect("events");
            }

            $tmpChapters = [];
            for ($i = 1; $i <= $event->bookInfo->chaptersNum; $i++) {
                $tmpChapters[$i] = [];
            }

            $chapters = $this->_model->getChapters($event->eventID, null, null, "l2");

            foreach ($chapters as $chapter) {
                if ($chapter["l2memberID"] == 0) continue;

                $tmp["l2chID"] = $chapter["l2chID"];
                $tmp["l2memberID"] = $chapter["l2memberID"];
                $tmp["chunks"] = json_decode($chapter["chunks"], true);
                $tmp["l2checked"] = $chapter["l2checked"];
                $tmp["sndCheck"] = (array)json_decode($chapter["sndCheck"], true);
                $tmp["peer1Check"] = (array)json_decode($chapter["peer1Check"], true);
                $tmp["peer2Check"] = (array)json_decode($chapter["peer2Check"], true);

                $tmpChapters[$chapter["chapter"]] = $tmp;
            }

            $members = $event->checkersL2;

            if (isset($_POST) && !empty($_POST)) {
                if (!empty(array_filter($tmpChapters))) {
                    $updated = $this->_model->updateEvent(
                        array(
                            "state" => EventStates::L2_CHECK,
                            /*"dateFrom" => date("Y-m-d H:i:s", time())*/),
                        array("eventID" => $eventID));
                    if ($updated)
                        Url::redirect("events/manage-l2/" . $eventID);
                } else {
                    $error[] = __("event_chapters_error");
                }
            }
        } else {
            $error[] = __("empty_or_not_permitted_event_error");
        }

        return View::make('Events/ManageL2')
            ->shares("title", __("manage_event"))
            ->shares("data", $data)
            ->shares("event", $event)
            ->shares("chapters", $tmpChapters)
            ->shares("members", $members)
            ->shares("error", @$error);
    }

    public function manageL3($eventID)
    {
        $event = $this->eventRepo->get($eventID);

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;

        if ($event) {
            if (!$this->isAdmin($event)) {
                Url::redirect("events");
            }

            if ($event->state != EventStates::L3_RECRUIT &&
                $event->state != EventStates::L3_CHECK &&
                $event->state != EventStates::COMPLETE) {
                Url::redirect("events");
            }

            $tmpChapters = [];
            if ($event->project->bookProject == "tn")
                $tmpChapters[0] = [];

            for ($i = 1; $i <= $event->bookInfo->chaptersNum; $i++) {
                $tmpChapters[$i] = [];
            }

            $chapters = $this->_model->getChapters($event->eventID, null, null, "l3");

            foreach ($chapters as $chapter) {
                if ($chapter["l3memberID"] == 0) continue;

                $tmp["l3chID"] = $chapter["l3chID"];
                $tmp["l3memberID"] = $chapter["l3memberID"];
                $tmp["chunks"] = json_decode($chapter["chunks"], true);
                $tmp["l3checked"] = $chapter["l3checked"];
                $tmp["peerCheck"] = (array)json_decode($chapter["peerCheck"], true);

                $tmpChapters[$chapter["chapter"]] = $tmp;
            }

            $members = $event->checkersL3;

            if (isset($_POST) && !empty($_POST)) {
                if (!empty(array_filter($tmpChapters))) {
                    $updated = $this->_model->updateEvent(
                        array(
                            "state" => EventStates::L3_CHECK,
                            /*"dateFrom" => date("Y-m-d H:i:s", time())*/),
                        array("eventID" => $eventID));
                    if ($updated)
                        Url::redirect("events/manage-l3/" . $eventID);
                } else {
                    $error[] = __("event_chapters_error");
                }
            }
        } else {
            $error[] = __("empty_or_not_permitted_event_error");
        }

        return View::make('Events/ManageL3')
            ->shares("title", __("manage_event"))
            ->shares("data", $data)
            ->shares("event", $event)
            ->shares("chapters", $tmpChapters)
            ->shares("members", $members)
            ->shares("error", @$error);
    }

    public function moveStepBack()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && $_POST["eventID"] != "" ? (integer)$_POST["eventID"] : null;
        $memberID = isset($_POST["memberID"]) && $_POST["memberID"] != "" ? (integer)$_POST["memberID"] : null;
        $to_step = isset($_POST["to_step"]) && $_POST["to_step"] != "" ? $_POST["to_step"] : null;
        $prev_chunk = isset($_POST["prev_chunk"]) && $_POST["prev_chunk"] != "" ? filter_var($_POST["prev_chunk"], FILTER_VALIDATE_BOOLEAN) : false;
        $confirm = isset($_POST["confirm"]) && $_POST["confirm"] != "" ? filter_var($_POST["confirm"], FILTER_VALIDATE_BOOLEAN) : false;
        $manageMode = isset($_POST["manageMode"]) && $_POST["manageMode"] != "" ? $_POST["manageMode"] : "l1";

        if ($eventID !== null && $memberID !== null && $to_step !== null) {

            $event = $this->eventRepo->get($eventID);
            $actionMember = $this->memberRepo->get($memberID);

            if ($event) {
                if (!$this->isAdmin($event)) {
                    $response["error"] = __("not_enough_rights_error");
                    echo json_encode($response);
                    return;
                }

                if ($manageMode == "l2") {
                    $member = $actionMember->checkersL2->where("eventID", $eventID, false)->first();
                    $finishedState = EventStates::L2_CHECKED;
                } elseif ($manageMode == "l3") {
                    $member = $actionMember->checkersL3->where("eventID", $eventID, false)->first();
                    $finishedState = EventStates::COMPLETE;
                } else {
                    $member = $actionMember->translators->where("eventID", $eventID, false)->first();
                    $finishedState = EventStates::TRANSLATED;
                }

                if ($member) {
                    if (EventStates::enum($event->state) < EventStates::enum($finishedState)) {
                        $mode = $manageMode == "l1" && $event->langInput
                            ? "li"
                            : ($event->project->sourceBible == "odb" ? "odb" : "") . $event->project->bookProject;

                        if (array_key_exists($to_step, EventSteps::enumArray($mode))
                            || array_key_exists($to_step, EventCheckSteps::enumArray("l2"))
                            || array_key_exists($to_step, EventCheckSteps::enumArray("l3"))) {

                            $postData = $this->moveMemberStepBack(
                                $member,
                                $to_step,
                                $confirm,
                                $prev_chunk
                            );

                            if (!empty($postData)) {
                                if (in_array("hasTranslation", $postData, true)) {
                                    $response["confirm"] = true;
                                    $response["message"] = __("chapter_has_translation");
                                    echo json_encode($response);
                                    return;
                                }

                                if (array_key_exists("translations", $postData) && $manageMode == "l1") {
                                    unset($postData["translations"]);
                                    $this->_translationModel->deleteTranslation(["trID" => $member->trID, "chapter" => $member->currentChapter]);
                                    $this->_translationModel->deleteCommentsByEvent($eventID, $member->currentChapter);
                                }

                                $member->update($postData);

                                $response["success"] = true;
                                $response["message"] = $member->step != $to_step
                                    ? __("moved_back_success")
                                    : __("checker_removed_success");
                            } else {
                                $response["error"] = __("not_allowed_action");
                            }
                        } else {
                            $response["error"] = __("wrong_parameters");
                        }
                    } else {
                        $response["error"] = __("event_is_finished");
                    }
                } else {
                    $response["error"] = __("wrong_parameters");
                }
            } else {
                $response["error"] = __("wrong_parameters");
            }
        }

        echo json_encode($response);
    }

    private function moveMemberStepBack($member, $toStep, $confirm, $prevChunk = false)
    {
        $manageMode = "l1";
        if ($member->event->state == EventStates::L2_CHECK)
            $manageMode = "l2";
        elseif ($member->event->state == EventStates::L3_CHECK)
            $manageMode = "l3";

        $mode = $manageMode == "l1" && $member->event->langInput
            ? "li"
            : ($member->event->project->sourceBible == "odb" ? "odb" : "") . $member->event->project->bookProject;

        $postData = [];

        // Level 2
        if ($manageMode == "l2") {
            // do not allow move from "none" and "preparation" steps
            if (EventCheckSteps::enum($member->step, $manageMode) < 2)
                return [];

            // Do not allow to move back more than one step at a time
            if ((EventCheckSteps::enum($member->step, $manageMode) - EventCheckSteps::enum($toStep, $manageMode)) > 1)
                return [];

            switch ($toStep) {
                case EventCheckSteps::PRAY:
                    $postData["step"] = EventSteps::PRAY;
                    break;

                case EventCheckSteps::CONSUME:
                    $postData["step"] = EventSteps::CONSUME;
                    break;
            }

            return $postData;
        } elseif ($manageMode == "l3") {
            // do not allow move from "none" and "preparation" steps
            if (EventCheckSteps::enum($member->step, $manageMode) < 2)
                return [];

            // Do not allow to move back more than one step at a time
            if ((EventCheckSteps::enum($member->step, $manageMode) - EventCheckSteps::enum($toStep, $manageMode)) > 1)
                return [];

            switch ($toStep) {
                case EventCheckSteps::PRAY:
                    $postData["step"] = EventSteps::PRAY;

                    $peerCheck = (array)json_decode($member->peerCheck, true);
                    if (array_key_exists($member->currentChapter, $peerCheck))
                        unset($peerCheck[$member->currentChapter]);

                    $postData["peerCheck"] = json_encode($peerCheck);
                    break;

                case EventCheckSteps::PEER_REVIEW_L3:
                    $postData["step"] = EventCheckSteps::PEER_REVIEW_L3;

                    $peerCheck = (array)json_decode($member->peerCheck, true);
                    if (array_key_exists($member->currentChapter, $peerCheck))
                        $peerCheck[$member->currentChapter]["done"] = 0;

                    $postData["peerCheck"] = json_encode($peerCheck);
                    break;
            }

            return $postData;
        }

        // Level 1
        // do not allow to move from "none" and "preparation" steps
        if (EventSteps::enum($member->step, $mode) < 2)
            return [];

        // Do not allow to move back more than one step at a time
        if ((EventSteps::enum($member->step, $mode) - EventSteps::enum($toStep, $mode)) > 1)
            return [];

        // Do not allow to move forward, exception from READ_CHUNK to BLIND_DRAFT of previous chunk
        if (EventSteps::enum($toStep, $mode) >= EventSteps::enum($member->step, $mode)
            && ($toStep == EventSteps::BLIND_DRAFT && !$prevChunk))
            return [];

        switch ($toStep) {
            case EventSteps::PRAY:
                $postData["step"] = EventSteps::PRAY;
                break;

            case EventSteps::CONSUME:
                $postData["step"] = EventSteps::CONSUME;

                if (!in_array($mode, ["tn"])) {
                    if ($mode == "odbsun") {
                        $postData["currentChunk"] = 0;
                    } else {
                        $verbCheck = (array)json_decode($member->verbCheck, true);
                        if (array_key_exists($member->currentChapter, $verbCheck))
                            unset($verbCheck[$member->currentChapter]);
                        $postData["verbCheck"] = json_encode($verbCheck);
                    }
                } else {
                    $trans = $this->_translationModel->getEventTranslation($member->trID, $member->currentChapter);

                    if (!empty($trans) && !$confirm)
                        return ["hasTranslation"];

                    $this->_model->updateChapter(["chunks" => "[]"], [
                        "eventID" => $member->eventID,
                        "chapter" => $member->currentChapter]);

                    $verbCheck = (array)json_decode($member->verbCheck, true);
                    if (array_key_exists($member->currentChapter, $verbCheck)) {
                        unset($verbCheck[$member->currentChapter]);
                    }
                    $postData["verbCheck"] = json_encode($verbCheck);
                    $postData["step"] = EventSteps::CONSUME;
                    $postData["currentChunk"] = 0;
                    $postData["translations"] = true;
                }
                break;

            case EventSteps::VERBALIZE:
                $postData["step"] = EventSteps::VERBALIZE;

                $verbCheck = (array)json_decode($member->verbCheck, true);
                if (array_key_exists($member->currentChapter, $verbCheck)) {
                    unset($verbCheck[$member->currentChapter]);
                }
                $postData["verbCheck"] = json_encode($verbCheck);
                break;

            case EventSteps::CHUNKING:
                $trans = $this->_translationModel->getEventTranslation($member->trID, $member->currentChapter);

                if (!empty($trans) && !$confirm)
                    return ["hasTranslation"];

                $this->_model->updateChapter(["chunks" => "[]"], [
                    "eventID" => $member->eventID,
                    "chapter" => $member->currentChapter]);

                $postData["step"] = EventSteps::CHUNKING;
                $postData["currentChunk"] = 0;
                $postData["translations"] = true;
                break;

            case EventSteps::READ_CHUNK:
                $postData["step"] = EventSteps::READ_CHUNK;
                break;

            case EventSteps::BLIND_DRAFT:
                $postData["step"] = EventSteps::BLIND_DRAFT;
                if ($prevChunk) {
                    $chunk = $member->currentChunk - 1;
                    $postData["currentChunk"] = max(0, $chunk);
                }
                break;

            case EventSteps::REARRANGE:
                $postData["step"] = EventSteps::REARRANGE;
                $postData["currentChunk"] = 0;
                if ($prevChunk) {
                    $chapter = $member->chapters->where("chapter", $member->currentChapter, false)->first();
                    if ($chapter) {
                        $chunks = (array)json_decode($chapter->chunks, true);
                        if ($member->step == EventSteps::SYMBOL_DRAFT && $member->currentChunk == 0)
                            $chunk = sizeof($chunks) - 1;
                        else
                            $chunk = $member->currentChunk - 1;

                        $postData["currentChunk"] = max(0, $chunk);
                    }
                }
                break;

            case EventSteps::SYMBOL_DRAFT:
                $postData["step"] = EventSteps::SYMBOL_DRAFT;
                $postData["currentChunk"] = 0;
                if ($prevChunk) {
                    $chapter = $member->chapters->where("chapter", $member->currentChapter, false)->first();
                    if ($chapter) {
                        $chunks = (array)json_decode($chapter->chunks, true);
                        if ($member->step == EventSteps::SELF_CHECK)
                            $chunk = sizeof($chunks) - 1;
                        else
                            $chunk = $member->currentChunk - 1;
                        $postData["currentChunk"] = max(0, $chunk);
                    }
                }
                break;

            case EventSteps::SELF_CHECK:
                $postData["step"] = EventSteps::SELF_CHECK;

                $peerCheck = (array)json_decode($member->peerCheck, true);
                if (array_key_exists($member->currentChapter, $peerCheck))
                    unset($peerCheck[$member->currentChapter]);
                $postData["peerCheck"] = json_encode($peerCheck);
                break;

            case EventSteps::MULTI_DRAFT:
                $postData["step"] = EventSteps::MULTI_DRAFT;
                break;
        }

        return $postData;
    }

    public function moveStepBackAlt()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && $_POST["eventID"] != "" ? (integer)$_POST["eventID"] : null;
        $memberID = isset($_POST["memberID"]) && $_POST["memberID"] != "" ? (integer)$_POST["memberID"] : null;
        $chapter = isset($_POST["chapter"]) && $_POST["chapter"] != "" ? (integer)$_POST["chapter"] : null;
        $mode = isset($_POST["mode"]) && $_POST["mode"] != "" ? $_POST["mode"] : "snd_checker";
        $otherChk = isset($_POST["otherChk"]) && $_POST["otherChk"] != "" ? $_POST["otherChk"] : "";

        if ($eventID !== null && $memberID !== null && $chapter !== null) {
            $event = $this->eventRepo->get($eventID);

            if ($event) {
                if (!$this->isAdmin($event)) {
                    $response["error"] = __("not_enough_rights_error");
                    echo json_encode($response);
                    return;
                }

                if (EventStates::enum($event->state) == EventStates::enum(EventStates::L2_CHECK)) {
                    $chapters = $this->_model->getChapters($eventID, $memberID, $chapter, "l2");
                    $chap = [];

                    if (!empty($chapters)) {
                        $chap["l2chID"] = $chapters[0]["l2chID"];
                        $chap["l2memberID"] = $chapters[0]["l2memberID"];
                        $chap["chunks"] = json_decode($chapters[0]["chunks"], true);
                        $chap["l2checked"] = $chapters[0]["l2checked"];
                        $chap["sndCheck"] = (array)json_decode($chapters[0]["sndCheck"], true);
                        $chap["peer1Check"] = (array)json_decode($chapters[0]["peer1Check"], true);
                        $chap["peer2Check"] = (array)json_decode($chapters[0]["peer2Check"], true);

                        $p1 = !empty($chap["peer1Check"])
                            && array_key_exists($chapter, $chap["peer1Check"])
                            && $chap["peer1Check"][$chapter]["memberID"] > 0;
                        $p2 = !empty($chap["peer2Check"])
                            && array_key_exists($chapter, $chap["peer2Check"])
                            && $chap["peer2Check"][$chapter]["memberID"] > 0;

                        switch ($mode) {
                            case "snd_checker":
                                if (!$p1) {
                                    $chap["sndCheck"][$chapter]["memberID"] = 0;
                                    $chap["sndCheck"][$chapter]["done"] = 0;
                                    unset($chap["peer1Check"][$chapter]);
                                    unset($chap["peer2Check"][$chapter]);
                                } else {
                                    $response["error"] = __("wrong_parameters");
                                }
                                break;

                            case "p1_checker":
                                if (!$p2) {
                                    $chap["peer1Check"][$chapter]["memberID"] = 0;
                                    $chap["peer1Check"][$chapter]["done"] = 0;
                                    $chap["peer2Check"][$chapter]["memberID"] = 0;
                                    $chap["peer2Check"][$chapter]["done"] = 0;
                                } else {
                                    $response["error"] = __("wrong_parameters");
                                }
                                break;

                            case "p2_checker":
                                if ($p2) {
                                    $chap["peer1Check"][$chapter]["done"] = 0;
                                    $chap["peer2Check"][$chapter]["memberID"] = 0;
                                    $chap["peer2Check"][$chapter]["done"] = 0;
                                } else {
                                    $response["error"] = __("wrong_parameters");
                                }
                                break;

                            default:
                                $response["error"] = __("wrong_parameters");
                                break;
                        }

                        if (!isset($response["error"])) {
                            $postData = [
                                "sndCheck" => json_encode($chap["sndCheck"]),
                                "peer1Check" => json_encode($chap["peer1Check"]),
                                "peer2Check" => json_encode($chap["peer2Check"]),
                            ];

                            $event->checkersL2()->updateExistingPivot($memberID, $postData);

                            $response["message"] = __("checker_removed_success");
                            $response["success"] = true;
                        }
                    } else {
                        $response["error"] = __("wrong_parameters");
                    }
                } elseif (EventStates::enum($event->state) == EventStates::enum(EventStates::L3_CHECK)) {
                    $chapters = $this->_model->getChapters($eventID, $memberID, $chapter, "l3");
                    $chap = [];

                    if (!empty($chapters)) {
                        $chap["l3chID"] = $chapters[0]["l3chID"];
                        $chap["l3memberID"] = $chapters[0]["l3memberID"];
                        $chap["chunks"] = json_decode($chapters[0]["chunks"], true);
                        $chap["l3checked"] = $chapters[0]["checked"];
                        $chap["peerCheck"] = (array)json_decode($chapters[0]["peerCheck"], true);

                        $peer = !empty($chap["peerCheck"])
                            && array_key_exists($chapter, $chap["peerCheck"])
                            && $chap["peerCheck"][$chapter]["memberID"] > 0;

                        if ($peer) {
                            $chap["peerCheck"][$chapter]["memberID"] = 0;
                            $chap["peerCheck"][$chapter]["done"] = 0;
                            // Set first checker back to PEER_REVIEW_L3 step
                            $chap["step"] = EventCheckSteps::PEER_REVIEW_L3;
                            $chap["currentChapter"] = $chapter;

                            $response["message"] = __("checker_removed_success");
                        } else {
                            $response["error"] = __("wrong_parameters");
                        }

                        if (!isset($response["error"])) {
                            $postData = [
                                "step" => $chap["step"],
                                "currentChapter" => $chap["currentChapter"],
                                "peerCheck" => json_encode($chap["peerCheck"]),
                            ];

                            $event->checkersL3()->updateExistingPivot($memberID, $postData);

                            $response["success"] = true;
                        }
                    } else {
                        $response["error"] = __("wrong_parameters");
                    }
                } elseif ($event->project->bookProject == "sun") {
                    if (EventStates::enum($event->state) == EventStates::enum(EventStates::TRANSLATING)) {
                        $chapters = $this->_model->getChapters($eventID, $memberID, $chapter, "sun");
                        $chap = [];

                        if (!empty($chapters)) {
                            $chap["trID"] = $chapters[0]["trID"];
                            $chap["memberID"] = $chapters[0]["memberID"];
                            $chap["chunks"] = json_decode($chapters[0]["chunks"], true);
                            $chap["checked"] = $chapters[0]["checked"];
                            $chap["kwCheck"] = (array)json_decode($chapters[0]["kwCheck"], true);
                            $chap["crCheck"] = (array)json_decode($chapters[0]["crCheck"], true);

                            $cr = !empty($chap["crCheck"])
                                && array_key_exists($chapter, $chap["crCheck"])
                                && $chap["crCheck"][$chapter]["memberID"] > 0;

                            switch ($mode) {
                                case "kw_checker":
                                    if (!$cr) {
                                        $chap["kwCheck"][$chapter]["memberID"] = 0;
                                        $chap["kwCheck"][$chapter]["done"] = 0;
                                        unset($chap["crCheck"][$chapter]);
                                    } else {
                                        $response["error"] = __("wrong_parameters");
                                    }
                                    break;

                                case "cr_checker":
                                    if ($cr) {
                                        $chap["crCheck"][$chapter]["memberID"] = 0;
                                        $chap["crCheck"][$chapter]["done"] = 0;
                                    } else {
                                        $response["error"] = __("wrong_parameters");
                                    }
                                    break;

                                default:
                                    $response["error"] = __("wrong_parameters");
                                    break;
                            }

                            if (!isset($response["error"])) {
                                $postData = [
                                    "kwCheck" => json_encode($chap["kwCheck"]),
                                    "crCheck" => json_encode($chap["crCheck"]),
                                ];

                                $event->translators()->updateExistingPivot($memberID, $postData);

                                $response["message"] = __("checker_removed_success");
                                $response["success"] = true;
                            }
                        } else {
                            $response["error"] = __("wrong_parameters");
                        }
                    } else {
                        $response["error"] = __("event_is_finished");
                    }
                } else if ($event->project->bookProject == "rad") {
                    if (EventStates::enum($event->state) == EventStates::enum(EventStates::TRANSLATING)) {
                        $chapters = $this->_model->getChapters($eventID, $memberID, $chapter, "rad");
                        $chap = [];

                        if (!empty($chapters)) {
                            $chap["trID"] = $chapters[0]["trID"];
                            $chap["memberID"] = $chapters[0]["memberID"];
                            $chap["chunks"] = json_decode($chapters[0]["chunks"], true);
                            $chap["checked"] = $chapters[0]["checked"];
                            $chap["peerCheck"] = (array)json_decode($chapters[0]["peerCheck"], true);

                            switch ($mode) {
                                case "peer_checker":
                                    $chap["peerCheck"][$chapter]["memberID"] = 0;
                                    $chap["peerCheck"][$chapter]["done"] = 0;
                                    break;

                                default:
                                    $response["error"] = __("wrong_parameters");
                                    break;
                            }

                            if (!isset($response["error"])) {
                                $postData = [
                                    "peerCheck" => json_encode($chap["peerCheck"])
                                ];

                                $event->translators()->updateExistingPivot($memberID, $postData);

                                $response["message"] = __("checker_removed_success");
                                $response["success"] = true;
                            }
                        } else {
                            $response["error"] = __("wrong_parameters");
                        }
                    } else {
                        $response["error"] = __("event_is_finished");
                    }
                } elseif (in_array($event->project->bookProject, ["tn", "tq", "tw", "obs"])) {
                    if (EventStates::enum($event->state) == EventStates::enum(EventStates::TRANSLATING)) {
                        $chapters = $this->_model->getChapters($eventID, $memberID, $chapter, "other");
                        $chap = [];

                        if (!empty($chapters)) {
                            $chap["trID"] = $chapters[0]["trID"];
                            $chap["memberID"] = $chapters[0]["memberID"];
                            $chap["chunks"] = json_decode($chapters[0]["chunks"], true);
                            $chap["checked"] = $chapters[0]["checked"];
                            $chap["otherCheck"] = (array)json_decode($chapters[0]["otherCheck"], true);
                            $chap["peerCheck"] = (array)json_decode($chapters[0]["peerCheck"], true);

                            $peer = !empty($chap["peerCheck"])
                                && array_key_exists($chapter, $chap["peerCheck"])
                                && $chap["peerCheck"][$chapter]["memberID"] > 0;

                            switch ($mode) {
                                case "other_checker":
                                    if (!$peer) {
                                        if ($otherChk == "remove") {
                                            $chap["otherCheck"][$chapter]["memberID"] = 0;
                                            $chap["otherCheck"][$chapter]["done"] = 0;
                                            unset($chap["peerCheck"][$chapter]);

                                            $response["message"] = __("checker_removed_success");
                                        } else {
                                            $chap["otherCheck"][$chapter]["done"] -= 1;
                                            unset($chap["peerCheck"][$chapter]);

                                            $response["message"] = __("moved_back_success");

                                            if ($chap["otherCheck"][$chapter]["done"] < 0) {
                                                $response["error"] = __("wrong_parameters");
                                            }
                                        }
                                    } else {
                                        $response["error"] = __("wrong_parameters");
                                    }
                                    break;

                                case "peer_checker":
                                    if ($peer) {
                                        $chap["peerCheck"][$chapter]["memberID"] = 0;
                                        $chap["peerCheck"][$chapter]["done"] = 0;
                                        // Set first checker back to PEER_REVIEW step
                                        if ($event->project->bookProject == "tn")
                                            $chap["otherCheck"][$chapter]["done"] = 5;
                                        else
                                            $chap["otherCheck"][$chapter]["done"] = 2;

                                        $response["message"] = __("checker_removed_success");
                                    } else {
                                        $response["error"] = __("wrong_parameters");
                                    }
                                    break;

                                default:
                                    $response["error"] = __("wrong_parameters");
                                    break;
                            }

                            if (!isset($response["error"])) {
                                $postData = [
                                    "otherCheck" => json_encode($chap["otherCheck"]),
                                    "peerCheck" => json_encode($chap["peerCheck"]),
                                ];

                                $event->translators()->updateExistingPivot($memberID, $postData);

                                $response["success"] = true;
                            }
                        } else {
                            $response["error"] = __("wrong_parameters");
                        }
                    } else {
                        $response["error"] = __("event_is_finished");
                    }
                } elseif (in_array($event->project->bookProject, ["ulb", "udb"])) {
                    if (EventStates::enum($event->state) == EventStates::enum(EventStates::TRANSLATING)) {
                        $chapters = $this->_model->getChapters($eventID, $memberID, $chapter);
                        $chap = [];

                        if (!empty($chapters)) {
                            $chap["trID"] = $chapters[0]["trID"];
                            $chap["memberID"] = $chapters[0]["memberID"];
                            $chap["chunks"] = json_decode($chapters[0]["chunks"], true);
                            $chap["checked"] = $chapters[0]["checked"];
                            $chap["verbCheck"] = (array)json_decode($chapters[0]["verbCheck"], true);
                            $chap["peerCheck"] = (array)json_decode($chapters[0]["peerCheck"], true);
                            $chap["kwCheck"] = (array)json_decode($chapters[0]["kwCheck"], true);
                            $chap["crCheck"] = (array)json_decode($chapters[0]["crCheck"], true);
                            $chap["otherCheck"] = (array)json_decode($chapters[0]["otherCheck"], true);

                            $peer = !empty($chap["peerCheck"])
                                && array_key_exists($chapter, $chap["peerCheck"])
                                && $chap["peerCheck"][$chapter]["memberID"] > 0;
                            $kw = !empty($chap["kwCheck"])
                                && array_key_exists($chapter, $chap["kwCheck"])
                                && $chap["kwCheck"][$chapter]["memberID"] > 0;
                            $cr = !empty($chap["crCheck"])
                                && array_key_exists($chapter, $chap["crCheck"])
                                && $chap["crCheck"][$chapter]["memberID"] > 0;
                            $final = !empty($chap["otherCheck"])
                                && array_key_exists($chapter, $chap["otherCheck"]);

                            switch ($mode) {
                                case "verb_checker":
                                    if (!$peer) {
                                        unset($chap["verbCheck"][$chapter]);
                                    } else {
                                        $response["error"] = __("wrong_parameters");
                                    }
                                    break;

                                case "peer_checker":
                                    if (!$kw) {
                                        $chap["peerCheck"][$chapter]["memberID"] = 0;
                                        $chap["peerCheck"][$chapter]["done"] = 0;
                                        unset($chap["kwCheck"][$chapter]);
                                    } else {
                                        $response["error"] = __("wrong_parameters");
                                    }
                                    break;

                                case "kw_checker":
                                    if (!$cr) {
                                        $chap["kwCheck"][$chapter]["memberID"] = 0;
                                        $chap["kwCheck"][$chapter]["done"] = 0;
                                        unset($chap["crCheck"][$chapter]);
                                    } else {
                                        $response["error"] = __("wrong_parameters");
                                    }
                                    break;

                                case "cr_checker":
                                    if (!$final) {
                                        $chap["crCheck"][$chapter]["memberID"] = 0;
                                        $chap["crCheck"][$chapter]["done"] = 0;
                                        unset($chap["otherCheck"][$chapter]);
                                    } else {
                                        $response["error"] = __("wrong_parameters");
                                    }
                                    break;

                                case "final_checker":
                                    if ($final) {
                                        $chap["crCheck"][$chapter]["memberID"] = 0;
                                        $chap["crCheck"][$chapter]["done"] = 0;
                                        unset($chap["otherCheck"][$chapter]);
                                    } else {
                                        $response["error"] = __("wrong_parameters");
                                    }
                                    break;

                                default:
                                    $response["error"] = __("wrong_parameters");
                                    break;
                            }

                            if (!isset($response["error"])) {
                                $postData = [
                                    "verbCheck" => json_encode($chap["verbCheck"]),
                                    "peerCheck" => json_encode($chap["peerCheck"]),
                                    "kwCheck" => json_encode($chap["kwCheck"]),
                                    "crCheck" => json_encode($chap["crCheck"]),
                                    "otherCheck" => json_encode($chap["otherCheck"]),
                                ];

                                $event->translators()->updateExistingPivot($memberID, $postData);

                                $response["message"] = __("checker_removed_success");
                                $response["success"] = true;
                            }
                        } else {
                            $response["error"] = __("wrong_parameters");
                        }
                    } else {
                        $response["error"] = __("event_is_finished");
                    }
                } else {
                    $response["error"] = __("wrong_parameters");
                }
            } else {
                $response["error"] = __("wrong_parameters");
            }
        }

        echo json_encode($response);
    }

    public function setOtherChecker()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && $_POST["eventID"] != "" ? (integer)$_POST["eventID"] : null;
        $memberID = isset($_POST["memberID"]) && $_POST["memberID"] != "" ? (integer)$_POST["memberID"] : null;

        if ($eventID !== null && $memberID !== null) {
            $event = $this->eventRepo->get($eventID);

            if ($event) {
                if (!$this->isAdmin($event)) {
                    $response["error"] = __("not_enough_rights_error");
                    echo json_encode($response);
                    return;
                }
            }

            $translator = $event->translators->find($memberID);

            if ($translator) {
                $event->translators()->updateExistingPivot($memberID, ["isChecker" => !$translator->pivot->isChecker]);
                $response["success"] = true;
            } else {
                $response["error"] = __("wrong_parameters");
            }
        }

        echo json_encode($response);
    }

    /**
     * Add or remove chapter user translating
     */
    public function assignChapter()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && $_POST["eventID"] != "" ? (integer)$_POST["eventID"] : null;
        $chapter = isset($_POST["chapter"]) && $_POST["chapter"] != "" ? (integer)$_POST["chapter"] : null;
        $memberID = isset($_POST["memberID"]) && $_POST["memberID"] != "" ? (integer)$_POST["memberID"] : null;
        $action = isset($_POST["action"]) && preg_match("/^(add|delete)$/", $_POST["action"]) ? $_POST["action"] : null;
        $manageMode = isset($_POST["manageMode"]) && $_POST["manageMode"] != "" ? $_POST["manageMode"] : "l1";

        if ($eventID !== null && $chapter !== null && $memberID !== null && $action !== null) {
            $event = $this->eventRepo->get($eventID);

            if ($event) {
                if ($this->isAdmin($event)) {
                    $mode = $event->project->bookProject;

                    $data["chapters"] = [];
                    if ($event->project->bookProject == "tn")
                        $data["chapters"][0] = [];

                    if ($mode == "tw") {
                        $tw_groups = $this->_model->getTwGroups(["eventID" => $event->eventID]);

                        foreach ($tw_groups as $group) {
                            $data["chapters"][$group->groupID] = [];
                        }
                    } else {
                        for ($i = 1; $i <= $event->bookInfo->chaptersNum; $i++) {
                            $data["chapters"][$i] = [];
                        }
                    }

                    $chapters = $this->_model->getChapters($event->eventID);

                    foreach ($chapters as $chap) {
                        $tmp["trID"] = $chap["trID"];
                        $tmp["memberID"] = $chap["memberID"];
                        $tmp["chunks"] = json_decode($chap["chunks"], true);
                        $tmp["done"] = $chap["done"];
                        $tmp["l2memberID"] = $chap["l2memberID"];
                        $tmp["l2chID"] = $chap["l2chID"];
                        $tmp["l2checked"] = $chap["l2checked"];
                        $tmp["l3memberID"] = $chap["l3memberID"];
                        $tmp["l3chID"] = $chap["l3chID"];
                        $tmp["l3checked"] = $chap["l3checked"];

                        $data["chapters"][$chap["chapter"]] = $tmp;
                    }

                    if (isset($data["chapters"][$chapter]) && empty($data["chapters"][$chapter])) {
                        if ($action == "add") {
                            if ($manageMode == "l2" || $manageMode == "l3") {
                                $response["error"] = __("error_ocured", ["This chapter hasn't been translated."]);
                                echo json_encode($response);
                                exit;
                            }

                            $translator = $event->translators->find($memberID);
                            $postdata = [
                                "trID" => $translator->pivot->trID,
                                "chapter" => $chapter,
                                "chunks" => "[]",
                                "done" => false
                            ];

                            $event->translatorsWithChapters()->attach($translator, $postdata);

                            $data["chapters"][$chapter] = $postdata;

                            $newChapters = $translator->chapters->filter(function($chapter) use($eventID) {
                                return $chapter->eventID == $eventID && !$chapter->done;
                            });

                            // Change translator's step to pray when at least one chapter is assigned to him or all chapters finished
                            if ($newChapters->count() > 0 || $translator->pivot->step == EventSteps::FINISHED) {
                                $event->translators()->updateExistingPivot($memberID, ["step" => EventSteps::PRAY]);
                            }

                            $response["success"] = true;
                        } else {
                            $response["error"] = __("error_ocured", ["wrong parameters"]);
                        }
                    } else {
                        if ($action == "delete") {
                            $translations = $this->_translationModel->getTranslationByEventID($eventID, $chapter);
                            // Check if chapter has translations
                            $hasTranslations = !empty($translations);

                            // Check if chapter has L2 translations
                            if ($manageMode == "l2") {
                                $trVerses = (array)json_decode($translations[0]->translatedVerses);
                                $l2Verses = $trVerses[EventMembers::L2_CHECKER];

                                $hasTranslations = !empty($l2Verses->verses);
                            }
                            // Check if chapter has L3 translations
                            if ($manageMode == "l3") {
                                $trVerses = (array)json_decode($translations[0]->translatedVerses);
                                $l3Verses = $trVerses[EventMembers::L3_CHECKER];

                                $hasTranslations = !empty($l3Verses->verses);
                            }

                            if (!$hasTranslations) {
                                if ($manageMode == "l1") {
                                    if ($data["chapters"][$chapter]["memberID"] == $memberID) {

                                        // Detaching removes all the chapters of the user
                                        // To fix, tables should be refactored
                                        //$event->translatorsWithChapters()->detach($translator, ["chapter" => $chapter]);

                                        $this->_model->removeChapter([
                                            "eventID" => $eventID,
                                            "memberID" => $memberID,
                                            "chapter" => $chapter]);
                                        $data["chapters"][$chapter] = [];

                                        $trPostData = [];

                                        $translator = $event->translators->find($memberID);
                                        $newChapters = $translator->chapters->filter(function($chapter) use($eventID) {
                                            return $chapter->eventID == $eventID && !$chapter->done;
                                        });

                                        // Clear translator data to default if current chapter was removed
                                        // Change translator's step to NONE when no chapter is assigned to him
                                        if ($translator->pivot->currentChapter == $chapter || $newChapters->count() == 0) {
                                            $trPostData["step"] = $newChapters->count() == 0 ? EventSteps::NONE : EventSteps::PRAY;
                                            $trPostData["currentChapter"] = in_array($mode, ["tn"]) ? -1 : 0;
                                            $trPostData["currentChunk"] = 0;

                                            $verbCheck = (array)json_decode($translator->pivot->verbCheck, true);
                                            if (array_key_exists($chapter, $verbCheck)) {
                                                unset($verbCheck[$chapter]);
                                                $trPostData["verbCheck"] = json_encode($verbCheck);
                                            }
                                        }

                                        if (!empty($trPostData)) {
                                            $event->translators()->updateExistingPivot($memberID, $trPostData);
                                        }

                                        $response["success"] = true;
                                    } else {
                                        $response["error"] = __("error_ocured", array("wrong parameters"));
                                    }
                                } else if ($manageMode == "l2") {
                                    if ($data["chapters"][$chapter]["l2memberID"] == $memberID) {
                                        $this->_model->updateChapter([
                                            "l2memberID" => 0,
                                            "l2chID" => 0
                                        ], [
                                            "eventID" => $eventID,
                                            "chapter" => $chapter
                                        ]);
                                        $data["chapters"][$chapter]["l2memberID"] = 0;
                                        $data["chapters"][$chapter]["l2chID"] = 0;

                                        $trPostData = [];

                                        $checker = $event->checkersL2->find($memberID);
                                        $newChapters = $checker->chaptersL2->filter(function($chapter) use($eventID) {
                                            return $chapter->eventID == $eventID && !$chapter->l2checked;
                                        });

                                        // Clear checker's data to default if current chapter was removed
                                        // Change checker's step to NONE when no chapter is assigned to him
                                        if ($checker->currentChapter == $chapter || $newChapters->count() == 0) {
                                            $trPostData["step"] = $newChapters->count() == 0 ? EventSteps::NONE : EventSteps::PRAY;
                                            $trPostData["currentChapter"] = 0;
                                        }

                                        if (!empty($trPostData)) {
                                            $event->checkersL2()->updateExistingPivot($memberID, $trPostData);
                                        }

                                        $response["success"] = true;
                                    } else {
                                        $response["error"] = __("error_ocured", array("wrong parameters"));
                                    }
                                } else if ($manageMode == "l3") {
                                    if ($data["chapters"][$chapter]["l3memberID"] == $memberID) {
                                        $this->_model->updateChapter([
                                            "l3memberID" => 0,
                                            "l3chID" => 0
                                        ], [
                                            "eventID" => $eventID,
                                            "chapter" => $chapter
                                        ]);
                                        $data["chapters"][$chapter]["l3memberID"] = 0;
                                        $data["chapters"][$chapter]["l3chID"] = 0;

                                        $trPostData = [];

                                        $checker = $event->checkersL3->find($memberID);
                                        $newChapters = $checker->chaptersL3->filter(function($chapter) use($eventID) {
                                            return $chapter->eventID == $eventID && !$chapter->l3checked;
                                        });

                                        // Clear checker's data to default if current chapter was removed
                                        // Change checker's step to NONE when no chapter is assigned to him
                                        if ($checker->currentChapter == $chapter || $newChapters->count() == 0) {
                                            $trPostData["step"] = $newChapters->count() == 0 ? EventCheckSteps::NONE : EventCheckSteps::PRAY;
                                            $trPostData["currentChapter"] = $event->project->bookProject == "tn" ? -1 : 0;
                                        }

                                        if (!empty($trPostData)) {
                                            $event->checkersL3()->updateExistingPivot($memberID, $trPostData);
                                        }

                                        $response["success"] = true;
                                    } else {
                                        $response["error"] = __("error_ocured", array("wrong parameters"));
                                    }
                                }
                            } else {
                                $response["error"] = __("event_translating_error");
                            }
                        } else if ($action == "add" && $manageMode == "l2") {
                            if ($data["chapters"][$chapter]["l2memberID"] == 0) {
                                $checker = $event->checkersL2->find($memberID);
                                $checkerL2 = $checker->checkersL2->where("eventID", $eventID, false)->first();

                                $postdata = [
                                    "l2chID" => $checkerL2->l2chID,
                                    "l2memberID" => $checker->memberID
                                ];

                                $this->_model->updateChapter($postdata, [
                                    "eventID" => $eventID,
                                    "chapter" => $chapter
                                ]);

                                $event->checkersL2()->updateExistingPivot($memberID, ["step" => EventCheckSteps::PRAY]);

                                $response["success"] = true;
                            } else {
                                $response["error"] = __("chapter_aready_assigned_error");
                            }
                        } else if ($action == "add" && $manageMode == "l3") {
                            if ($data["chapters"][$chapter]["l3memberID"] == 0) {
                                $checker = $event->checkersL3->find($memberID);
                                $checkerL3 = $checker->checkersL3->where("eventID", $eventID, false)->first();

                                $postdata = [
                                    "l3chID" => $checkerL3->l3chID,
                                    "l3memberID" => $checker->memberID
                                ];

                                $this->_model->updateChapter($postdata, [
                                    "eventID" => $eventID,
                                    "chapter" => $chapter
                                ]);

                                $event->checkersL3()->updateExistingPivot($memberID, ["step" => EventCheckSteps::PRAY]);

                                $response["success"] = true;
                            } else {
                                $response["error"] = __("chapter_aready_assigned_error");
                            }
                        } else {
                            $response["error"] = __("chapter_aready_assigned_error");
                        }
                    }
                } else {
                    $response["error"] = __("not_enough_rights_error");
                }
            } else {
                $response["error"] = __("error_ocured", array("wrong parameters"));
            }
        } else {
            $response["error"] = __("error_ocured", array("wrong parameters"));
        }

        echo json_encode($response);
    }

    public function addEventMember()
    {
        $data["errors"] = array();

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST['eventID']) && $_POST['eventID'] != "" ? $_POST['eventID'] : null;
        $userType = isset($_POST['userType']) && $_POST['userType'] != "" ? $_POST['userType'] : null;
        $memberID = isset($_POST['memberID']) && $_POST['memberID'] != "" ? $_POST['memberID'] : null;

        $appliedMember = $this->memberRepo->get($memberID);

        if (!$appliedMember) {
            $error[] = __('wrong_event_parameters');
            echo json_encode(array("error" => $error));
            return;
        }

        if ($eventID == null) {
            $error[] = __('wrong_event_parameters');
            echo json_encode(array("error" => $error));
            return;
        }

        if ($userType == null || !preg_match("/^(" . EventMembers::TRANSLATOR . "|" . EventMembers::L2_CHECKER . "|" . EventMembers::L3_CHECKER . ")$/", $userType)) {
            $error[] = __("wrong_event_parameters");
            echo json_encode(array("error" => $error));
            return;
        }

        if ($userType == EventMembers::L2_CHECKER || $userType == EventMembers::L3_CHECKER) {
            $education = (array)json_decode($appliedMember->profile->education);
            if (empty($education)) {
                $data["errors"][] = __("education_public");
            } else {
                foreach ($education as $item) {
                    if (!preg_match("/^(BA|MA|PHD)$/", $item)) {
                        $data["errors"][] = __("education_public");
                        break;
                    }
                }
            }

            $ed_area = (array)json_decode($appliedMember->profile->ed_area);
            if (empty($ed_area))
                $data["errors"][] = __("ed_area");
            else {
                foreach ($ed_area as $item) {
                    if (!preg_match("/^(Theology|Pastoral Ministry|Bible Translation|Exegetics)$/", $item)) {
                        $data["errors"][] = __("ed_area");
                        break;
                    }
                }
            }

            if (empty($appliedMember->profile->ed_place))
                $data["errors"][] = __("ed_place");

            if (empty($appliedMember->profile->hebrew_knwlg))
                $data["errors"][] = __("hebrew_knwlg");

            if (empty($appliedMember->profile->greek_knwlg))
                $data["errors"][] = __("greek_knwlg");

            $church_role = (array)json_decode($appliedMember->profile->church_role);
            if (empty($church_role))
                $data["errors"][] = __("church_role_public");
            else {
                foreach ($church_role as $item) {
                    if (!preg_match("/^(Elder|Bishop|Pastor|Teacher|Denominational Leader|Seminary Professor)$/", $item)) {
                        $data["errors"][] = __("church_role_public");
                        break;
                    }
                }
            }
        }

        if (empty($data["errors"])) {
            $event = $this->eventRepo->get($eventID);

            if (!$event) {
                $error[] = __("event_notexist_error");
                echo json_encode(array("error" => $error));
                return;
            }

            if (!$this->isAdmin($event)) {
                $error[] = __("not_enough_rights_error");
                echo json_encode(array("error" => $error));
                return;
            }

            $mode = $event->project->bookProject;
            $exists = $event->translators->contains($appliedMember)
                || $event->checkersL2->contains($appliedMember)
                || $event->checkersL3->contains($appliedMember);

            switch ($userType) {
                case EventMembers::TRANSLATOR:
                    if (!$exists) {
                        $chapter = in_array($mode, ["tn"]) ? -1 : 0;
                        $trData = array(
                            "step" => EventSteps::NONE,
                            "currentChapter" => $chapter
                        );

                        $event->translators()->attach($appliedMember, $trData);

                        echo json_encode(array("success" => __("successfully_applied")));
                    } else {
                        $error[] = __("error_member_in_event");
                    }
                    break;

                case EventMembers::L2_CHECKER:
                    if (!$exists) {
                        $l2Data = array(
                            "step" => EventSteps::NONE
                        );
                        $event->checkersL2()->attach($appliedMember, $l2Data);

                        echo json_encode(array("success" => __("successfully_applied")));
                    } else {
                        $error[] = __("error_member_in_event");
                    }
                    break;

                case EventMembers::L3_CHECKER:
                    if (!$exists) {
                        $chapter = in_array($mode, ["tn"]) ? -1 : 0;
                        $l3Data = array(
                            "step" => EventSteps::NONE,
                            "currentChapter" => $chapter
                        );
                        $event->checkersL3()->attach($appliedMember, $l3Data);

                        echo json_encode(array("success" => __("successfully_applied")));
                    } else {
                        $error[] = __("error_member_in_event");
                    }
                    break;
            }

            if (isset($error)) {
                echo json_encode(array("error" => $error));
            }
        } else {
            $error[] = __('empty_profile_error');
            echo json_encode(array("error" => $error, "errors" => $data["errors"]));
        }
    }

    /**
     * Delete user from event
     */
    public function deleteEventMember()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && $_POST["eventID"] != "" ? (integer)$_POST["eventID"] : null;
        $memberID = isset($_POST["memberID"]) && $_POST["memberID"] != "" ? (integer)$_POST["memberID"] : null;
        $manageMode = isset($_POST["manageMode"]) && $_POST["manageMode"] != "" ? $_POST["manageMode"] : "l1";

        $deleteMember = $this->memberRepo->get($memberID);

        if ($eventID !== null && $memberID != null) {
            $event = $this->eventRepo->get($eventID);
            if ($event) {
                if ($this->isAdmin($event)) {
                    $hasChapter = false;
                    $chapters = $this->_model->getChapters($event->eventID);

                    foreach ($chapters as $chap) {
                        $index = "memberID";
                        if ($manageMode == "l2")
                            $index = "l2memberID";
                        if ($manageMode == "l3")
                            $index = "l3memberID";
                        if ($chap[$index] == $memberID) {
                            $hasChapter = true;
                            break;
                        }
                    }

                    if (!$hasChapter) {
                        if ($manageMode == "l2")
                            $event->checkersL2()->detach($deleteMember);
                        else if ($manageMode == "l3")
                            $event->checkersL3()->detach($deleteMember);
                        else
                            $event->translators()->detach($deleteMember);
                        $response["success"] = true;
                    } else {
                        $response["error"] = __("translator_has_chapter");
                    }
                } else {
                    $response["error"] = __("not_enough_rights_error");
                }
            } else {
                $response["error"] = __("error_ocured", array("wrong parameters"));
            }
        } else {
            $response["error"] = __("error_ocured", array("wrong parameters"));
        }

        echo json_encode($response);
    }

    public function createWordsGroup()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && $_POST["eventID"] != "" ? (integer)$_POST["eventID"] : null;
        $group = isset($_POST["group"]) ? (array)$_POST["group"] : [];

        $group = array_filter($group, function ($elm) {
            return $elm != "";
        });

        if ($eventID && !empty($group)) {
            $event = $this->eventRepo->get($eventID);

            if ($event) {
                if (!$this->isAdmin($event)) {
                    $response["error"] = __("not_enough_rights_error");
                    echo json_encode($response);
                    return;
                }

                $groups = $this->_model->getTwGroups(["eventID" => $eventID]);

                $testGroup = [];
                foreach ($groups as $gr) {
                    $elm = (array)json_decode($gr->words, true);
                    $testGroup = Arrays::append($testGroup, $elm);
                }

                if (empty(array_intersect($group, $testGroup))) {
                    $created = $this->_model->createTwGroup([
                        "eventID" => $eventID,
                        "words" => json_encode($group)
                    ]);

                    if ($created) {
                        $response["success"] = true;
                    }
                } else {
                    $response["success"] = false;
                    $response["error"] = __("words_present_in_group_error");
                }
            } else {
                $response["success"] = false;
                $response["error"] = __("error_ocured", array("wrong parameters"));
            }
        } else {
            $response["success"] = false;
            $response["error"] = __("error_ocured", array("wrong parameters"));
        }

        echo json_encode($response);
    }

    public function deleteWordsGroup()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) ? (integer)$_POST["eventID"] : null;
        $groupID = isset($_POST["groupID"]) ? (integer)$_POST["groupID"] : null;

        if ($eventID && $groupID) {
            $event = $this->eventRepo->get($eventID);

            if ($event) {
                if (!$this->isAdmin($event)) {
                    $response["error"] = __("not_enough_rights_error");
                    echo json_encode($response);
                    return;
                }

                $chapter = $this->_model->getChapters($eventID, null, $groupID);

                if (empty($chapter)) {
                    $deleted = $this->_model->deleteTwGroups([
                        "groupID" => $groupID
                    ]);

                    if ($deleted) {
                        $response["success"] = true;
                    } else {
                        $response["success"] = false;
                        $response["error"] = __("error_ocured", array("wrong parameters"));
                    }
                } else {
                    $response["success"] = false;
                    $response["error"] = __("user_has_group_error");
                }
            } else {
                $response["success"] = false;
                $response["error"] = __("error_ocured", array("wrong parameters"));
            }
        } else {
            $response["success"] = false;
            $response["error"] = __("error_ocured", array("wrong parameters"));
        }

        echo json_encode($response);
    }

    public function getEventMembers()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && $_POST["eventID"] != "" ? (integer)$_POST["eventID"] : null;
        $memberIDs = isset($_POST["memberIDs"]) && $_POST["memberIDs"] != "" ? (array)$_POST["memberIDs"] : [];
        $manageMode = isset($_POST["manageMode"]) && $_POST["manageMode"] != "" ? $_POST["manageMode"] : "l1";

        if ($eventID !== null && $memberIDs !== null) {
            $event = $this->eventRepo->get($eventID);

            if ($event) {
                if ($event->admins->contains($this->_member)
                    || $event->project->admins->contains($this->_member)
                    || $event->project->gatewayLanguage->admins->contains($this->_member)) {

                    if ($manageMode == "l1")
                        $members = $event->translators;
                    else if ($manageMode == "l2")
                        $members = $event->checkersL2;
                    else if ($manageMode == "l3")
                        $members = $event->checkersL3;
                    else
                        $members = new Collection();

                    $response["members"] = $members->except($memberIDs)->getDictionary();
                    $response["success"] = true;
                } else {
                    $response["error"] = __("not_enough_rights_error");
                }
            } else {
                $response["error"] = __("error_ocured", array("wrong parameters"));
            }
        } else {
            $response["error"] = __("error_ocured", array("wrong parameters"));
        }

        echo json_encode($response);
    }

    public function sendUserEmail() {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && $_POST["eventID"] != "" ? (integer)$_POST["eventID"] : null;
        $memberID = isset($_POST["memberID"]) && $_POST["memberID"] != "" ? (integer)$_POST["memberID"] : null;
        $chapter = isset($_POST["chapter"]) && $_POST["chapter"] != "" ? (integer)$_POST["chapter"] : null;

        if ($eventID && $memberID) {
            $event = $this->eventRepo->get($eventID);

            if ($event) {
                if ($event->admins->contains($this->_member)
                    || $event->project->admins->contains($this->_member)
                    || $event->project->gatewayLanguage->admins->contains($this->_member)) {

                    $member = $this->memberRepo->get($memberID);

                    if ($member) {
                        if ($chapter) {
                            $this->sendChapterAssignmentNotif($event, $member, $chapter);
                        } else {
                            $this->sendProjectAssignmentNotif($event, $member);
                        }

                        $response["success"] = true;
                    }
                } else {
                    $response["error"] = __("not_enough_rights_error");
                }
            } else {
                $response["error"] = __("error_ocured", array("wrong parameters"));
            }
        }
    }

    private function getTranslationWordsByCategory($category, $lang = "en", $onlyNames = false)
    {
        $tw_cache_words = "tw_" . $lang . "_" . $category . ($onlyNames ? "_names" : "");

        if (Cache::has($tw_cache_words)) {
            $tw_source = Cache::get($tw_cache_words);
            $tWords = json_decode($tw_source, true);
        } else {
            $tWords = $this->_apiModel->getTranslationWordsByCategory($category, $lang, $onlyNames);

            if (!empty($tWords))
                Cache::add($tw_cache_words, json_encode($tWords), 365 * 24 * 7);
        }

        return $tWords;
    }

    private function isAdmin($event) {
        return $event->admins->contains($this->_member)
            || $event->project->admins->contains($this->_member)
            || $event->project->gatewayLanguage->admins->contains($this->_member);
    }

    private function sendProjectAssignmentNotif($event, $user) {
        if(Config::get("app.type") == "remote") {
            Mailer::send('Emails/Manage/ProjectAssignmentNotification',
                [
                    "book" => $event->bookInfo->name,
                    "language" => $event->project->gatewayLanguage->language->langName,
                    "project" => __($event->project->bookProject),
                    "target" => $event->project->targetLanguage->langName
                ],
                function($message) use($user)
                {
                    $message->to($user->email)->subject(__("project_assignment_notif"));
                });
        }
    }

    private function sendChapterAssignmentNotif($event, $user, $chapter) {
        if(Config::get("app.type") == "remote") {
            Mailer::send('Emails/Manage/ChapterAssignmentNotification',
                [
                    "book" => $event->bookInfo->name,
                    "language" => $event->project->gatewayLanguage->language->langName,
                    "project" => __($event->project->bookProject),
                    "target" => $event->project->targetLanguage->langName,
                    "chapter" => $chapter
                ],
                function ($message) use ($user) {
                    $message->to($user->email)->subject(__('chapter_assignment_notif'));
                });
        }
    }
}
