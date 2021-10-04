<?php
/**
 * Created by mXaln
 */

namespace App\Controllers;

use App\Models\SailDictionaryModel;
use Support\Facades\View;
use Config\Config;
use Helpers\Url;
use App\Core\Controller;
use Helpers\Constants\EventSteps;
use Helpers\Constants\EventCheckSteps;
use stdClass;

class DemoController extends Controller {
    public function __construct() {
        parent::__construct();

        if (Config::get("app.isMaintenance")
            && !in_array($_SERVER['REMOTE_ADDR'], Config::get("app.ips"))) {
            Url::redirect("maintenance");
        }
    }

    public function demo($page = null)
    {
        if (!isset($page))
            Url::redirect("events/demo/pray");

        $notifications = [];

        for ($i = 0; $i < 3; $i++) {
            $notifObj = new stdClass();

            if ($i == 0)
                $notifObj->step = EventSteps::PEER_REVIEW;
            elseif ($i == 1)
                $notifObj->step = EventSteps::KEYWORD_CHECK;
            else
                $notifObj->step = EventSteps::CONTENT_REVIEW;

            $notifObj->currentChapter = 2;
            $notifObj->firstName = "Mark";
            $notifObj->lastName = "Patton";
            $notifObj->bookCode = "2ti";
            $notifObj->bookProject = "ulb";
            $notifObj->tLang = "English";
            $notifObj->bookName = "2 Timothy";
            $notifObj->manageMode = "l1";
            $notifObj->sourceBible = "ulb";

            $notifications[] = $notifObj;
        }

        $data["notifications"] = $notifications;
        $data["isDemo"] = true;
        $data["isLangInput"] = false;
        $data["menu"] = 5;
        $data["next_step"] = EventSteps::PRAY;

        $view = View::make("Events/L1/Demo/DemoHeader");
        $data["step"] = "";

        switch ($page) {
            case "pray":
                $view->nest("page", "Events/L1/Demo/Pray");
                $data["step"] = EventSteps::PRAY;
                $data["next_step"] = EventSteps::CONSUME;
                break;

            case "consume":
                $view->nest("page", "Events/L1/Demo/Consume");
                $data["step"] = EventSteps::CONSUME;
                $data["next_step"] = EventSteps::VERBALIZE;
                break;

            case "verbalize":
                $view->nest("page", "Events/L1/Demo/Verbalize");
                $data["step"] = EventSteps::VERBALIZE;
                $data["next_step"] = EventSteps::CHUNKING;
                break;

            case "chunking":
                $view->nest("page", "Events/L1/Demo/Chunking");
                $data["step"] = EventSteps::CHUNKING;
                $data["next_step"] = EventSteps::BLIND_DRAFT;
                break;

            case "read_chunk":
                $view->nest("page", "Events/L1/Demo/ReadChunk");
                $data["step"] = EventSteps::READ_CHUNK;
                $data["next_step"] = "continue_alt";
                break;

            case "blind_draft":
                $view->nest("page", "Events/L1/Demo/BlindDraft");
                $data["step"] = EventSteps::BLIND_DRAFT;
                $data["next_step"] = EventSteps::SELF_CHECK;
                break;

            case "self_check":
                $view->nest("page", "Events/L1/Demo/SelfCheck");
                $data["step"] = EventSteps::SELF_CHECK;
                $data["next_step"] = EventSteps::PEER_REVIEW;
                break;

            case "peer_review":
                $view->nest("page", "Events/L1/Demo/PeerReview");
                $data["step"] = EventSteps::PEER_REVIEW;
                $data["next_step"] = EventSteps::KEYWORD_CHECK;
                break;

            case "peer_review_checker":
                $view->nest("page", "Events/L1/Demo/PeerReviewChecker");
                $data["step"] = EventSteps::PEER_REVIEW;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                break;

            case "keyword_check":
                $view->nest("page", "Events/L1/Demo/KeywordCheck");
                $data["step"] = EventSteps::KEYWORD_CHECK;
                $data["next_step"] = EventSteps::CONTENT_REVIEW;
                break;

            case "keyword_check_checker":
                $view->nest("page", "Events/L1/Demo/KeywordCheckChecker");
                $data["step"] = EventSteps::KEYWORD_CHECK;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                break;

            case "content_review":
                $view->nest("page", "Events/L1/Demo/ContentReview");
                $data["step"] = EventSteps::CONTENT_REVIEW;
                $data["next_step"] = EventSteps::FINAL_REVIEW;
                break;

            case "content_review_checker":
                $view->nest("page", "Events/L1/Demo/ContentReviewChecker");
                $data["step"] = EventSteps::CONTENT_REVIEW;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                break;

            case "final_review":
                $view->nest("page", "Events/L1/Demo/FinalReview");
                $data["step"] = EventSteps::FINAL_REVIEW;
                $data["next_step"] = "continue_alt";
                break;

            case "information":
                return View::make("Events/L1/Demo/Information")
                    ->shares("title", __("event_info"))
                    ->shares("data", $data);
                break;
        }

        return $view
            ->shares("title", __("demo"))
            ->shares("data", $data);
    }

    public function demoLangInput($page = null)
    {
        if (!isset($page))
            Url::redirect("events/demo-scripture-input/pray");

        $data["notifications"] = [];
        $data["isDemo"] = true;
        $data["isLangInput"] = true;
        $data["menu"] = 5;
        $data["next_step"] = EventSteps::PRAY;

        $view = View::make("Events/L1/Demo/DemoHeader");
        $data["step"] = "";

        switch ($page) {
            case "pray":
                $view->nest("page", "Events/L1/Demo/Pray");
                $data["step"] = EventSteps::PRAY;
                $data["next_step"] = "multi-draft_lang_input";
                break;

            case "input":
                $view->nest("page", "Events/L1/Demo/LangInput");
                $data["step"] = EventSteps::MULTI_DRAFT;
                $data["next_step"] = EventSteps::SELF_CHECK;
                break;

            case "self_check":
                $view->nest("page", "Events/L1/Demo/SelfCheckLangInput");
                $data["step"] = EventSteps::SELF_CHECK;
                $data["next_step"] = "continue_alt";
                break;

            case "information":
                return View::make("Events/L1/Demo/Information")
                    ->shares("title", __("event_info"))
                    ->shares("data", $data);
                break;
        }

        return $view
            ->shares("title", __("demo"))
            ->shares("data", $data);
    }

    public function demoTn($page = null)
    {
        if (!isset($page))
            Url::redirect("events/demo-tn/pray");

        $notifications = [];

        for ($i = 0; $i < 2; $i++) {
            $notifObj = new stdClass();

            if ($i == 0) {
                $notifObj->currentChapter = 1;
                $notifObj->firstName = "Mark";
                $notifObj->lastName = "Patton";
                $notifObj->bookCode = "act";
                $notifObj->bookProject = "tn";
                $notifObj->tLang = "Bahasa Indonesia";
                $notifObj->bookName = "Acts";
                $notifObj->step = "other";
                $notifObj->manageMode = "tn";
                $notifObj->sourceBible = "ulb";
            } else {
                $notifObj->step = EventSteps::PEER_REVIEW;
                $notifObj->currentChapter = 1;
                $notifObj->firstName = "Henry";
                $notifObj->lastName = "Miller";
                $notifObj->bookCode = "act";
                $notifObj->bookProject = "tn";
                $notifObj->tLang = "Bahasa Indonesia";
                $notifObj->bookName = "Acts";
                $notifObj->manageMode = "tn";
                $notifObj->sourceBible = "ulb";
            }

            $notifications[] = $notifObj;
        }

        $data["notifications"] = $notifications;
        $data["isDemo"] = true;
        $data["menu"] = 5;
        $data["isCheckerPage"] = false;
        $data["isPeerPage"] = false;
        $data["next_step"] = EventSteps::PRAY;

        $view = View::make("Events/Notes/Demo/DemoHeader");
        $data["step"] = "";

        switch ($page) {
            case "pray":
                $view->nest("page", "Events/Notes/Demo/Pray");
                $data["step"] = EventSteps::PRAY;
                $data["next_step"] = EventSteps::CONSUME . "_tn";
                break;

            case "consume":
                $view->nest("page", "Events/Notes/Demo/Consume");
                $data["step"] = EventSteps::CONSUME;
                $data["next_step"] = EventSteps::READ_CHUNK . "_tn";
                break;

            case "read_chunk":
                $view->nest("page", "Events/Notes/Demo/ReadChunk");
                $data["step"] = EventSteps::READ_CHUNK;
                $data["next_step"] = EventSteps::BLIND_DRAFT;
                break;

            case "blind_draft":
                $view->nest("page", "Events/Notes/Demo/BlindDraft");
                $data["step"] = EventSteps::BLIND_DRAFT;
                $data["next_step"] = EventSteps::SELF_CHECK;
                break;

            case "self_check":
                $view->nest("page", "Events/Notes/Demo/SelfEdit");
                $data["step"] = EventSteps::SELF_CHECK;
                $data["next_step"] = "continue_alt";
                break;

            case "pray_chk":
                $view->nest("page", "Events/Notes/Demo/PrayChk");
                $data["step"] = EventSteps::PRAY;
                $data["next_step"] = EventSteps::CONSUME . "_tn";
                $data["isCheckerPage"] = true;
                break;

            case "consume_chk":
                $view->nest("page", "Events/Notes/Demo/ConsumeChk");
                $data["step"] = EventSteps::CONSUME;
                $data["next_step"] = EventSteps::HIGHLIGHT . "_tn";
                $data["isCheckerPage"] = true;
                break;

            case "highlight":
                $view->nest("page", "Events/Notes/Demo/Highlight");
                $data["step"] = EventSteps::HIGHLIGHT;
                $data["next_step"] = EventSteps::SELF_CHECK . "_tn_chk";
                $data["isCheckerPage"] = true;
                break;

            case "self_check_chk":
                $view->nest("page", "Events/Notes/Demo/SelfEditChk");
                $data["step"] = EventSteps::SELF_CHECK;
                $data["next_step"] = EventSteps::KEYWORD_CHECK . "_tn";
                $data["isCheckerPage"] = true;
                break;

            case "highlight_chk":
                $view->nest("page", "Events/Notes/Demo/HighlightChk");
                $data["step"] = EventSteps::KEYWORD_CHECK;
                $data["next_step"] = EventSteps::PEER_REVIEW . "_tn";
                $data["isCheckerPage"] = true;
                break;

            case "peer_review":
                $view->nest("page", "Events/Notes/Demo/PeerReview");
                $data["step"] = EventSteps::PEER_REVIEW;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                break;

            case "peer_review_checker":
                $view->nest("page", "Events/Notes/Demo/PeerReviewChecker");
                $data["step"] = EventSteps::PEER_REVIEW;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                $data["isPeerPage"] = true;
                break;

            case "information":
                return View::make("Events/Notes/Demo/Information")
                    ->shares("title", __("event_info"));
                break;
        }

        return $view
            ->shares("title", __("demo"))
            ->shares("data", $data);
    }

    public function demoTq($page = null)
    {
        if (!isset($page))
            Url::redirect("events/demo-tq/pray");

        $notifications = [];

        for ($i = 0; $i < 2; $i++) {
            $notifObj = new stdClass();

            if ($i == 0) {
                $notifObj->currentChapter = 1;
                $notifObj->firstName = "Антон";
                $notifObj->lastName = "Шилов";
                $notifObj->bookCode = "3jn";
                $notifObj->bookProject = "tq";
                $notifObj->tLang = "Русский";
                $notifObj->bookName = "3 John";
                $notifObj->step = "other";
                $notifObj->manageMode = "tq";
                $notifObj->sourceBible = "ulb";
            } else {
                $notifObj->step = EventSteps::PEER_REVIEW;
                $notifObj->currentChapter = 1;
                $notifObj->firstName = "Tanya";
                $notifObj->lastName = "Enotova";
                $notifObj->bookCode = "3jn";
                $notifObj->bookProject = "tq";
                $notifObj->tLang = "Русский";
                $notifObj->bookName = "3 John";
                $notifObj->manageMode = "tq";
                $notifObj->sourceBible = "ulb";
            }

            $notifications[] = $notifObj;
        }

        $data["notifications"] = $notifications;
        $data["isDemo"] = true;
        $data["menu"] = 5;
        $data["isCheckerPage"] = false;
        $data["isPeerPage"] = false;
        $data["next_step"] = EventSteps::PRAY;

        $view = View::make("Events/Questions/Demo/DemoHeader");
        $data["step"] = "";

        switch ($page) {
            case "pray":
                $view->nest("page", "Events/Questions/Demo/Pray");
                $data["step"] = EventSteps::PRAY;
                $data["next_step"] = EventSteps::MULTI_DRAFT;
                break;

            case "multi_draft":
                $view->nest("page", "Events/Questions/Demo/MultiDraft");
                $data["step"] = EventSteps::MULTI_DRAFT;
                $data["next_step"] = EventSteps::SELF_CHECK;
                break;

            case "self_check":
                $view->nest("page", "Events/Questions/Demo/SelfEdit");
                $data["step"] = EventSteps::SELF_CHECK;
                $data["next_step"] = "continue_alt";
                break;

            case "pray_chk":
                $view->nest("page", "Events/Questions/Demo/PrayChk");
                $data["step"] = EventSteps::PRAY;
                $data["next_step"] = EventSteps::KEYWORD_CHECK;
                $data["isCheckerPage"] = true;
                break;

            case "keyword_check":
                $view->nest("page", "Events/Questions/Demo/KeywordCheck");
                $data["step"] = EventSteps::KEYWORD_CHECK;
                $data["next_step"] = EventSteps::PEER_REVIEW . "_tq";
                $data["isCheckerPage"] = true;
                break;

            case "peer_review":
                $view->nest("page", "Events/Questions/Demo/PeerReview");
                $data["step"] = EventSteps::PEER_REVIEW;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                break;

            case "peer_review_checker":
                $view->nest("page", "Events/Questions/Demo/PeerReviewChecker");
                $data["step"] = EventSteps::PEER_REVIEW;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                $data["isPeerPage"] = true;
                break;

            case "information":
                return View::make("Events/Questions/Demo/Information")
                    ->shares("title", __("event_info"));
                break;
        }

        return $view
            ->shares("title", __("demo"))
            ->shares("data", $data);
    }

    public function demoTw($page = null)
    {
        if (!isset($page))
            Url::redirect("events/demo-tw/pray");

        $notifications = [];

        for ($i = 0; $i < 2; $i++) {
            $notifObj = new stdClass();

            if ($i == 0) {
                $notifObj->currentChapter = 1;
                $notifObj->firstName = "Антон";
                $notifObj->lastName = "Шилов";
                $notifObj->bookCode = "wns";
                $notifObj->bookProject = "tw";
                $notifObj->tLang = "Русский";
                $notifObj->bookName = "names";
                $notifObj->step = "other";
                $notifObj->manageMode = "tw";
                $notifObj->group = "aaron...adam";
                $notifObj->sourceBible = "ulb";
            } else {
                $notifObj->step = EventSteps::PEER_REVIEW;
                $notifObj->currentChapter = 1;
                $notifObj->firstName = "Tanya";
                $notifObj->lastName = "Enotova";
                $notifObj->bookCode = "wns";
                $notifObj->bookProject = "tw";
                $notifObj->tLang = "Русский";
                $notifObj->bookName = "names";
                $notifObj->manageMode = "tw";
                $notifObj->group = "aaron...adam";
                $notifObj->sourceBible = "ulb";
            }

            $notifications[] = $notifObj;
        }

        $data["notifications"] = $notifications;
        $data["isDemo"] = true;
        $data["menu"] = 5;
        $data["isCheckerPage"] = false;
        $data["isPeerPage"] = false;
        $data["next_step"] = EventSteps::PRAY;

        $view = View::make("Events/Words/Demo/DemoHeader");
        $data["step"] = "";

        switch ($page) {
            case "pray":
                $view->nest("page", "Events/Words/Demo/Pray");
                $data["step"] = EventSteps::PRAY;
                $data["next_step"] = EventSteps::MULTI_DRAFT;
                break;

            case "multi_draft":
                $view->nest("page", "Events/Words/Demo/MultiDraft");
                $data["step"] = EventSteps::MULTI_DRAFT;
                $data["next_step"] = EventSteps::SELF_CHECK;
                break;

            case "self_check":
                $view->nest("page", "Events/Words/Demo/SelfEdit");
                $data["step"] = EventSteps::SELF_CHECK;
                $data["next_step"] = "continue_alt";
                break;

            case "pray_chk":
                $view->nest("page", "Events/Words/Demo/PrayChk");
                $data["step"] = EventSteps::PRAY;
                $data["next_step"] = EventSteps::KEYWORD_CHECK;
                $data["isCheckerPage"] = true;
                break;

            case "keyword_check":
                $view->nest("page", "Events/Words/Demo/KeywordCheck");
                $data["step"] = EventSteps::KEYWORD_CHECK;
                $data["next_step"] = EventSteps::PEER_REVIEW . "_tw";
                $data["isCheckerPage"] = true;
                break;

            case "peer_review":
                $view->nest("page", "Events/Words/Demo/PeerReview");
                $data["step"] = EventSteps::PEER_REVIEW;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                break;

            case "peer_review_checker":
                $view->nest("page", "Events/Words/Demo/PeerReviewChecker");
                $data["step"] = EventSteps::PEER_REVIEW;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                $data["isPeerPage"] = true;
                break;

            case "information":
                return View::make("Events/Words/Demo/Information")
                    ->shares("title", __("event_info"));
                break;
        }

        return $view
            ->shares("title", __("demo"))
            ->shares("data", $data);
    }

    public function demoL2($page = null)
    {
        if (!isset($page))
            Url::redirect("events/demo-l2/pray");

        for ($i = 0; $i < 2; $i++) {
            $notifObj = new stdClass();

            if ($i == 0)
                $notifObj->step = EventCheckSteps::SND_CHECK;
            else
                $notifObj->step = EventCheckSteps::PEER_REVIEW_L2;

            $notifObj->currentChapter = 2;
            $notifObj->firstName = "Mark";
            $notifObj->lastName = "Patton";
            $notifObj->bookCode = "2ti";
            $notifObj->bookProject = "ulb";
            $notifObj->tLang = "English";
            $notifObj->bookName = "2 Timothy";
            $notifObj->manageMode = "l2";
            $notifObj->sourceBible = "ulb";

            $notifications[] = $notifObj;
        }

        $data["notifications"] = $notifications;
        $data["isDemo"] = true;
        $data["menu"] = 5;
        $data["isCheckerPage"] = true;
        $data["next_step"] = EventCheckSteps::PRAY;

        $view = View::make("Events/L2/Demo/DemoHeader");
        $data["step"] = "";

        switch ($page) {
            case "pray":
                $view->nest("page", "Events/L2/Demo/Pray");
                $data["step"] = EventCheckSteps::PRAY;
                $data["next_step"] = EventCheckSteps::CONSUME;
                break;

            case "consume":
                $view->nest("page", "Events/L2/Demo/Consume");
                $data["step"] = EventCheckSteps::CONSUME;
                $data["next_step"] = EventCheckSteps::FST_CHECK;
                break;

            case "fst_check":
                $view->nest("page", "Events/L2/Demo/FstCheck");
                $data["step"] = EventCheckSteps::FST_CHECK;
                $data["next_step"] = "continue_alt";
                break;

            case "snd_check":
                $view->nest("page", "Events/L2/Demo/SndCheck");
                $data["step"] = EventCheckSteps::SND_CHECK;
                $data["next_step"] = EventCheckSteps::KEYWORD_CHECK_L2;
                break;

            case "keyword_check_l2":
                $view->nest("page", "Events/L2/Demo/KeywordCheck");
                $data["step"] = EventCheckSteps::KEYWORD_CHECK_L2;
                $data["next_step"] = "continue_alt";;
                break;

            case "peer_review_l2":
                $view->nest("page", "Events/L2/Demo/PeerReview");
                $data["step"] = EventCheckSteps::PEER_REVIEW_L2;
                $data["next_step"] = "continue_alt";
                break;

            case "peer_review_l2_checker":
                $view->nest("page", "Events/L2/Demo/PeerReviewChecker");
                $data["step"] = EventCheckSteps::PEER_REVIEW_L2;
                $data["next_step"] = "continue_alt";
                unset($data["isCheckerPage"]);
                break;

            case "information":
                return View::make("Events/L2/Demo/Information")
                    ->shares("title", __("event_info"));
                break;
        }

        return $view
            ->shares("title", __("demo"))
            ->shares("data", $data);
    }

    public function demoL3($page = null)
    {
        if (!isset($page))
            Url::redirect("events/demo-l3/pray");

        $notifObj = new stdClass();
        $notifObj->step = EventCheckSteps::PEER_REVIEW_L3;
        $notifObj->currentChapter = 2;
        $notifObj->firstName = "Mark";
        $notifObj->lastName = "Patton";
        $notifObj->bookCode = "2ti";
        $notifObj->bookProject = "ulb";
        $notifObj->tLang = "Papuan Malay";
        $notifObj->bookName = "2 Timothy";
        $notifObj->manageMode = "l3";
        $notifObj->sourceBible = "ulb";

        $notifications[] = $notifObj;

        $data["notifications"] = $notifications;
        $data["isDemo"] = true;
        $data["menu"] = 5;
        $data["isCheckerPage"] = true;
        $data["isPeer"] = false;
        $data["next_step"] = EventCheckSteps::PRAY;

        $view = View::make("Events/L3/Demo/DemoHeader");
        $data["step"] = "";

        switch ($page) {
            case "pray":
                $view->nest("page", "Events/L3/Demo/Pray");
                $data["step"] = EventCheckSteps::PRAY;
                $data["next_step"] = EventCheckSteps::PEER_REVIEW_L3;
                break;

            case "peer_review_l3":
                $view->nest("page", "Events/L3/Demo/PeerReview");
                $data["step"] = EventCheckSteps::PEER_REVIEW_L3;
                $data["next_step"] = EventCheckSteps::PEER_EDIT_L3;
                break;

            case "peer_edit_l3":
                $view->nest("page", "Events/L3/Demo/PeerEdit");
                $data["step"] = EventCheckSteps::PEER_EDIT_L3;
                $data["next_step"] = "continue_alt";
                break;

            case "peer_review_l3_checker":
                $view->nest("page", "Events/L3/Demo/PeerReviewChecker");
                $data["step"] = EventCheckSteps::PEER_REVIEW_L3;
                $data["next_step"] = EventCheckSteps::PEER_EDIT_L3;
                $data["isPeer"] = true;
                break;

            case "peer_edit_l3_checker":
                $view->nest("page", "Events/L3/Demo/PeerEditChecker");
                $data["step"] = EventCheckSteps::PEER_EDIT_L3;
                $data["next_step"] = "continue_alt";
                $data["isPeer"] = true;
                break;

            case "information":
                return View::make("Events/L3/Demo/Information")
                    ->shares("title", __("event_info"));
                break;
        }

        return $view
            ->shares("title", __("demo"))
            ->shares("data", $data);
    }

    public function demoL3Notes($page = null)
    {
        if (!isset($page))
            Url::redirect("events/demo-tn-l3/pray");

        $notifObj = new stdClass();
        $notifObj->step = EventCheckSteps::PEER_REVIEW_L3;
        $notifObj->currentChapter = 2;
        $notifObj->firstName = "Mark";
        $notifObj->lastName = "Patton";
        $notifObj->bookCode = "jas";
        $notifObj->bookProject = "tn";
        $notifObj->tLang = "Bahasa Indonesia";
        $notifObj->bookName = "James";
        $notifObj->manageMode = "l3";
        $notifObj->sourceBible = "ulb";

        $notifications[] = $notifObj;

        $data["notifications"] = $notifications;
        $data["isDemo"] = true;
        $data["menu"] = 5;
        $data["isCheckerPage"] = true;
        $data["isPeer"] = false;
        $data["next_step"] = EventCheckSteps::PRAY;

        $view = View::make("Events/L3Notes/Demo/DemoHeader");
        $data["step"] = "";

        switch ($page) {
            case "pray":
                $view->nest("page", "Events/L3Notes/Demo/Pray");
                $data["step"] = EventCheckSteps::PRAY;
                $data["next_step"] = EventCheckSteps::PEER_REVIEW_L3;
                break;

            case "peer_review_l3":
                $view->nest("page", "Events/L3Notes/Demo/PeerReview");
                $data["step"] = EventCheckSteps::PEER_REVIEW_L3;
                $data["next_step"] = EventCheckSteps::PEER_EDIT_L3;
                break;

            case "peer_edit_l3":
                $view->nest("page", "Events/L3Notes/Demo/PeerEdit");
                $data["step"] = EventCheckSteps::PEER_EDIT_L3;
                $data["next_step"] = "continue_alt";
                break;

            case "peer_review_l3_checker":
                $view->nest("page", "Events/L3Notes/Demo/PeerReviewChecker");
                $data["step"] = EventCheckSteps::PEER_REVIEW_L3;
                $data["next_step"] = EventCheckSteps::PEER_EDIT_L3;
                $data["isPeer"] = true;
                break;

            case "peer_edit_l3_checker":
                $view->nest("page", "Events/L3Notes/Demo/PeerEditChecker");
                $data["step"] = EventCheckSteps::PEER_EDIT_L3;
                $data["next_step"] = "continue_alt";
                $data["isPeer"] = true;
                break;

            case "information":
                return View::make("Events/L3Notes/Demo/Information")
                    ->shares("title", __("event_info"));
                break;
        }

        return $view
            ->shares("title", __("demo"))
            ->shares("data", $data);
    }

    public function demoSun($page = null)
    {
        if (!isset($page))
            Url::redirect("events/demo-sun/pray");

        for ($i = 0; $i < 2; $i++) {
            $notifObj = new stdClass();

            if ($i == 0)
                $notifObj->step = EventSteps::THEO_CHECK;
            else
                $notifObj->step = EventSteps::CONTENT_REVIEW;

            $notifObj->currentChapter = 2;
            $notifObj->firstName = "Mark";
            $notifObj->lastName = "Patton";
            $notifObj->bookCode = "mat";
            $notifObj->bookProject = "sun";
            $notifObj->tLang = "English";
            $notifObj->bookName = "Matthew";
            $notifObj->manageMode = "sun";
            $notifObj->sourceBible = "ulb";

            $notifications[] = $notifObj;
        }

        $data["notifications"] = $notifications;
        $data["isDemo"] = true;
        $data["isCheckerPage"] = false;
        $data["next_step"] = EventSteps::PRAY;
        $data["menu"] = 5;

        $this->_saildictModel = new SailDictionaryModel();

        $view = View::make("Events/SUN/Demo/DemoHeader");
        $data["step"] = "";

        switch ($page) {
            case "pray":
                $view->nest("page", "Events/SUN/Demo/Pray");
                $data["step"] = EventSteps::PRAY;
                $data["next_step"] = EventSteps::CONSUME;
                break;

            case "consume":
                $view->nest("page", "Events/SUN/Demo/Consume");
                $data["step"] = EventSteps::CONSUME;
                $data["next_step"] = EventSteps::CHUNKING . "_sun";
                break;

            case "chunking":
                $view->nest("page", "Events/SUN/Demo/Chunking");
                $data["step"] = EventSteps::CHUNKING;
                $data["next_step"] = EventSteps::REARRANGE;
                break;

            case "rearrange":
                $view->nest("page", "Events/SUN/Demo/WordsDraft");
                $data["step"] = EventSteps::REARRANGE;
                $data["next_step"] = EventSteps::SYMBOL_DRAFT;
                break;

            case "symbol-draft":
                $view->nest("page", "Events/SUN/Demo/SymbolsDraft");
                $data["step"] = EventSteps::SYMBOL_DRAFT;
                $data["next_step"] = EventSteps::SELF_CHECK;
                break;

            case "self-check":
                $view->nest("page", "Events/SUN/Demo/SelfCheck");
                $data["step"] = EventSteps::SELF_CHECK;
                $data["next_step"] = "continue_alt";
                break;

            case "theo_check_checker":
                $view->nest("page", "Events/SUN/Demo/TheoCheck");
                $data["step"] = EventSteps::THEO_CHECK;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                break;

            case "content_review_checker":
                $view->nest("page", "Events/SUN/Demo/ContentReview");
                $data["step"] = EventSteps::CONTENT_REVIEW;
                $data["next_step"] = EventSteps::FINAL_REVIEW;
                $data["isCheckerPage"] = true;
                break;

            case "verse-markers":
                $view->nest("page", "Events/SUN/Demo/FinalReview");
                $data["step"] = EventSteps::FINAL_REVIEW;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                break;

            case "information":
                return View::make("Events/SUN/Demo/Information")
                    ->shares("title", __("event_info"));
                break;
        }

        return $view
            ->shares("title", __("demo"))
            ->shares("data", $data);
    }

    public function demoSunL3($page = null)
    {
        if (!isset($page))
            Url::redirect("events/demo-sun-l3/pray");

        $notifObj = new stdClass();
        $notifObj->step = EventCheckSteps::PEER_REVIEW_L3;
        $notifObj->currentChapter = 17;
        $notifObj->firstName = "Mark";
        $notifObj->lastName = "Patton";
        $notifObj->bookCode = "mat";
        $notifObj->bookProject = "sun";
        $notifObj->tLang = "English";
        $notifObj->bookName = "Matthew";
        $notifObj->manageMode = "l3";
        $notifObj->sourceBible = "ulb";

        $notifications[] = $notifObj;

        $data["notifications"] = $notifications;
        $data["isDemo"] = true;
        $data["menu"] = 5;
        $data["isCheckerPage"] = true;
        $data["isPeer"] = false;
        $data["next_step"] = EventCheckSteps::PRAY;

        $data["bookCode"] = "mat";
        $data["currentChapter"] = 17;
        $data["tnLangID"] = "en";
        $data["twLangID"] = "en";
        $data["totalVerses"] = 27;
        $data["targetLang"] = "en";

        $view = View::make("Events/L3Sun/Demo/DemoHeader");
        $data["step"] = "";

        switch ($page) {
            case "pray":
                $view->nest("page", "Events/L3Sun/Demo/Pray");
                $data["step"] = EventCheckSteps::PRAY;
                $data["next_step"] = EventCheckSteps::PEER_REVIEW_L3;
                break;

            case "peer_review_l3":
                $view->nest("page", "Events/L3Sun/Demo/PeerReview");
                $data["step"] = EventCheckSteps::PEER_REVIEW_L3;
                $data["next_step"] = EventCheckSteps::PEER_EDIT_L3;
                break;

            case "peer_edit_l3":
                $view->nest("page", "Events/L3Sun/Demo/PeerEdit");
                $data["step"] = EventCheckSteps::PEER_EDIT_L3;
                $data["next_step"] = "continue_alt";
                break;

            case "peer_review_l3_checker":
                $view->nest("page", "Events/L3Sun/Demo/PeerReviewChecker");
                $data["step"] = EventCheckSteps::PEER_REVIEW_L3;
                $data["next_step"] = EventCheckSteps::PEER_EDIT_L3;
                $data["isPeer"] = true;
                break;

            case "peer_edit_l3_checker":
                $view->nest("page", "Events/L3Sun/Demo/PeerEditChecker");
                $data["step"] = EventCheckSteps::PEER_EDIT_L3;
                $data["next_step"] = "continue_alt";
                $data["isPeer"] = true;
                break;

            case "information":
                return View::make("Events/L3Sun/Demo/Information")
                    ->shares("title", __("event_info"));
                break;
        }

        return $view
            ->shares("title", __("demo"))
            ->shares("data", $data);
    }

    public function demoSunOdb($page = null)
    {
        if (!isset($page))
            Url::redirect("events/demo-sun-odb/pray");

        for ($i = 0; $i < 2; $i++) {
            $notifObj = new stdClass();

            if ($i == 0)
                $notifObj->step = EventSteps::THEO_CHECK;
            else
                $notifObj->step = EventSteps::CONTENT_REVIEW;

            $notifObj->currentChapter = 2;
            $notifObj->firstName = "Mark";
            $notifObj->lastName = "Patton";
            $notifObj->bookCode = "a01";
            $notifObj->bookProject = "sun";
            $notifObj->tLang = "English";
            $notifObj->bookName = "A01";
            $notifObj->manageMode = "sun-odb";
            $notifObj->sourceBible = "odb";

            $notifications[] = $notifObj;
        }

        $data["notifications"] = $notifications;
        $data["isDemo"] = true;
        $data["isCheckerPage"] = false;
        $data["next_step"] = EventSteps::PRAY;
        $data["menu"] = 5;

        $this->_saildictModel = new SailDictionaryModel();

        $view = View::make("Events/ODBSUN/Demo/DemoHeader");
        $data["step"] = "";

        switch ($page) {
            case "pray":
                $view->nest("page", "Events/ODBSUN/Demo/Pray");
                $data["step"] = EventSteps::PRAY;
                $data["next_step"] = EventSteps::CONSUME . "_odb";
                break;

            case "consume":
                $view->nest("page", "Events/ODBSUN/Demo/Consume");
                $data["step"] = EventSteps::CONSUME;
                $data["next_step"] = EventSteps::REARRANGE;
                break;

            case "rearrange":
                $view->nest("page", "Events/ODBSUN/Demo/WordsDraft");
                $data["step"] = EventSteps::REARRANGE;
                $data["next_step"] = EventSteps::SYMBOL_DRAFT;
                break;

            case "symbol-draft":
                $view->nest("page", "Events/ODBSUN/Demo/SymbolsDraft");
                $data["step"] = EventSteps::SYMBOL_DRAFT;
                $data["next_step"] = EventSteps::SELF_CHECK;
                break;

            case "self-check":
                $view->nest("page", "Events/ODBSUN/Demo/SelfCheck");
                $data["step"] = EventSteps::SELF_CHECK;
                $data["next_step"] = "continue_alt";
                break;

            case "theo_check_checker":
                $view->nest("page", "Events/ODBSUN/Demo/TheoCheck");
                $data["step"] = EventSteps::THEO_CHECK;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                break;

            case "content_review_checker":
                $view->nest("page", "Events/ODBSUN/Demo/ContentReview");
                $data["step"] = EventSteps::CONTENT_REVIEW;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                break;

            case "information":
                return View::make("Events/ODBSUN/Demo/Information")
                    ->shares("title", __("event_info"));
                break;
        }

        return $view
            ->shares("title", __("demo"))
            ->shares("data", $data);
    }

    public function demoRadio($page = null)
    {
        if (!isset($page))
            Url::redirect("events/demo-rad/pray");

        $notifObj = new stdClass();
        $notifObj->step = EventSteps::PEER_REVIEW;
        $notifObj->currentChapter = 1;
        $notifObj->firstName = "Henry";
        $notifObj->lastName = "Stanton";
        $notifObj->bookCode = "b02";
        $notifObj->bookProject = "rad";
        $notifObj->tLang = "Español Latin America";
        $notifObj->bookName = "B06";
        $notifObj->manageMode = "rad";
        $notifObj->sourceBible = "rad";

        $notifications = [$notifObj];

        $data["notifications"] = $notifications;
        $data["isDemo"] = true;
        $data["isCheckerPage"] = false;
        $data["next_step"] = EventSteps::PRAY;
        $data["menu"] = 5;

        $view = View::make("Events/Radio/Demo/DemoHeader");
        $data["step"] = "";

        switch ($page) {
            case "pray":
                $view->nest("page", "Events/Radio/Demo/Pray");
                $data["step"] = EventSteps::PRAY;
                $data["next_step"] = EventSteps::CONSUME . "_odb";
                break;

            case "consume":
                $view->nest("page", "Events/Radio/Demo/Consume");
                $data["step"] = EventSteps::CONSUME;
                $data["next_step"] = EventSteps::MULTI_DRAFT;
                break;

            case "multi-draft":
                $view->nest("page", "Events/Radio/Demo/MultiDraft");
                $data["step"] = EventSteps::MULTI_DRAFT;
                $data["next_step"] = EventSteps::SELF_CHECK;
                break;

            case "self-check":
                $view->nest("page", "Events/Radio/Demo/SelfCheck");
                $data["step"] = EventSteps::SELF_CHECK;
                $data["next_step"] = "continue_alt";
                break;

            case "peer_review":
                $view->nest("page", "Events/Radio/Demo/PeerReview");
                $data["step"] = EventSteps::PEER_REVIEW;
                $data["next_step"] = "continue_alt";
                $data["isCheckerPage"] = true;
                break;

            case "information":
                return View::make("Events/Radio/Demo/Information")
                    ->shares("title", __("event_info"));
                break;
        }

        return $view
            ->shares("title", __("demo"))
            ->shares("data", $data);
    }
}
