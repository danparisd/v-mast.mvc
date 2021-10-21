<?php
/**
 * Created by PhpStorm.
 * User: Maxim
 * Date: 05 Feb 2016
 * Time: 19:57
 */

namespace App\Models;

use App\Models\ORM\Event;
use App\Repositories\Event\IEventRepository;
use Database\Model;
use Database\ORM\Collection;
use DB;
use Helpers\Arrays;
use Helpers\Constants\EventCheckSteps;
use Helpers\Constants\EventSteps;
use Helpers\Session;
use PDO;


class EventsModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'eventID';
    protected $eventRepo = null;

    public function __construct(IEventRepository $eventRepo)
    {
        parent::__construct();
        $this->eventRepo = $eventRepo;
    }

    /**
     * Get project
     * @param array $select An array of fields
     * @param array $where Single/Multidimentional array with where params (field, operator, value, logical)
     * @return array|static[]
     */
    public function getProject(array $select, array $where)
    {
        $builder = $this->db->table("projects");

        foreach ($where as $item) {
            if (is_array($item)) {
                call_user_func_array(array($builder, "where"), $item);
            } else {
                call_user_func_array(array($builder, "where"), $where);
                break;
            }
        }

        return $builder
            ->leftJoin("languages", "languages.langID", "=", "projects.targetLang")
            ->select($select)->get();
    }


    /**
     * Get all events of a member or specific event
     * @param $memberID
     * @param null $eventID
     * @param null $chapter
     * @param bool $includeCheckers
     * @param bool $includeFinished
     * @param bool $includeNone
     * @return array
     */
    public function getMemberEvents(
        $memberID,
        $eventID = null,
        $chapter = null,
        $includeCheckers = false,
        $includeFinished = true,
        $includeNone = true
    ) {
        $sql = "SELECT translators.trID, " .
            "translators.memberID AS myMemberID, translators.step, " .
            "translators.currentChunk, translators.currentChapter, " .
            "translators.verbCheck, translators.peerCheck, " .
            "translators.kwCheck, translators.crCheck, " .
            "translators.otherCheck, translators.isChecker, " .
            "tw_groups.words, " .
            "(SELECT COUNT(*) FROM " . PREFIX . "translators AS all_trs WHERE all_trs.eventID = translators.eventID ) AS currTrs, " .
            "evnt.eventID, evnt.state, evnt.bookCode, evnt.dateFrom, evnt.langInput, " .
            "evnt.dateTo, " .
            "projects.projectID, projects.bookProject, " .
            "projects.sourceLangID, projects.gwLang, " .
            "projects.tnLangID, projects.tqLangID, projects.twLangID, " .
            "projects.targetLang, projects.glID, " .
            "projects.sourceBible, t_lang.langName as tLang, chapters.chunks, " .
            "t_lang.direction as tLangDir, projects.resLangID, res_lang.direction as resLangDir, " .
            "s_lang.langName as sLang, s_lang.direction as sLangDir, " .
            "book_info.name, book_info.sort, book_info.chaptersNum  " .
            "FROM " . PREFIX . "translators AS translators " .
            "LEFT JOIN " . PREFIX . "tw_groups AS tw_groups ON tw_groups.groupID = translators.currentChapter " .
            "LEFT JOIN " . PREFIX . "chapters AS chapters ON translators.eventID = chapters.eventID AND translators.currentChapter = chapters.chapter ".
            "LEFT JOIN " . PREFIX . "events AS evnt ON translators.eventID = evnt.eventID " .
            "LEFT JOIN " . PREFIX . "projects AS projects ON evnt.projectID = projects.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS res_lang ON projects.resLangID = res_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info AS book_info ON evnt.bookCode = book_info.code " .
            "WHERE translators.eventID !='' " .
            (!is_null($memberID) ? " AND translators.memberID = :memberID " : " ") .
            (!is_null($eventID) ? " AND translators.eventID=:eventID " : " ") .
            "ORDER BY tLang, projects.bookProject, book_info.sort";

        $prepare = array();
        if (!is_null($memberID))
            $prepare[":memberID"] = $memberID;

        if (!is_null($eventID))
            $prepare[":eventID"] = $eventID;

        $events = $this->db->select($sql, $prepare);
        $filtered = [];
        $eventAdmins = [];

        foreach ($events as $event) {
            if (empty($eventAdmins)) {
                $eventAdmins = $this->eventRepo->get($event->eventID)->admins;
            }
            $event->admins = $eventAdmins;

            $checkingSteps = [
                EventSteps::PEER_REVIEW,
                EventSteps::KEYWORD_CHECK,
                EventSteps::CONTENT_REVIEW,
                EventSteps::FINAL_REVIEW
            ];
            $excludedSteps = [];
            if (!$includeNone) {
                $excludedSteps[] = EventSteps::NONE;
            }
            if (!$includeFinished) {
                $excludedSteps[] = EventSteps::FINISHED;
            }
            $checkingSteps = array_merge($checkingSteps, $excludedSteps);
            $inTranslation = !in_array($event->step, $checkingSteps);

            if (in_array($event->bookProject, ["ulb","udb"])) {
                if ($inTranslation && !isset($chapter)) {
                    $event->checkerID = 0;
                    $filtered[] = $event;
                }
            } else {
                if (!in_array($event->step, $excludedSteps)) {
                    $event->checkerID = 0;
                    $filtered[] = $event;
                    continue;
                }
            }

            if (in_array($event->bookProject, ["ulb","udb"])) {
                $peerCheck = (array)json_decode($event->peerCheck, true);
                $kwCheck = (array)json_decode($event->kwCheck, true);
                $crCheck = (array)json_decode($event->crCheck, true);
                $otherCheck = (array)json_decode($event->otherCheck, true);

                foreach ($peerCheck as $chap => $data) {
                    if ($data["done"] < 2 && $includeCheckers && (!isset($chapter) || $chapter == $chap)) {
                        $ev = $this->modifiedCheckerEvent($event, $chap, $data, EventSteps::PEER_REVIEW);
                        $filtered[] = $ev;
                    }
                }

                foreach ($kwCheck as $chap => $data) {
                    if ($data["done"] < 2 && $includeCheckers && (!isset($chapter) || $chapter == $chap)) {
                        $ev = $this->modifiedCheckerEvent($event, $chap, $data, EventSteps::KEYWORD_CHECK);
                        $filtered[] = $ev;
                    }
                }

                foreach ($crCheck as $chap => $data) {
                    if ($data["done"] < 2 && $includeCheckers && (!isset($chapter) || $chapter == $chap)) {
                        $ev = $this->modifiedCheckerEvent($event, $chap, $data, EventSteps::CONTENT_REVIEW);
                        $filtered[] = $ev;
                    }
                }

                foreach ($otherCheck as $chap => $data) {
                    if ($data["done"] == 0 && $includeCheckers && (!isset($chapter) || $chapter == $chap)) {
                        $ev = clone $event;
                        $ev->step = EventSteps::FINAL_REVIEW;
                        $ev->currentChapter = $chap;
                        $ev->checkerID = 0;

                        $chapters = $this->getChapters($event->eventID, $event->myMemberID, $chapter); // Should be one
                        $chunks = !empty($chapters) ? $chapters[0]["chunks"] : "";
                        $ev->chunks = $chunks;

                        $filtered[] = $ev;
                    }
                }
            }
        }

        return $filtered;
    }

    private function modifiedCheckerEvent($event, $chapter, $data, $step, $manageMode = "l1") {
        $ev = clone $event;

        $checkerFName = null;
        $checkerLName = null;
        $checkerID = 0;
        $ev->step = $step;
        $ev->checkDone = false;

        if ($data["memberID"] != 0) {
            $memberModel = new MembersModel();
            $member = $memberModel->getMember([
                "firstName",
                "lastName"
            ], ["memberID", $data["memberID"]]);
            if (!empty($member)) {
                $checkerFName = $member[0]->firstName;
                $checkerLName = $member[0]->lastName;
                $checkerID = $data["memberID"];
            }
            $ev->checkDone = $data["done"] == 1;
        }

        $ev->currentChapter = $chapter;
        $ev->checkerID = $checkerID;
        $ev->checkerFName = $checkerFName;
        $ev->checkerLName = $checkerLName;

        $chapters = $this->getChapters($event->eventID, $event->myMemberID, $chapter, $manageMode); // Should be one
        $chunks = !empty($chapters) ? $chapters[0]["chunks"] : "";
        $ev->chunks = $chunks;

        return $ev;
    }

    /**
     * Get translator information
     * @param $memberID Checker member ID
     * @param null $eventID event ID
     * @param null $trMemberID Translator member ID
     * @return array
     */
    public function getMemberEventsForChecker($memberID, $eventID = null, $trMemberID = null, $chapter = null)
    {
        $prepare = [];
        if ($eventID)
            $prepare[":eventID"] = $eventID;
        if ($trMemberID)
            $prepare[":trMemberID"] = $trMemberID;

        $sql = "SELECT trs.*, members.userName, members.firstName, " .
            "members.lastName, evnt.bookCode, evnt.state, " .
            "t_lang.langName AS tLang, s_lang.langName AS sLang, " .
            "book_info.name AS bookName, book_info.sort, " .
            "projects.sourceLangID, projects.bookProject, " .
            "projects.tnLangID, projects.tqLangID, projects.twLangID, " .
            "projects.sourceBible, projects.gwLang, " .
            "projects.targetLang, projects.resLangID, " .
            "t_lang.direction as tLangDir, s_lang.direction as sLangDir, evnt.langInput, " .
            "chapters.chunks, projects.projectID " .
            "FROM " . PREFIX . "translators AS trs " .
            "LEFT JOIN " . PREFIX . "chapters AS chapters ON trs.eventID = chapters.eventID AND trs.currentChapter = chapters.chapter " .
            "LEFT JOIN " . PREFIX . "members as members ON trs.memberID = members.memberID " .
            "LEFT JOIN " . PREFIX . "events AS evnt ON evnt.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "projects AS projects ON projects.projectID = evnt.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info AS book_info ON evnt.bookCode = book_info.code " .
            "WHERE trs.trID > 0 " .
            "AND (projects.bookProject IN ('ulb','udb') " .
            ($eventID ? "AND trs.eventID = :eventID " : " ") .
            ($trMemberID ? "AND trs.memberID = :trMemberID " : " ") . ") ".
            "ORDER BY tLang, book_info.sort";

        $events = $this->db->select($sql, $prepare);

        $filtered = [];
        $eventAdmins = [];

        foreach ($events as $event) {
            if (empty($eventAdmins)) {
                $eventAdmins = $this->eventRepo->get($event->eventID)->admins;
            }
            $event->admins = $eventAdmins;

            $peerCheck = (array)json_decode($event->peerCheck, true);
            $kwCheck = (array)json_decode($event->kwCheck, true);
            $crCheck = (array)json_decode($event->crCheck, true);

            foreach ($peerCheck as $chap => $data) {
                // Exclude translator's events
                if ($event->memberID == $memberID) continue;
                // Exclude finished events
                if ($data["done"] > 0) continue;
                // Exclude other checkers events
                if ($data["memberID"] != $memberID) continue;
                // Filter to secific chapter
                if ($chapter && $chapter != $chap) continue;

                $chapters = $this->getChapters($event->eventID, $event->memberID, $chapter); // Should be one
                $chunks = !empty($chapters) ? $chapters[0]["chunks"] : "";

                $ev = clone $event;
                $ev->step = EventSteps::PEER_REVIEW;
                $ev->currentChapter = $chap;
                $ev->checkerID = $data["memberID"];
                $ev->chunks = $chunks;
                $filtered[] = $ev;
            }

            foreach ($kwCheck as $chap => $data) {
                // Exclude translator's events
                if ($event->memberID == $memberID) continue;
                // Exclude finished events
                if ($data["done"] > 0) continue;
                // Exclude other checkers events
                if ($data["memberID"] != $memberID) continue;
                // Filter to secific chapter
                if ($chapter && $chapter != $chap) continue;

                $chapters = $this->getChapters($event->eventID, $event->memberID, $chapter); // Should be one
                $chunks = !empty($chapters) ? $chapters[0]["chunks"] : "";

                $ev = clone $event;
                $ev->step = EventSteps::KEYWORD_CHECK;
                $ev->currentChapter = $chap;
                $ev->checkerID = $data["memberID"];
                $ev->chunks = $chunks;
                $filtered[] = $ev;
            }

            foreach ($crCheck as $chap => $data) {
                // Exclude translator's events
                if ($event->memberID == $memberID) continue;
                // Exclude finished events
                if ($data["done"] > 0) continue;
                // Exclude other checkers events
                if ($data["memberID"] != $memberID) continue;
                // Filter to secific chapter
                if ($chapter && $chapter != $chap) continue;

                $chapters = $this->getChapters($event->eventID, $event->memberID, $chapter); // Should be one
                $chunks = !empty($chapters) ? $chapters[0]["chunks"] : "";

                $ev = clone $event;
                $ev->step = EventSteps::CONTENT_REVIEW;
                $ev->currentChapter = $chap;
                $ev->checkerID = $data["memberID"];
                $ev->chunks = $chunks;
                $filtered[] = $ev;
            }
        }

        return $filtered;
    }

    /**
     * Get Notes checker event/s
     * @param $memberID Notes Checker member ID
     * @param null $eventID event ID
     * @param null $chkMemberID Notes translator member ID
     * @param $chapter
     * @return array
     */
    public function getMemberEventsForNotes($memberID, $eventID = null, $chkMemberID = null, $chapter = null)
    {
        $prepare = [];
        if ($eventID)
            $prepare[":eventID"] = $eventID;
        if ($chkMemberID)
            $prepare[":chkMemberID"] = $chkMemberID;

        $sql = "SELECT trs.*, " . PREFIX . "members.userName, " . PREFIX . "members.firstName, "
            . PREFIX . "members.lastName, evnt.bookCode, "
            . "evnt.dateFrom, evnt.dateTo, evnt.state, "
            . "t_lang.langName AS tLang, s_lang.langName AS sLang, "
            . PREFIX . "book_info.name AS name, " . PREFIX . "book_info.sort, "
            . PREFIX . "projects.sourceLangID, " . PREFIX . "projects.bookProject, "
            . PREFIX . "projects.sourceBible, " . PREFIX . "projects.gwLang, "
            . PREFIX . "projects.tnLangID, " . PREFIX . "projects.tqLangID, " . PREFIX . "projects.twLangID, "
            . PREFIX . "projects.targetLang, " . PREFIX . "projects.resLangID, res_lang.direction as resLangDir, " .
            "t_lang.direction as tLangDir, s_lang.direction as sLangDir, "
            . PREFIX . "book_info.chaptersNum, " . PREFIX . "projects.projectID " .
            "FROM " . PREFIX . "translators AS trs " .
            "LEFT JOIN " . PREFIX . "members ON trs.memberID = " . PREFIX . "members.memberID " .
            "LEFT JOIN " . PREFIX . "events AS evnt ON evnt.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "projects ON " . PREFIX . "projects.projectID = evnt.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON " . PREFIX . "projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON " . PREFIX . "projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS res_lang ON " . PREFIX . "projects.resLangID = res_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info ON evnt.bookCode = " . PREFIX . "book_info.code " .
            "WHERE " . PREFIX . "projects.bookProject = 'tn' " .
            ($eventID ? "AND trs.eventID = :eventID " : " ") .
            ($chkMemberID ? "AND trs.memberID = :chkMemberID " : " ") .
            "ORDER BY tLang, " . PREFIX . "book_info.sort";

        $events = $this->db->select($sql, $prepare);
        $filtered = [];
        $eventAdmins = [];

        foreach ($events as $event) {
            // Checker event
            if (empty($eventAdmins)) $eventAdmins = $this->eventRepo->get($event->eventID)->admins;
            $event->admins = $eventAdmins;
            $otherCheck = (array)json_decode($event->otherCheck, true);
            $peerCheck = (array)json_decode($event->peerCheck, true);
            foreach ($otherCheck as $chap => $data) {
                if (!isset($chapter) || $chapter == $chap) {
                    if ($data["memberID"] == $memberID && $data["done"] != 6) {
                        $ev = clone $event;

                        $checkerFName = null;
                        $checkerLName = null;
                        $checkerID = 0;
                        $ev->step = EventSteps::PRAY;
                        $ev->checkDone = false;

                        if (isset($peerCheck[$chap]) && $peerCheck[$chap]["memberID"] != 0) {
                            $memberModel = new MembersModel();
                            $member = $memberModel->getMember([
                                "firstName",
                                "lastName"
                            ], ["memberID", $peerCheck[$chap]["memberID"]]);
                            if (!empty($member)) {
                                $checkerFName = $member[0]->firstName;
                                $checkerLName = $member[0]->lastName;
                                $checkerID = $peerCheck[$chap]["memberID"];
                            }

                            $ev->checkDone = $peerCheck[$chap]["done"] > 0;
                        }

                        switch ($data["done"]) {
                            case 1:
                                $ev->step = EventSteps::CONSUME;
                                break;
                            case 2:
                                $ev->step = EventSteps::HIGHLIGHT;
                                break;
                            case 3:
                                $ev->step = EventSteps::SELF_CHECK;
                                break;
                            case 4:
                                $ev->step = EventSteps::KEYWORD_CHECK;
                                break;
                            case 5:
                                $ev->step = EventSteps::PEER_REVIEW;
                                break;
                        }

                        $ev->currentChapter = $chap;
                        $ev->peer = 1;
                        $ev->myMemberID = 0;
                        $ev->myChkMemberID = $memberID;
                        $ev->checkerFName = $checkerFName;
                        $ev->checkerLName = $checkerLName;
                        $ev->checkerID = $checkerID;
                        $ev->isContinue = true; // Means not owner of chapter
                        $ev->isCheckerPage = true;
                        $filtered[] = $ev;
                    }
                }
            }

            // Peer check event
            foreach ($peerCheck as $chap => $data) {
                if (!isset($chapter) || $chapter == $chap) {
                    if ($data["memberID"] == $memberID && $data["done"] == 0) {
                        $ev = clone $event;
                        $checkerFName = null;
                        $checkerLName = null;
                        $checkerID = 0;

                        $memberModel = new MembersModel();
                        $member = $memberModel->getMember([
                            "firstName",
                            "lastName"
                        ], ["memberID", $otherCheck[$chap]["memberID"]]);
                        if (!empty($member)) {
                            $checkerFName = $member[0]->firstName;
                            $checkerLName = $member[0]->lastName;
                            $checkerID = $otherCheck[$chap]["memberID"];
                        }

                        $ev->step = EventSteps::PEER_REVIEW;
                        $ev->currentChapter = $chap;
                        $ev->peer = 2;
                        $ev->myMemberID = $memberID;
                        $ev->myChkMemberID = $memberID;
                        $ev->checkerFName = $checkerFName;
                        $ev->checkerLName = $checkerLName;
                        $ev->checkerID = $checkerID;
                        $ev->isContinue = true;
                        $ev->isCheckerPage = true;
                        $filtered[] = $ev;
                    }
                }
            }
        }

        return $filtered;
    }

    /**
     * Get Questions checker event/s
     * @param $memberID Notes Checker member ID
     * @param null $eventID event ID
     * @param null $chkMemberID Notes translator member ID
     * @param $chapter
     * @return array
     */
    public function getMemberEventsForOther($memberID, $eventID = null, $chkMemberID = null, $chapter = null)
    {
        $prepare = [];
        if ($eventID)
            $prepare[":eventID"] = $eventID;
        if ($chkMemberID)
            $prepare[":chkMemberID"] = $chkMemberID;

        $sql = "SELECT trs.*, " . PREFIX . "members.userName, " . PREFIX . "members.firstName, "
            . PREFIX . "members.lastName, evnt.bookCode, "
            . "evnt.dateFrom, evnt.dateTo, evnt.state, "
            . "t_lang.langName AS tLang, s_lang.langName AS sLang, "
            . PREFIX . "book_info.name AS name, " . PREFIX . "book_info.sort, "
            . PREFIX . "projects.sourceLangID, " . PREFIX . "projects.bookProject, "
            . PREFIX . "projects.sourceBible, " . PREFIX . "projects.gwLang, "
            . PREFIX . "projects.tnLangID, " . PREFIX . "projects.tqLangID, " . PREFIX . "projects.twLangID, "
            . PREFIX . "projects.targetLang, " . PREFIX . "projects.resLangID, res_lang.direction as resLangDir, " .
            "t_lang.direction as tLangDir, s_lang.direction as sLangDir, "
            . PREFIX . "book_info.chaptersNum, " . PREFIX . "projects.projectID, "
            . PREFIX . "tw_groups.words " .
            "FROM " . PREFIX . "translators AS trs " .
            "LEFT JOIN " . PREFIX . "members ON trs.memberID = " . PREFIX . "members.memberID " .
            "LEFT JOIN " . PREFIX . "events AS evnt ON evnt.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "projects ON " . PREFIX . "projects.projectID = evnt.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON " . PREFIX . "projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON " . PREFIX . "projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS res_lang ON " . PREFIX . "projects.resLangID = res_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info ON evnt.bookCode = " . PREFIX . "book_info.code " .
            "LEFT JOIN " . PREFIX . "tw_groups ON trs.currentChapter = " . PREFIX . "tw_groups.groupID " .
            "WHERE " . PREFIX . "projects.bookProject IN ('tq','tw','obs') " .
            ($eventID ? "AND trs.eventID = :eventID " : " ") .
            ($chkMemberID ? "AND trs.memberID = :chkMemberID " : " ") .
            "ORDER BY tLang, " . PREFIX . "book_info.sort";

        $events = $this->db->select($sql, $prepare);
        $filtered = [];
        $eventAdmins = [];

        foreach ($events as $event) {
            // Checker event
            if (empty($eventAdmins)) $eventAdmins = $this->eventRepo->get($event->eventID)->admins;
            $event->admins = $eventAdmins;
            $otherCheck = (array)json_decode($event->otherCheck, true);
            $peerCheck = (array)json_decode($event->peerCheck, true);
            foreach ($otherCheck as $chap => $data) {
                if (!isset($chapter) || $chapter == $chap) {
                    if ($data["memberID"] == $memberID && $data["done"] != 3) {
                        if ($event->bookProject == "tw") {
                            $group = $this->getTwGroups([
                                "groupID" => $chap,
                                "eventID" => $event->eventID]);

                            $event->words = $group[0]->words;
                        }

                        $ev = clone $event;

                        $checkerFName = null;
                        $checkerLName = null;
                        $checkerID = 0;
                        $ev->step = EventSteps::PRAY;
                        $ev->checkDone = false;

                        if (isset($peerCheck[$chap]) && $peerCheck[$chap]["memberID"] != 0) {
                            $memberModel = new MembersModel();
                            $member = $memberModel->getMember([
                                "firstName",
                                "lastName"
                            ], ["memberID", $peerCheck[$chap]["memberID"]]);
                            if (!empty($member)) {
                                $checkerFName = $member[0]->firstName;
                                $checkerLName = $member[0]->lastName;
                                $checkerID = $peerCheck[$chap]["memberID"];
                            }

                            $ev->checkDone = $peerCheck[$chap]["done"] > 0;
                        }

                        switch ($data["done"]) {
                            case 1:
                                $ev->step = EventSteps::KEYWORD_CHECK;
                                break;
                            case 2:
                                $ev->step = EventSteps::PEER_REVIEW;
                                break;
                        }

                        $ev->currentChapter = $chap;
                        $ev->peer = 1;
                        $ev->myMemberID = 0;
                        $ev->myChkMemberID = $memberID;
                        $ev->checkerFName = $checkerFName;
                        $ev->checkerLName = $checkerLName;
                        $ev->checkerID = $checkerID;
                        $ev->isContinue = true; // Means not owner of chapter
                        $ev->isCheckerPage = true;
                        $filtered[] = $ev;
                    }
                }
            }

            // Peer check event
            foreach ($peerCheck as $chap => $data) {
                if (!isset($chapter) || $chapter == $chap) {
                    if ($data["memberID"] == $memberID && $data["done"] == 0) {
                        if ($event->bookProject == "tw") {
                            $group = $this->getTwGroups([
                                "groupID" => $chap,
                                "eventID" => $event->eventID]);

                            $event->words = $group[0]->words;
                        }

                        $ev = clone $event;
                        $checkerFName = null;
                        $checkerLName = null;
                        $checkerID = 0;

                        $memberModel = new MembersModel();
                        $member = $memberModel->getMember([
                            "firstName",
                            "lastName"
                        ], ["memberID", $otherCheck[$chap]["memberID"]]);
                        if (!empty($member)) {
                            $checkerFName = $member[0]->firstName;
                            $checkerLName = $member[0]->lastName;
                            $checkerID = $otherCheck[$chap]["memberID"];
                        }

                        $ev->step = EventSteps::PEER_REVIEW;
                        $ev->currentChapter = $chap;
                        $ev->peer = 2;
                        $ev->myMemberID = $memberID;
                        $ev->myChkMemberID = $memberID;
                        $ev->checkerFName = $checkerFName;
                        $ev->checkerLName = $checkerLName;
                        $ev->checkerID = $checkerID;
                        $ev->isContinue = true;
                        $ev->isCheckerPage = true;
                        $filtered[] = $ev;
                    }
                }
            }
        }

        return $filtered;
    }

    /**
     * Get tW groups
     * @param $where
     * @return mixed
     */
    public function getTwGroups($where)
    {
        return $this->db->table("tw_groups")
            ->where($where)
            ->orderBy("groupID")
            ->get();
    }

    /**
     * Get Radio checker event/s
     * @param $memberID Notes Checker member ID
     * @param null $eventID event ID
     * @param null $chkMemberID Notes translator member ID
     * @param $chapter
     * @return array
     */
    public function getMemberEventsForRadio($memberID, $eventID = null, $chkMemberID = null, $chapter = null)
    {
        $prepare = [];
        if ($eventID)
            $prepare[":eventID"] = $eventID;
        if ($chkMemberID)
            $prepare[":chkMemberID"] = $chkMemberID;

        $sql = "SELECT trs.*, " . PREFIX . "members.userName, " . PREFIX . "members.firstName, "
            . PREFIX . "members.lastName, evnt.bookCode, "
            . "evnt.dateFrom, evnt.dateTo, evnt.state, "
            . "t_lang.langName AS tLang, s_lang.langName AS sLang, "
            . PREFIX . "book_info.name AS name, " . PREFIX . "book_info.sort, "
            . PREFIX . "projects.sourceLangID, " . PREFIX . "projects.bookProject, "
            . PREFIX . "projects.sourceBible, " . PREFIX . "projects.gwLang, "
            . PREFIX . "projects.tnLangID, " . PREFIX . "projects.tqLangID, " . PREFIX . "projects.twLangID, "
            . PREFIX . "projects.targetLang, " . PREFIX . "projects.resLangID, res_lang.direction as resLangDir, " .
            "t_lang.direction as tLangDir, s_lang.direction as sLangDir, "
            . PREFIX . "book_info.chaptersNum, " . PREFIX . "projects.projectID " .
            "FROM " . PREFIX . "translators AS trs " .
            "LEFT JOIN " . PREFIX . "members ON trs.memberID = " . PREFIX . "members.memberID " .
            "LEFT JOIN " . PREFIX . "events AS evnt ON evnt.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "projects ON " . PREFIX . "projects.projectID = evnt.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON " . PREFIX . "projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON " . PREFIX . "projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS res_lang ON " . PREFIX . "projects.resLangID = res_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info ON evnt.bookCode = " . PREFIX . "book_info.code " .
            "WHERE " . PREFIX . "projects.bookProject = 'rad' " .
            ($eventID ? "AND trs.eventID = :eventID " : " ") .
            ($chkMemberID ? "AND trs.memberID = :chkMemberID " : " ") .
            "ORDER BY tLang, " . PREFIX . "book_info.sort";

        $events = $this->db->select($sql, $prepare);
        $filtered = [];
        $eventAdmins = [];

        foreach ($events as $event) {
            // Checker event
            if (empty($eventAdmins)) $eventAdmins = $this->eventRepo->get($event->eventID)->admins;
            $event->admins = $eventAdmins;
            $peerCheck = (array)json_decode($event->peerCheck, true);
            foreach ($peerCheck as $chap => $data) {
                if (!isset($chapter) || $chapter == $chap) {
                    if ($data["memberID"] == $memberID && $data["done"] != 1) {
                        $ev = clone $event;

                        $ev->step = EventSteps::PEER_REVIEW;
                        $ev->currentChapter = $chap;
                        $ev->myMemberID = 0;
                        $ev->checkerID = 0;
                        $ev->checkDone = 0;
                        $ev->myChkMemberID = $memberID;
                        $ev->isContinue = true; // Means not owner of chapter
                        $ev->isCheckerPage = true;
                        $filtered[] = $ev;
                    }
                }
            }
        }

        return $filtered;
    }


    /**
     * Get all events of a revision checker or specific event
     * @param $memberID
     * @param null $eventID
     * @return array
     */
    public function getCheckerRevisionEvents($memberID, $eventID = null)
    {
        $sql = "SELECT checkers.*, " .
            "(SELECT COUNT(*) FROM ".PREFIX."checkers_l2 AS all_chkrs WHERE all_chkrs.eventID = checkers.eventID ) AS currChkrs, " .
            "evnt.eventID, evnt.state, evnt.bookCode, evnt.dateFrom, evnt.langInput, " .
            "evnt.dateTo, " .
            "projects.projectID, projects.bookProject, " .
            "projects.sourceLangID, projects.gwLang, " .
            "projects.tnLangID, projects.tqLangID, projects.twLangID, " .
            "projects.targetLang, projects.glID, " .
            "projects.sourceBible, t_lang.langName as tLang, chapters.chunks, " .
            "t_lang.direction as tLangDir, projects.resLangID, res_lang.direction as resLangDir, " .
            "s_lang.langName as sLang, s_lang.direction as sLangDir, " .
            "book_info.name, book_info.sort, book_info.chaptersNum " .
            "FROM " . PREFIX . "checkers_l2 AS checkers " .
            "LEFT JOIN " . PREFIX . "chapters AS chapters ON checkers.eventID = chapters.eventID AND checkers.currentChapter = chapters.chapter " .
            "LEFT JOIN " . PREFIX . "events AS evnt ON checkers.eventID = evnt.eventID " .
            "LEFT JOIN " . PREFIX . "projects AS projects ON evnt.projectID = projects.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS res_lang ON projects.resLangID = res_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info AS book_info ON evnt.bookCode = book_info.code " .
            "WHERE checkers.eventID !='' " .
            (!is_null($memberID) ? " AND checkers.memberID = :memberID " : " ") .
            (!is_null($eventID) ? " AND checkers.eventID=:eventID " : " ") .
            "ORDER BY tLang, projects.bookProject, book_info.sort";

        $prepare = array();
        if (!is_null($memberID))
            $prepare[":memberID"] = $memberID;

        if (!is_null($eventID))
            $prepare[":eventID"] = $eventID;

        return $this->db->select($sql, $prepare);
    }


    /**
     * Get all events of a member or specific event
     * @param $memberID
     * @param null $eventID
     * @param null $chapter
     * @param bool $includeCheckers
     * @param bool $includeFinished
     * @param bool $includeNone
     * @return array
     */
    public function getRevisionMemberEvents(
        $memberID,
        $eventID = null,
        $chapter = null,
        $includeCheckers = false,
        $includeNone = true
    ) {
        $sql = "SELECT chks.*, chks.memberID AS myMemberID, members.userName, members.firstName, " .
            "members.lastName, evnt.bookCode, evnt.state, " .
            "evnt.dateFrom, evnt.dateTo, " .
            "t_lang.langName AS tLang, s_lang.langName AS sLang, " .
            "book_info.name AS name, book_info.sort, " .
            "projects.sourceLangID, projects.bookProject, " .
            "projects.tnLangID, projects.tqLangID, projects.twLangID, " .
            "projects.sourceBible, projects.gwLang, " .
            "projects.targetLang, projects.resLangID, " .
            "t_lang.direction as tLangDir, s_lang.direction as sLangDir, " .
            "book_info.chaptersNum, projects.projectID, " .
            "chapters.chunks " .
            "FROM " . PREFIX . "checkers_l2 AS chks " .
            "LEFT JOIN " . PREFIX . "chapters AS chapters ON chks.eventID = chapters.eventID AND chks.currentChapter = chapters.chapter ".
            "LEFT JOIN " . PREFIX . "members AS members ON chks.memberID = members.memberID " .
            "LEFT JOIN " . PREFIX . "events AS evnt ON evnt.eventID = chks.eventID " .
            "LEFT JOIN " . PREFIX . "projects AS projects ON projects.projectID = evnt.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info AS book_info ON evnt.bookCode = book_info.code " .
            "WHERE chks.l2chID != 0 " .
            ($memberID ? " AND chks.memberID = :memberID " : " ") .
            ($eventID ? "AND chks.eventID = :eventID " : " ") .
            "ORDER BY tLang, book_info.sort";

        $prepare = array();
        if (!is_null($memberID))
            $prepare[":memberID"] = $memberID;

        if (!is_null($eventID))
            $prepare[":eventID"] = $eventID;

        $events = $this->db->select($sql, $prepare);
        $filtered = [];
        $eventAdmins = [];

        foreach ($events as $event) {
            if (empty($eventAdmins)) {
                $eventAdmins = $this->eventRepo->get($event->eventID)->admins;
            }
            $event->admins = $eventAdmins;

            $checkingSteps = [
                EventCheckSteps::PEER_REVIEW,
                EventCheckSteps::KEYWORD_CHECK,
                EventCheckSteps::CONTENT_REVIEW
            ];
            $excludedSteps = [];
            if (!$includeNone) {
                $excludedSteps[] = EventCheckSteps::NONE;
            }
            $checkingSteps = array_merge($checkingSteps, $excludedSteps);
            $inChecking = !in_array($event->step, $checkingSteps);

            if ($inChecking && !isset($chapter)) {
                $event->checkerID = 0;
                $filtered[] = $event;
            }

            $peerCheck = (array)json_decode($event->peerCheck, true);
            $kwCheck = (array)json_decode($event->kwCheck, true);
            $crCheck = (array)json_decode($event->crCheck, true);

            foreach ($peerCheck as $chap => $data) {
                if ($data["done"] < 2 && $includeCheckers && (!isset($chapter) || $chapter == $chap)) {
                    $ev = $this->modifiedCheckerEvent($event, $chap, $data, EventCheckSteps::PEER_REVIEW, "l2");
                    $filtered[] = $ev;
                }
            }

            foreach ($kwCheck as $chap => $data) {
                if ($data["done"] < 2 && $includeCheckers && (!isset($chapter) || $chapter == $chap)) {
                    $ev = $this->modifiedCheckerEvent($event, $chap, $data, EventCheckSteps::KEYWORD_CHECK, "l2");
                    $filtered[] = $ev;
                }
            }

            foreach ($crCheck as $chap => $data) {
                if ($data["done"] < 2 && $includeCheckers && (!isset($chapter) || $chapter == $chap)) {
                    $ev = $this->modifiedCheckerEvent($event, $chap, $data, EventCheckSteps::CONTENT_REVIEW, "l2");
                    $filtered[] = $ev;
                }
            }
        }

        return $filtered;
    }


    /**
     * Get approving revision checker event/s
     * @param int $memberID Approver Checker member ID
     * @param null $eventID
     * @param null $chkMemberID 1st Checker member ID
     * @param null $chapter
     * @return array
     */
    public function getMemberEventsForRevisionChecker($memberID, $eventID = null, $chkMemberID = null, $chapter = null)
    {
        $prepare = [];
        if ($eventID)
            $prepare[":eventID"] = $eventID;
        if ($chkMemberID)
            $prepare[":chkMemberID"] = $chkMemberID;

        $sql = "SELECT chks.*, chks.memberID AS checker_l2, members.userName, members.firstName, " .
            "members.lastName, evnt.bookCode, evnt.state, " .
            "evnt.dateFrom, evnt.dateTo, " .
            "t_lang.langName AS tLang, s_lang.langName AS sLang, " .
            "book_info.name AS name, book_info.sort, " .
            "projects.sourceLangID, projects.bookProject, " .
            "projects.tnLangID, projects.tqLangID, projects.twLangID, " .
            "projects.sourceBible, projects.gwLang, " .
            "projects.targetLang, projects.resLangID, " .
            "t_lang.direction as tLangDir, s_lang.direction as sLangDir, " .
            "book_info.chaptersNum, projects.projectID, " .
            "chapters.chunks " .
            "FROM " . PREFIX . "checkers_l2 AS chks " .
            "LEFT JOIN " . PREFIX . "chapters AS chapters ON chks.eventID = chapters.eventID AND chks.currentChapter = chapters.chapter ".
            "LEFT JOIN " . PREFIX . "members AS members ON chks.memberID = members.memberID " .
            "LEFT JOIN " . PREFIX . "events AS evnt ON evnt.eventID = chks.eventID " .
            "LEFT JOIN " . PREFIX . "projects AS projects ON projects.projectID = evnt.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info AS book_info ON evnt.bookCode = book_info.code " .
            "WHERE chks.l2chID != 0 " .
            ($eventID ? "AND chks.eventID = :eventID " : " ") .
            ($chkMemberID ? "AND chks.memberID = :chkMemberID " : " ") .
            "ORDER BY tLang, book_info.sort";

        $events = $this->db->select($sql, $prepare);
        $filtered = [];
        $eventAdmins = [];

        foreach ($events as $event) {
            if (empty($eventAdmins[$event->eventID])) {
                $eventAdmins[$event->eventID] = $this->eventRepo->get($event->eventID)->admins;
            }
            $event->admins = $eventAdmins[$event->eventID];

            $peerCheck = (array)json_decode($event->peerCheck, true);
            foreach ($peerCheck as $chap => $data) {
                // Exclude finished events
                if ($data["done"] > 0) continue;
                // Exclude other checkers events
                if ($data["memberID"] != $memberID) continue;
                // Filter to specific chapter
                if ($chapter && $chapter != $chap) continue;

                $chapters = $this->getChapters($event->eventID, $event->memberID, $chapter); // Should be one
                $chunks = !empty($chapters) ? $chapters[0]["chunks"] : "";

                $ev = clone $event;
                $ev->step = EventCheckSteps::PEER_REVIEW;
                $ev->currentChapter = $chap;
                $ev->checkerID = $data["memberID"];
                $ev->chunks = $chunks;
                $filtered[] = $ev;
            }

            $kwCheck = (array)json_decode($event->kwCheck, true);
            foreach ($kwCheck as $chap => $data) {
                // Exclude translator's events
                if ($event->memberID == $memberID) continue;
                // Exclude finished events
                if ($data["done"] > 0) continue;
                // Exclude other checkers events
                if ($data["memberID"] != $memberID) continue;
                // Filter to secific chapter
                if ($chapter && $chapter != $chap) continue;

                $chapters = $this->getChapters($event->eventID, $event->memberID, $chapter); // Should be one
                $chunks = !empty($chapters) ? $chapters[0]["chunks"] : "";

                $ev = clone $event;
                $ev->step = EventSteps::KEYWORD_CHECK;
                $ev->currentChapter = $chap;
                $ev->checkerID = $data["memberID"];
                $ev->chunks = $chunks;
                $filtered[] = $ev;
            }

            $crCheck = (array)json_decode($event->crCheck, true);
            foreach ($crCheck as $chap => $data) {
                // Exclude translator's events
                if ($event->memberID == $memberID) continue;
                // Exclude finished events
                if ($data["done"] > 0) continue;
                // Exclude other checkers events
                if ($data["memberID"] != $memberID) continue;
                // Filter to secific chapter
                if ($chapter && $chapter != $chap) continue;

                $chapters = $this->getChapters($event->eventID, $event->memberID, $chapter); // Should be one
                $chunks = !empty($chapters) ? $chapters[0]["chunks"] : "";

                $ev = clone $event;
                $ev->step = EventSteps::CONTENT_REVIEW;
                $ev->currentChapter = $chap;
                $ev->checkerID = $data["memberID"];
                $ev->chunks = $chunks;
                $filtered[] = $ev;
            }
        }

        return $filtered;
    }


    /**
     * Get all events of a L3 checker or specific event
     * @param $memberID
     * @param null $eventID
     * @return array
     */
    public function getCheckerL3Events($memberID, $eventID = null)
    {
        $sql = "SELECT checkers.l3chID, checkers.memberID, checkers.step, " .
            "checkers.currentChapter, checkers.peerCheck, " .
            "(SELECT COUNT(*) FROM ".PREFIX."checkers_l3 AS all_chkrs WHERE all_chkrs.eventID = checkers.eventID ) AS currChkrs, " .
            "evnt.eventID, evnt.state, evnt.bookCode, evnt.dateFrom, evnt.langInput, " .
            "evnt.dateTo, " .
            "projects.projectID, projects.bookProject, " .
            "projects.sourceLangID, projects.gwLang, " .
            "projects.tnLangID, projects.tqLangID, projects.twLangID, " .
            "projects.targetLang, projects.glID, " .
            "projects.sourceBible, t_lang.langName as tLang, chapters.chunks, " .
            "t_lang.direction as tLangDir, projects.resLangID, res_lang.direction as resLangDir, " .
            "s_lang.langName as sLang, s_lang.direction as sLangDir, " .
            "book_info.name, book_info.sort, book_info.chaptersNum " .
            "FROM " . PREFIX . "checkers_l3 AS checkers " .
            "LEFT JOIN " . PREFIX . "chapters AS chapters ON checkers.eventID = chapters.eventID AND checkers.currentChapter = chapters.chapter " .
            "LEFT JOIN " . PREFIX . "events AS evnt ON checkers.eventID = evnt.eventID " .
            "LEFT JOIN " . PREFIX . "projects AS projects ON evnt.projectID = projects.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS res_lang ON projects.resLangID = res_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info AS book_info ON evnt.bookCode = book_info.code " .
            "WHERE checkers.eventID !='' " .
            (!is_null($memberID) ? " AND checkers.memberID = :memberID " : " ") .
            (!is_null($eventID) ? " AND checkers.eventID=:eventID " : " ") .
            "ORDER BY tLang, projects.bookProject, book_info.sort";

        $prepare = array();
        if (!is_null($memberID))
            $prepare[":memberID"] = $memberID;

        if (!is_null($eventID))
            $prepare[":eventID"] = $eventID;

        return $this->db->select($sql, $prepare);
    }


    /**
     * Get L3 checker event/s
     * @param $memberID 1st Checker member ID
     * @param null $eventID
     * @param null $chkMemberID Peer checker
     * @param null $chapter
     * @return array
     */
    public function getMemberEventsForCheckerL3($memberID, $eventID = null, $chkMemberID = null, $chapter = null)
    {
        $prepare = [];
        if ($eventID)
            $prepare[":eventID"] = $eventID;
        if ($chkMemberID)
            $prepare[":chkMemberID"] = $chkMemberID;

        $sql = "SELECT chks.*, members.userName, members.firstName, " .
            "members.lastName, evnt.bookCode, evnt.state, " .
            "evnt.dateFrom, evnt.dateTo, " .
            "t_lang.langName AS tLang, s_lang.langName AS sLang, " .
            "book_info.name AS name, book_info.sort, " .
            "projects.sourceLangID, projects.bookProject, " .
            "projects.tnLangID, projects.tqLangID, projects.twLangID, " .
            "projects.sourceBible, projects.gwLang, projects.glID, " .
            "projects.targetLang, projects.resLangID, " .
            "t_lang.direction as tLangDir, s_lang.direction as sLangDir, " .
            "book_info.chaptersNum, projects.projectID, " .
            "chapters.chunks " .
            "FROM " . PREFIX . "checkers_l3 AS chks " .
            "LEFT JOIN " . PREFIX . "chapters AS chapters ON chks.eventID = chapters.eventID AND chks.currentChapter = chapters.chapter ".
            "LEFT JOIN " . PREFIX . "members AS members ON chks.memberID = members.memberID " .
            "LEFT JOIN " . PREFIX . "events AS evnt ON evnt.eventID = chks.eventID " .
            "LEFT JOIN " . PREFIX . "projects AS projects ON projects.projectID = evnt.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info AS book_info ON evnt.bookCode = book_info.code " .
            "WHERE chks.l3chID != 0 " .
            ($eventID ? "AND chks.eventID = :eventID " : " ") .
            ($chkMemberID ? "AND chks.memberID = :chkMemberID " : " ") .
            "ORDER BY tLang, book_info.sort";

        $events = $this->db->select($sql, $prepare);
        $filtered = [];
        $eventAdmins = new Collection();

        foreach ($events as $event) {
            // First Checker events
            if ($eventAdmins->isEmpty()) {
                $eventAdmins = $this->eventRepo->get($event->eventID)->admins;
            }

            $event->admins = $eventAdmins;
            if ($event->memberID == $memberID
                && $event->step != EventCheckSteps::NONE
                && ($chapter == null || $chapter == $event->currentChapter)) {
                $filtered[] = $event;
            }

            // Peer Check events
            $peerCheck = (array)json_decode($event->peerCheck, true);
            foreach ($peerCheck as $chap => $data) {
                if (!isset($chapter) || $chapter == $chap) {
                    if ($data["memberID"] == $memberID && $data["done"] != 2) {
                        $ev = clone $event;

                        $memberModel = new MembersModel();
                        $member = $memberModel->getMember([
                            "firstName",
                            "lastName"
                        ], ["memberID", $ev->memberID]);
                        $checkerFName = $member[0]->firstName;
                        $checkerLName = $member[0]->lastName;

                        $ev->peerStep = $ev->step;
                        $ev->step = $data["done"] == 0 ?
                            EventCheckSteps::PEER_REVIEW_L3 :
                            EventCheckSteps::PEER_EDIT_L3;
                        $ev->currentChapter = $chap;
                        $ev->l3memberID = $ev->memberID;
                        $ev->myMemberID = 0;
                        $ev->myChkMemberID = $memberID;
                        $ev->chkMemberID = $ev->l3memberID;
                        $ev->checkerFName = $checkerFName;
                        $ev->checkerLName = $checkerLName;
                        $ev->isContinue = true; // Means not owner of chapter
                        $filtered[] = $ev;
                    }
                }
            }
        }

        return $filtered;
    }


    /**
     * Get SUN checker event/s
     * @param $checkerID Checker member ID
     * @param null $eventID event ID
     * @param null $memberID Translator member ID
     * @return array
     */
    public function getMemberEventsForCheckerSun($checkerID, $eventID = null, $memberID = null, $chapter = null)
    {
        $prepare = [];
        if ($eventID)
            $prepare[":eventID"] = $eventID;
        if ($memberID)
            $prepare[":memberID"] = $memberID;

        $sql = "SELECT trs.*, " . PREFIX . "members.userName, " . PREFIX . "members.firstName, "
            . PREFIX . "members.lastName, evnt.bookCode, evnt.state, "
            . "evnt.dateFrom, evnt.dateTo, "
            . "t_lang.langName AS tLang, s_lang.langName AS sLang, "
            . PREFIX . "book_info.name AS name, " . PREFIX . "book_info.sort, "
            . PREFIX . "projects.sourceLangID, " . PREFIX . "projects.bookProject, "
            . PREFIX . "projects.sourceBible, " . PREFIX . "projects.gwLang, "
            . PREFIX . "projects.targetLang, " . PREFIX . "projects.resLangID, " .
            "t_lang.direction as tLangDir, s_lang.direction as sLangDir, "
            . PREFIX . "book_info.chaptersNum, " . PREFIX . "projects.projectID " .
            "FROM " . PREFIX . "translators AS trs " .
            "LEFT JOIN " . PREFIX . "members ON trs.memberID = " . PREFIX . "members.memberID " .
            "LEFT JOIN " . PREFIX . "events AS evnt ON evnt.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "projects ON " . PREFIX . "projects.projectID = evnt.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON " . PREFIX . "projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON " . PREFIX . "projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info ON evnt.bookCode = " . PREFIX . "book_info.code " .
            "WHERE " . PREFIX . "projects.bookProject = 'sun' AND trs.kwCheck != '' " .
            ($eventID ? "AND trs.eventID = :eventID " : " ") .
            ($memberID ? "AND trs.memberID = :memberID " : " ") .
            "ORDER BY tLang, " . PREFIX . "book_info.sort";

        $events = $this->db->select($sql, $prepare);
        $filtered = [];
        $eventAdmins = [];

        foreach ($events as $event) {
            // Theo Check events
            if (empty($eventAdmins)) $eventAdmins = $this->eventRepo->get($event->eventID)->admins;
            $event->admins = $eventAdmins;
            $kwCheck = (array)json_decode($event->kwCheck, true);
            foreach ($kwCheck as $chap => $data) {
                if (!isset($chapter) || $chapter == $chap) {
                    if ($data["memberID"] == $checkerID && $data["done"] == 0) {
                        $ev = clone $event;

                        $ev->step = EventSteps::THEO_CHECK;
                        $ev->currentChapter = $chap;
                        $ev->myMemberID = 0;
                        $ev->myChkMemberID = $checkerID;
                        $ev->isContinue = true; // Means not owner of chapter
                        $filtered[] = $ev;
                    }
                }
            }

            // Verse-by-verse Check events
            $crCheck = (array)json_decode($event->crCheck, true);
            foreach ($crCheck as $chap => $data) {
                if (!isset($chapter) || $chapter == $chap) {
                    $doneStatus = $event->sourceBible == "odb" ? 1 : 2;
                    if ($data["memberID"] == $checkerID && $data["done"] != $doneStatus) {
                        $ev = clone $event;

                        $ev->step = $data["done"] == 0 ?
                            EventSteps::CONTENT_REVIEW :
                            EventSteps::FINAL_REVIEW;
                        $ev->currentChapter = $chap;
                        $ev->myMemberID = 0;
                        $ev->myChkMemberID = $checkerID;
                        $ev->isContinue = true; // Means not owner of chapter
                        $filtered[] = $ev;
                    }
                }
            }
        }

        return $filtered;
    }


    /**
     * Get SUN checker event/s
     * @param $checkerID SUN Checker member ID
     * @param null $eventID event ID
     * @param null $memberID SUN translator member ID
     * @return array
     */
    public function getMemberEventsForSun($checkerID, $eventID = null, $memberID = null, $chapter = null)
    {
        $prepare = [];
        if ($eventID)
            $prepare[":eventID"] = $eventID;
        if ($memberID)
            $prepare[":memberID"] = $memberID;

        $sql = "SELECT trs.*, " . PREFIX . "members.userName, " . PREFIX . "members.firstName, "
            . PREFIX . "members.lastName, evnt.bookCode, evnt.state, "
            . "evnt.dateFrom, evnt.dateTo, "
            . "t_lang.langName AS tLang, s_lang.langName AS sLang, "
            . PREFIX . "book_info.name AS name, " . PREFIX . "book_info.sort, "
            . PREFIX . "projects.sourceLangID, " . PREFIX . "projects.bookProject, "
            . PREFIX . "projects.tnLangID, " . PREFIX . "projects.tqLangID, " . PREFIX . "projects.twLangID, "
            . PREFIX . "projects.sourceBible, " . PREFIX . "projects.gwLang, "
            . PREFIX . "projects.targetLang, " . PREFIX . "projects.resLangID, " .
            "t_lang.direction as tLangDir, s_lang.direction as sLangDir, "
            . PREFIX . "book_info.chaptersNum, " . PREFIX . "projects.projectID " .
            "FROM " . PREFIX . "translators AS trs " .
            "LEFT JOIN " . PREFIX . "members ON trs.memberID = " . PREFIX . "members.memberID " .
            "LEFT JOIN " . PREFIX . "events AS evnt ON evnt.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "projects ON " . PREFIX . "projects.projectID = evnt.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON " . PREFIX . "projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON " . PREFIX . "projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info ON evnt.bookCode = " . PREFIX . "book_info.code " .
            "WHERE " . PREFIX . "projects.bookProject = 'sun' " .
            ($eventID ? "AND trs.eventID = :eventID " : " ") .
            ($memberID ? "AND trs.memberID = :memberID " : " ") .
            "ORDER BY tLang, " . PREFIX . "book_info.sort";

        $events = $this->db->select($sql, $prepare);
        $filtered = [];

        foreach ($events as $event) {
            // translation events
            if ($event->memberID == $checkerID
                && $event->step != EventCheckSteps::NONE
                && ($chapter == null || $chapter == $event->currentChapter)) {
                $filtered[] = $event;
            }

            // Theo Check events
            $kwCheck = (array)json_decode($event->kwCheck, true);
            foreach ($kwCheck as $chap => $data) {
                if (!isset($chapter) || $chapter == $chap) {
                    if ($data["memberID"] == $checkerID && $data["done"] == 0) {
                        $ev = clone $event;

                        $ev->step = EventSteps::THEO_CHECK;
                        $ev->currentChapter = $chap;
                        $ev->myMemberID = 0;
                        $ev->myChkMemberID = $checkerID;
                        $ev->isContinue = true; // Means not owner of chapter
                        $filtered[] = $ev;
                    }
                }
            }

            // Verse-by-verse Check events
            $crCheck = (array)json_decode($event->crCheck, true);
            foreach ($crCheck as $chap => $data) {
                if (!isset($chapter) || $chapter == $chap) {
                    if ($data["memberID"] == $checkerID && $data["done"] != 2) {
                        $ev = clone $event;

                        $ev->step = $data["done"] == 0 ?
                            EventSteps::CONTENT_REVIEW :
                            EventSteps::FINAL_REVIEW;
                        $ev->currentChapter = $chap;
                        $ev->myMemberID = 0;
                        $ev->myChkMemberID = $checkerID;
                        $ev->isContinue = true; // Means not owner of chapter
                        $ev->checkDone = false;
                        $filtered[] = $ev;
                    }
                }
            }
        }

        return $filtered;
    }

    public function getMembersForProject($projectTypes)
    {
        return $this->db->table("translators")
            ->leftJoin("events", "events.eventID", "=", "translators.eventID")
            ->leftJoin("projects", "projects.projectID", "=", "events.projectID")
            ->whereIn("projects.bookProject", $projectTypes)
            ->orderBy("events.eventID")
            ->get();
    }

    public function getMembersForL2Event($eventID)
    {
        $this->db->setFetchMode(PDO::FETCH_ASSOC);
        $builder = $this->db->table("checkers_l2")
            ->select("checkers_l2.*", "members.userName", "members.firstName", "members.lastName", "checkers_l2.peer2Check")
            ->leftJoin("members", "checkers_l2.memberID", "=", "members.memberID")
            ->where("checkers_l2.eventID", $eventID);

        $res = $builder->orderBy("members.userName")->get();
        $this->db->setFetchMode(PDO::FETCH_CLASS);

        return $res;
    }

    /**
     * Get all assigned chapters of event of a translator
     * @param $eventID
     * @param $memberID
     * @param $chapter
     * @param $manageMode
     * @return array|static[]
     */
    public function getChapters($eventID, $memberID = null, $chapter = null, $manageMode = "l1")
    {
        $this->db->setFetchMode(PDO::FETCH_ASSOC);

        $builder = $this->db->table("chapters");

        if ($manageMode == "l2") {
            $builder->leftJoin("checkers_l2", function ($join) {
                $join->on("chapters.eventID", "=", "checkers_l2.eventID")
                    ->on("chapters.l2memberID", "=", "checkers_l2.memberID");
            });
            if ($memberID !== null)
                $builder->where(["chapters.l2memberID" => $memberID]);
        } else if ($manageMode == "l3") {
            $builder->leftJoin("checkers_l3", function ($join) {
                $join->on("chapters.eventID", "=", "checkers_l3.eventID")
                    ->on("chapters.l3memberID", "=", "checkers_l3.memberID");
            });
            if ($memberID !== null)
                $builder->where(["chapters.l3memberID" => $memberID]);
        } else if ($manageMode != null) {
            $builder->leftJoin("translators", function ($join) {
                $join->on("chapters.eventID", "=", "translators.eventID")
                    ->on("chapters.memberID", "=", "translators.memberID");
            });
            if ($memberID !== null)
                $builder->where(["chapters.memberID" => $memberID]);
        } else {
            if ($memberID !== null)
                $builder->where(["chapters.memberID" => $memberID]);
        }

        if ($chapter !== null)
            $builder->where(["chapters.chapter" => $chapter]);

        $builder->where(["chapters.eventID" => $eventID])
            ->orderBy("chapters.chapter");

        $res = $builder->get();

        $this->db->setFetchMode(PDO::FETCH_CLASS);

        return $res;
    }

    public function getEventWithContributorsL2($eventID)
    {
        return $this->db->table("events")
            ->select([
                "events.eventID",
                "checkers_l2.sndCheck", "checkers_l2.peer1Check",
                "checkers_l2.peer2Check",
                "book_info.chaptersNum"
            ])
            ->leftJoin("checkers_l2", "events.eventID", "=", "checkers_l2.eventID")
            ->leftJoin("book_info", "events.bookCode", "=", "book_info.code")
            ->where("events.eventID", $eventID)
            ->get();

    }

    public function getEventWithContributorsL3($eventID)
    {
        return $this->db->table("events")
            ->select([
                "events.eventID",
                "checkers_l3.peerCheck",
                "book_info.chaptersNum"
            ])
            ->leftJoin("checkers_l3", "events.eventID", "=", "checkers_l3.eventID")
            ->leftJoin("book_info", "events.bookCode", "=", "book_info.code")
            ->where("events.eventID", $eventID)
            ->get();

    }

    public function getProjectContributors($projectID, $withRoles = true, $withAdmins = true)
    {
        $project = $this->getProjectWithContributors($projectID);
        if (!empty($project)) {

            $contributors = [];
            $contributorsIDs = [];

            $membersModel = new MembersModel();

            $mode = $project[0]->bookProject;
            $lastEventID = null;

            // Checkers
            foreach ($project as $participant) {
                // Facilitators
                if ($withAdmins) {
                    $contributorsIDs += (array)json_decode($participant->admins);
                    $contributorsIDs += (array)json_decode($participant->admins_l2);
                    $contributorsIDs += (array)json_decode($participant->admins_l3);
                }

                $verbCheck = (array)json_decode($participant->verbCheck);
                $peerCheck = (array)json_decode($participant->peerCheck);
                $kwCheck = (array)json_decode($participant->kwCheck);
                $crCheck = (array)json_decode($participant->crCheck);
                $otherCheck = (array)json_decode($participant->otherCheck);
                $sndCheck = (array)json_decode($participant->sndCheck);
                $peer1Check = (array)json_decode($participant->peer1Check);
                $peer2Check = (array)json_decode($participant->peer2Check);
                $peer3Check = (array)json_decode($participant->peer3Check);

                // Resource Checkers
                if (in_array($mode, ["tn", "sun", "tw", "tq", "ulb", "udb", "obs"])) {
                    $contributorsIDs = Arrays::append($contributorsIDs, array_values(array_map(function ($elm) {
                        return $elm->memberID;
                    }, $peerCheck)));
                    $contributorsIDs = Arrays::append($contributorsIDs, array_values(array_map(function ($elm) {
                        return $elm->memberID;
                    }, $kwCheck)));
                    $contributorsIDs = Arrays::append($contributorsIDs, array_values(array_map(function ($elm) {
                        return $elm->memberID;
                    }, $crCheck)));
                    $contributorsIDs = Arrays::append($contributorsIDs, array_values(array_map(function ($elm) {
                        return $elm->memberID;
                    }, $otherCheck)));
                } else {
                    // Scripture Checkers
                    $contributorsIDs = Arrays::append($contributorsIDs, array_values(array_map(function ($elm) {
                        return $elm->memberID;
                    }, $verbCheck)));

                    $contributorsIDs = Arrays::append($contributorsIDs, array_values(array_map(function ($elm) {
                        return $elm->memberID;
                    }, $sndCheck)));
                    $contributorsIDs = Arrays::append($contributorsIDs, array_values(array_map(function ($elm) {
                        return $elm->memberID;
                    }, $peer1Check)));
                    $contributorsIDs = Arrays::append($contributorsIDs, array_values(array_map(function ($elm) {
                        return $elm->memberID;
                    }, $peer2Check)));
                }

                $contributorsIDs = Arrays::append($contributorsIDs, array_values(array_map(function ($elm) {
                    return $elm->memberID;
                }, $peer3Check)));

                // Translators/Revision checkers/L3 checkers
                if ($lastEventID != $participant->eventID) {
                    $chapters = $this->getChapters($participant->eventID, null, null, null);

                    foreach ($chapters as $chapter) {
                        if ($chapter["memberID"] != null) {
                            $contributorsIDs[] = $chapter["memberID"];
                        }
                        if ($chapter["l2memberID"] != null) {
                            $contributorsIDs[] = $chapter["l2memberID"];
                        }
                        if ($chapter["l3memberID"] != null) {
                            $contributorsIDs[] = $chapter["l3memberID"];
                        }
                    }
                    $lastEventID = $participant->eventID;
                }
            }

            $contributorsIDs = array_unique($contributorsIDs);

            $filteredNumeric = array_filter($contributorsIDs, function ($elm) {
                return is_numeric($elm) && $elm > 0;
            });

            $contributors = Arrays::append($contributors, array_filter($contributorsIDs, function ($elm) {
                return !is_numeric($elm);
            }));
            $contributors = array_map(function ($elm) {
                $name = mb_split(" ", $elm);
                return [
                    "fname" => trim(mb_convert_case($name[0], MB_CASE_TITLE, 'UTF-8')),
                    "lname" => trim(mb_convert_case(isset($name[1]) ? $name[1] : "", MB_CASE_TITLE, 'UTF-8')),
                    "uname" => "---",
                    "role" => "",
                    "signup" => "---",
                    "email" => "---",
                    "tou" => "yes",
                    "sof" => "yes"
                ];
            }, $contributors);

            $membersArray = (array)$membersModel->getMembers($filteredNumeric, true, true);

            foreach ($membersArray as $member) {
                if (in_array($member->memberID, $filteredNumeric)) {
                    $role = "";

                    if ($withRoles) {
                        $church_role = (array)json_decode($member->church_role);

                        if (in_array("Pastor", $church_role))
                            $role = __('pastor');
                        elseif (in_array("Seminary Professor", $church_role))
                            $role = __('seminary_professor');
                        elseif (in_array("Denominational Leader", $church_role))
                            $role = __('denominational_leader');
                        elseif (in_array("Bishop", $church_role))
                            $role = __('bishop');
                        elseif (in_array("Elder", $church_role))
                            $role = __('elder');
                        elseif (in_array("Teacher", $church_role))
                            $role = __('teacher');
                    }

                    $tmp = [
                        "fname" => trim(mb_convert_case($member->firstName, MB_CASE_TITLE, 'UTF-8')),
                        "lname" => trim(mb_convert_case($member->lastName, MB_CASE_TITLE, 'UTF-8')),
                        "uname" => trim($member->userName),
                        "role" => $role != "" ? $role : "",
                        "signup" => $member->created,
                        "email" => $member->email,
                        "tou" => "yes",
                        "sof" => "yes"
                    ];

                    $contributors[] = $tmp;
                }
            }

            $contributors = array_unique($contributors, SORT_REGULAR);
            sort($contributors);

            return $contributors;
        }

        return [];
    }

    public function getProjectWithContributors($projectID)
    {
        return $this->db->table("events")
            ->select([
                "events.eventID",
                "translators.verbCheck", "translators.peerCheck",
                "translators.kwCheck", "translators.crCheck",
                "translators.otherCheck", "checkers_l2.sndCheck",
                "checkers_l2.peer1Check", "checkers_l2.peer2Check",
                "checkers_l3.peerCheck AS peer3Check", "projects.bookProject"
            ])
            ->leftJoin("translators", "events.eventID", "=", "translators.eventID")
            ->leftJoin("checkers_l2", "events.eventID", "=", "checkers_l2.eventID")
            ->leftJoin("checkers_l3", "events.eventID", "=", "checkers_l3.eventID")
            ->leftJoin("projects", "events.projectID", "=", "projects.projectID")
            ->where("events.projectID", $projectID)
            ->orderBy("events.eventID")
            ->get();

    }

    /**
     * Get notifications for assigned events
     * @return array
     */
    public function getNotifications()
    {
        $sql = "SELECT trs.*, " .
            PREFIX . "members.userName, " . PREFIX . "members.firstName, " . PREFIX . "members.lastName, " .
            PREFIX . "events.bookCode, " . PREFIX . "projects.sourceBible, " . PREFIX . "projects.bookProject, mytrs.step as myStep, " .
            "t_lang.langName AS tLang, s_lang.langName AS sLang, " . PREFIX . "book_info.name AS bookName " .
            "FROM " . PREFIX . "translators AS trs " .
            "LEFT JOIN " . PREFIX . "members ON trs.memberID = " . PREFIX . "members.memberID " .
            "LEFT JOIN " . PREFIX . "events ON " . PREFIX . "events.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "translators as mytrs ON mytrs.memberID = :memberID AND mytrs.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "projects ON " . PREFIX . "projects.projectID = " . PREFIX . "events.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON " . PREFIX . "projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON " . PREFIX . "projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info ON " . PREFIX . "events.bookCode = " . PREFIX . "book_info.code " .
            "WHERE trs.eventID IN(SELECT eventID FROM " . PREFIX . "translators WHERE memberID = :memberID) " .
            "AND " . PREFIX . "projects.bookProject IN ('ulb', 'udb')";

        $prepare = [
            ":memberID" => Session::get("memberID")
        ];

        $notifications = $this->db->select($sql, $prepare);
        $notifs = [];

        foreach ($notifications as $notification) {
            $peerCheck = (array)json_decode($notification->peerCheck, true);
            $kwCheck = (array)json_decode($notification->kwCheck, true);
            $crCheck = (array)json_decode($notification->crCheck, true);

            foreach ($peerCheck as $chapter => $data) {
                // Exclude taken chapters
                if ($data["memberID"] > 0) continue;

                // Exclude member that is translator
                if ($notification->memberID == Session::get("memberID")) continue;

                $note = clone $notification;
                $note->currentChapter = $chapter;
                $note->step = EventSteps::PEER_REVIEW;
                $note->checkerID = 0;
                $notifs[] = $note;
            }

            foreach ($kwCheck as $chapter => $data) {
                // Exclude taken chapters
                if ($data["memberID"] > 0) continue;

                // Exclude member that is translator
                if ($notification->memberID == Session::get("memberID")) continue;

                $note = clone $notification;
                $note->currentChapter = $chapter;
                $note->step = EventSteps::KEYWORD_CHECK;
                $note->checkerID = 0;
                $notifs[] = $note;
            }

            foreach ($crCheck as $chapter => $data) {
                // Exclude taken chapters
                if ($data["memberID"] > 0) continue;

                // Exclude member that is translator
                if ($notification->memberID == Session::get("memberID")) continue;

                $note = clone $notification;
                $note->currentChapter = $chapter;
                $note->step = EventSteps::CONTENT_REVIEW;
                $note->checkerID = 0;
                $notifs[] = $note;
            }
        }

        return $notifs;
    }

    /**
     * Get notifications from tN, tQ and tW events
     * @return array
     */
    public function getNotificationsOther()
    {
        $sql = "SELECT trs.*, " .
            PREFIX . "members.userName, " . PREFIX . "members.firstName, " . PREFIX . "members.lastName, " .
            PREFIX . "events.bookCode, " . PREFIX . "projects.sourceBible, " . PREFIX . "projects.bookProject, mytrs.step as myStep, " .
            "t_lang.langName AS tLang, s_lang.langName AS sLang, " . PREFIX . "book_info.name AS bookName " .
            "FROM " . PREFIX . "translators AS trs " .
            "LEFT JOIN " . PREFIX . "members ON trs.memberID = " . PREFIX . "members.memberID " .
            "LEFT JOIN " . PREFIX . "events ON " . PREFIX . "events.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "translators as mytrs ON mytrs.memberID = :memberID AND mytrs.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "projects ON " . PREFIX . "projects.projectID = " . PREFIX . "events.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON " . PREFIX . "projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON " . PREFIX . "projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info ON " . PREFIX . "events.bookCode = " . PREFIX . "book_info.code " .
            "WHERE trs.eventID IN(SELECT eventID FROM " . PREFIX . "translators WHERE memberID = :memberID AND isChecker=1) " .
            "AND " . PREFIX . "projects.bookProject IN ('tq','tw','tn','obs')";

        $prepare = [
            ":memberID" => Session::get("memberID")
        ];

        $questionsNotifications = $this->db->select($sql, $prepare);
        $notifs = [];

        foreach ($questionsNotifications as $notification) {
            $otherCheck = (array)json_decode($notification->otherCheck, true);
            $peerCheck = (array)json_decode($notification->peerCheck, true);

            foreach ($otherCheck as $chapter => $data) {
                // Exclude taken chapters
                if ($data["memberID"] > 0) continue;

                // Exclude member that is translator
                if ($notification->memberID == Session::get("memberID")) continue;

                if ($notification->bookProject == "tw") {
                    $group = $this->getTwGroups([
                        "groupID" => $chapter,
                        "eventID" => $notification->eventID]);

                    $words = (array)json_decode($group[0]->words, true);
                    $notification->group = $words[0] . "..." . $words[sizeof($words) - 1];
                }

                $note = clone $notification;
                $note->currentChapter = $chapter;
                $note->step = "other";
                $note->manageMode = $notification->bookProject;
                $note->peer = 1;
                $notifs[] = $note;
            }

            foreach ($peerCheck as $chapter => $data) {
                // Exclude taken chapters
                if ($data["memberID"] > 0) continue;

                // Exclude member that is already in otherCheck
                if ($otherCheck[$chapter]["memberID"] == Session::get("memberID")) continue;

                if ($notification->bookProject == "tw") {
                    $group = $this->getTwGroups([
                        "groupID" => $chapter,
                        "eventID" => $notification->eventID]);

                    $words = (array)json_decode($group[0]->words, true);
                    $notification->group = $words[0] . "..." . $words[sizeof($words) - 1];
                }

                $note = clone $notification;

                $memberModel = new MembersModel();
                $member = $memberModel->getMember([
                    "firstName",
                    "lastName"
                ], ["memberID", $otherCheck[$chapter]["memberID"]]);
                if (!empty($member)) {
                    $note->firstName = $member[0]->firstName;
                    $note->lastName = $member[0]->lastName;
                }

                $note->currentChapter = $chapter;
                $note->step = EventSteps::PEER_REVIEW;
                $note->manageMode = $notification->bookProject;
                $note->peer = 2;
                $notifs[] = $note;
            }
        }

        return $notifs;
    }

    /**
     * Get notifications for revision events
     * @return array
     */
    public function getNotificationsRevision()
    {
        $sql = "SELECT chks.*, " .
            PREFIX . "members.userName, " . PREFIX . "members.firstName, " . PREFIX . "members.lastName, " .
            PREFIX . "events.bookCode, " . PREFIX . "projects.bookProject, " . PREFIX . "projects.sourceBible, mychks.step as myStep, " .
            "t_lang.langName AS tLang, s_lang.langName AS sLang, " . PREFIX . "book_info.name AS bookName " .
            "FROM " . PREFIX . "checkers_l2 AS chks " .
            "LEFT JOIN " . PREFIX . "members ON chks.memberID = " . PREFIX . "members.memberID " .
            "LEFT JOIN " . PREFIX . "events ON " . PREFIX . "events.eventID = chks.eventID " .
            "LEFT JOIN " . PREFIX . "checkers_l2 as mychks ON mychks.memberID = :memberID AND mychks.eventID = chks.eventID " .
            "LEFT JOIN " . PREFIX . "projects ON " . PREFIX . "projects.projectID = " . PREFIX . "events.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON " . PREFIX . "projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON " . PREFIX . "projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info ON " . PREFIX . "events.bookCode = " . PREFIX . "book_info.code " .
            "WHERE chks.eventID IN(SELECT eventID FROM " . PREFIX . "checkers_l2 WHERE memberID = :memberID)";

        $prepare = [
            ":memberID" => Session::get("memberID")
        ];

        $notifications = $this->db->select($sql, $prepare);
        $notifs = [];

        foreach ($notifications as $notification) {
            if ($notification->memberID != Session::get("memberID")) {
                $notification->manageMode = "l2";

                $peerCheck = (array)json_decode($notification->peerCheck, true);
                $kwCheck = (array)json_decode($notification->kwCheck, true);
                $crCheck = (array)json_decode($notification->crCheck, true);

                foreach ($peerCheck as $chapter => $data) {
                    // Exclude taken chapters
                    if ($data["memberID"] > 0) continue;

                    $note = clone $notification;
                    $note->currentChapter = $chapter;
                    $note->step = EventCheckSteps::PEER_REVIEW;
                    $note->checkerID = 0;
                    $notifs[] = $note;
                }

                foreach ($kwCheck as $chapter => $data) {
                    // Exclude taken chapters
                    if ($data["memberID"] > 0) continue;

                    $note = clone $notification;
                    $note->currentChapter = $chapter;
                    $note->step = EventCheckSteps::KEYWORD_CHECK;
                    $note->checkerID = 0;
                    $notifs[] = $note;
                }

                foreach ($crCheck as $chapter => $data) {
                    // Exclude taken chapters
                    if ($data["memberID"] > 0) continue;

                    $note = clone $notification;
                    $note->currentChapter = $chapter;
                    $note->step = EventCheckSteps::CONTENT_REVIEW;
                    $note->checkerID = 0;
                    $notifs[] = $note;
                }
            }
        }

        return $notifs;
    }

    /**
     * Get notifications for Level 3 events
     * @return array
     */
    public function getNotificationsL3()
    {
        $sql = "SELECT chks.*, " .
            PREFIX . "members.userName, " . PREFIX . "members.firstName, " . PREFIX . "members.lastName, " .
            PREFIX . "events.bookCode, " . PREFIX . "projects.bookProject, " . PREFIX . "projects.sourceBible, mychks.step as myStep, " .
            "t_lang.langName AS tLang, s_lang.langName AS sLang, " . PREFIX . "book_info.name AS bookName " .
            "FROM " . PREFIX . "checkers_l3 AS chks " .
            "LEFT JOIN " . PREFIX . "members ON chks.memberID = " . PREFIX . "members.memberID " .
            "LEFT JOIN " . PREFIX . "events ON " . PREFIX . "events.eventID = chks.eventID " .
            "LEFT JOIN " . PREFIX . "checkers_l3 as mychks ON mychks.memberID = :memberID AND mychks.eventID = chks.eventID " .
            "LEFT JOIN " . PREFIX . "projects ON " . PREFIX . "projects.projectID = " . PREFIX . "events.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON " . PREFIX . "projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON " . PREFIX . "projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info ON " . PREFIX . "events.bookCode = " . PREFIX . "book_info.code " .
            "WHERE chks.eventID IN (SELECT eventID FROM " . PREFIX . "checkers_l3 WHERE memberID = :memberID)";

        $prepare = [
            ":memberID" => Session::get("memberID")
        ];

        $notifications = $this->db->select($sql, $prepare);
        $notifs = [];

        foreach ($notifications as $notification) {
            if ($notification->step != EventCheckSteps::PEER_REVIEW_L3)
                continue;

            if (Session::get("memberID") == $notification->memberID)
                continue;

            // Peer check notifications
            $peerCheck = (array)json_decode($notification->peerCheck, true);
            foreach ($peerCheck as $chapter => $data) {
                // Exclude taken chapters
                if ($data["memberID"] > 0)
                    continue;

                $notif = clone $notification;
                $notif->step = EventCheckSteps::PEER_REVIEW_L3;
                $notif->currentChapter = $chapter;
                $notif->manageMode = "l3";
                $notifs[] = $notif;
            }
        }

        return $notifs;
    }

    /**
     * Get notifications for Level 2 events
     * @return array
     */
    public function getNotificationsSun()
    {
        $sql = "SELECT trs.*, " .
            PREFIX . "members.userName, " . PREFIX . "members.firstName, " . PREFIX . "members.lastName, " .
            PREFIX . "events.bookCode, " . PREFIX . "projects.sourceBible, " . PREFIX . "projects.bookProject, mytrs.step as myStep, " .
            "t_lang.langName AS tLang, s_lang.langName AS sLang, " . PREFIX . "book_info.name AS bookName " .
            "FROM " . PREFIX . "translators AS trs " .
            "LEFT JOIN " . PREFIX . "members ON trs.memberID = " . PREFIX . "members.memberID " .
            "LEFT JOIN " . PREFIX . "events ON " . PREFIX . "events.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "translators as mytrs ON mytrs.memberID = :memberID AND mytrs.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "projects ON " . PREFIX . "projects.projectID = " . PREFIX . "events.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON " . PREFIX . "projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON " . PREFIX . "projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info ON " . PREFIX . "events.bookCode = " . PREFIX . "book_info.code " .
            "WHERE trs.eventID IN(SELECT eventID FROM " . PREFIX . "translators WHERE memberID = :memberID) " .
            "AND " . PREFIX . "projects.bookProject = 'sun' ";

        $prepare = [
            ":memberID" => Session::get("memberID")
        ];

        $notifications = $this->db->select($sql, $prepare);
        $notifs = [];

        foreach ($notifications as $notification) {
            // Theological check notifications
            if ($notification->memberID != Session::get("memberID")) {
                $kwCheck = (array)json_decode($notification->kwCheck, true);
                foreach ($kwCheck as $chapter => $data) {
                    // Exclude taken chapters
                    if ($data["memberID"] > 0) continue;

                    $notif = clone $notification;
                    $notif->step = EventSteps::THEO_CHECK;
                    $notif->currentChapter = $chapter;
                    $notif->manageMode = "sun";
                    $notifs[] = $notif;
                }
            }

            // Verse-by-verse check notifications
            if ($notification->memberID != Session::get("memberID")) {
                $crCheck = (array)json_decode($notification->crCheck, true);
                foreach ($crCheck as $chapter => $data) {
                    // Exclude taken chapters
                    if ($data["memberID"] > 0) continue;

                    $notif = clone $notification;
                    $notif->step = EventSteps::CONTENT_REVIEW;
                    $notif->currentChapter = $chapter;
                    $notif->manageMode = "sun";
                    $notifs[] = $notif;
                }
            }
        }

        return $notifs;
    }

    /**
     * Get notifications for Level 2 events
     * @return array
     */
    public function getNotificationsRadio()
    {
        $sql = "SELECT trs.*, " .
            PREFIX . "members.userName, " . PREFIX . "members.firstName, " . PREFIX . "members.lastName, " .
            PREFIX . "events.bookCode, " . PREFIX . "projects.sourceBible, " . PREFIX . "projects.bookProject, mytrs.step as myStep, " .
            "t_lang.langName AS tLang, s_lang.langName AS sLang, " . PREFIX . "book_info.name AS bookName " .
            "FROM " . PREFIX . "translators AS trs " .
            "LEFT JOIN " . PREFIX . "members ON trs.memberID = " . PREFIX . "members.memberID " .
            "LEFT JOIN " . PREFIX . "events ON " . PREFIX . "events.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "translators as mytrs ON mytrs.memberID = :memberID AND mytrs.eventID = trs.eventID " .
            "LEFT JOIN " . PREFIX . "projects ON " . PREFIX . "projects.projectID = " . PREFIX . "events.projectID " .
            "LEFT JOIN " . PREFIX . "languages AS t_lang ON " . PREFIX . "projects.targetLang = t_lang.langID " .
            "LEFT JOIN " . PREFIX . "languages AS s_lang ON " . PREFIX . "projects.sourceLangID = s_lang.langID " .
            "LEFT JOIN " . PREFIX . "book_info ON " . PREFIX . "events.bookCode = " . PREFIX . "book_info.code " .
            "WHERE trs.eventID IN (SELECT eventID FROM " . PREFIX . "translators WHERE memberID = :memberID AND isChecker=1) " .
            "AND " . PREFIX . "projects.bookProject = 'rad' ";

        $prepare = [
            ":memberID" => Session::get("memberID")
        ];

        $notifications = $this->db->select($sql, $prepare);
        $notifs = [];

        foreach ($notifications as $notification) {
            // Peer check notifications
            if ($notification->memberID != Session::get("memberID")) {
                $peerCheck = (array)json_decode($notification->peerCheck, true);
                foreach ($peerCheck as $chapter => $data) {
                    // Exclude taken chapters
                    if ($data["memberID"] > 0) continue;

                    $notif = clone $notification;
                    $notif->step = EventSteps::PEER_REVIEW;
                    $notif->currentChapter = $chapter;
                    $notif->manageMode = "rad";
                    $notifs[] = $notif;
                }
            }
        }

        return $notifs;
    }

    /** Get list of all languages
     * @param null $isGW (true - gateway, false - other, null - all)
     * @param null $langs filter by list of lang ids
     * @return array
     */
    public function getAllLanguages($isGW = null, $langs = null)
    {
        $builder = $this->db->table("languages");

        if ($isGW !== null) {
            $builder->where("languages.isGW", $isGW);
        }
        if (is_array($langs) && !empty($langs)) {
            $builder->whereIn("languages.langID", $langs);
        }

        return $builder->select("languages.langID", "languages.langName", "languages.angName", "gateway_languages.glID")
            ->leftJoin("gateway_languages", "languages.langID", "=", "gateway_languages.gwLang")
            ->orderBy("languages.langID")->get();
    }

    public function getAdminLanguages($memberID = null)
    {
        $builder = $this->db->table("events")
            ->select("events.eventID", "events.admins", "events.admins_l2", "events.admins_l3",
                "projects.gwLang", "projects.targetLang")
            ->leftJoin("projects", "events.projectID", "=", "projects.projectID");

        if ($memberID != null) {
            $builder->where("events.admins", "LIKE", "%\"$memberID\"%")
                ->orWhere("events.admins_l2", "LIKE", "%\"$memberID\"%")
                ->orWhere("events.admins_l3", "LIKE", "%\"$memberID\"%");
        }

        return $builder->get();
    }

    public function getSuperadminLanguages($memberID = null)
    {
        $builder = $this->db->table("gateway_languages")
            ->select("glID", "gwLang", "admins");

        if ($memberID != null) {
            $builder->where("admins", "LIKE", "%\"$memberID\"%");
        }

        return $builder->get();
    }

    public function getBooksOfTranslators()
    {
        return $this->db->table("chapters")
            ->select(["members.userName", "members.firstName", "members.lastName",
                "chapters.chapter", "chapters.done", "book_info.name", "book_info.code", "tw_groups.words",
                "projects.bookProject", "projects.targetLang", "languages.angName", "languages.langName"])
            ->leftJoin("members", "chapters.memberID", "=", "members.memberID")
            ->leftJoin("events", "chapters.eventID", "=", "events.eventID")
            ->leftJoin("projects", "events.projectID", "=", "projects.projectID")
            ->leftJoin("book_info", "events.bookCode", "=", "book_info.code")
            ->leftJoin("languages", "projects.targetLang", "=", "languages.langID")
            ->leftJoin("tw_groups", function ($join) {
                $join->on("chapters.chapter", "=", "tw_groups.groupID")
                    ->where("projects.bookProject", "=", "tw");
            })
            ->orderBy("members.userName")
            ->orderBy("book_info.sort")
            ->orderBy("chapters.chapter")
            ->get();
    }

    public function getEventMemberInfo($eventID, $memberID)
    {
        $sql = "SELECT trs.memberID AS translator, ".
            "proj.bookProject, trs.isChecker, ".
            "l2.memberID AS l2checker, l3.memberID AS l3checker ".
            "FROM ".PREFIX."events AS evnt ".
            "LEFT JOIN ".PREFIX."translators AS trs ON evnt.eventID = trs.eventID ".
            "LEFT JOIN ".PREFIX."checkers_l2 AS l2 ON evnt.eventID = l2.eventID AND l2.memberID = :memberID ".
            "LEFT JOIN ".PREFIX."checkers_l3 AS l3 ON evnt.eventID = l3.eventID AND l3.memberID = :memberID ".
            "LEFT JOIN ".PREFIX."projects AS proj ON evnt.projectID = proj.projectID ".
            "WHERE evnt.eventID = :eventID";

        $prepare = array(
            ":memberID" => $memberID,
            ":eventID" => $eventID);

        return $this->db->select($sql, $prepare);
    }


    /**
     * Create project
     * @param array $data
     * @return string
     */
    public function createProject($data)
    {
        return $this->db->table("projects")
            ->insertGetId($data);
    }

    /**
     * Update project
     * @param array $data
     * @param array $where
     * @return string
     */
    public function updateProject($data, $where)
    {
        return $this->db->table("projects")
            ->where($where)
            ->update($data);
    }

    /**
     * Create event
     * @param array $data
     * @return string
     */
    public function createEvent($data)
    {
        return $this->db->table("events")
            ->insertGetId($data);
    }

    /**
     * Add member as new translator for event
     * @param array $data
     * @return string
     */
    public function addTranslator($data)
    {
        return $this->db->table("translators")
            ->insertGetId($data);
    }

    /**
     * Add member as new Revision checker for event
     * @param array $data
     * @return string
     */
    public function addL2Checker($data)
    {
        return $this->db->table("checkers_l2")
            ->insertGetId($data);
    }

    /**
     * Add member as new Level 3 checker for event
     * @param array $data
     * @return string
     */
    public function addL3Checker($data)
    {
        return $this->db->table("checkers_l3")
            ->insertGetId($data);
    }

    /**
     * Update event
     * @param array $data
     * @param array $where
     * @return int
     */
    public function updateEvent($data, $where)
    {
        return $this->db->table("events")
            ->where($where)
            ->update($data);
    }

    /**
     * Delete event
     * @param array $where
     * @return int
     */
    public function deleteEvent($where)
    {
        return $this->db->table("events")
            ->where($where)
            ->delete();
    }

    /**
     * Update translator
     * @param array $data
     * @param array $where
     * @return int
     */
    public function updateTranslator($data, $where)
    {
        return $this->db->table("translators")
            ->where($where)
            ->update($data);
    }

    /**
     * Update Revision Checker
     * @param array $data
     * @param array $where
     * @return int
     */
    public function updateL2Checker($data, $where)
    {
        return $this->db->table("checkers_l2")
            ->where($where)
            ->update($data);
    }

    /**
     * Update L3 Checker
     * @param array $data
     * @param array $where
     * @return int
     */
    public function updateL3Checker($data, $where)
    {
        return $this->db->table("checkers_l3")
            ->where($where)
            ->update($data);
    }

    /**
     * Assign chapter to translator's queue
     * @param $data
     * @return int
     */
    public function assignChapter($data)
    {
        return $this->db->table("chapters")
            ->insertGetId($data);
    }

    /**
     * Remove chapter from translator's queue
     * @param $where
     * @return int
     */
    public function removeChapter($where)
    {
        return $this->db->table("chapters")
            ->where($where)
            ->delete();
    }

    /**
     * Get next chapter to translate/check
     * @param $eventID
     * @param $memberID
     * @param string $level
     * @return array
     */
    public function getNextChapter($eventID, $memberID, $level = "l1")
    {
        $builder = $this->db->table("chapters")
            ->where(["eventID" => $eventID]);

        if ($level == "l1") {
            $builder->where(["memberID" => $memberID])
                ->where("done", "!=", true);
        } else if ($level == "l2") {
            $builder->where(["l2memberID" => $memberID])
                ->where("l2checked", "!=", true);
        } else if ($level == "l3") {
            $builder->where(["l3memberID" => $memberID])
                ->where("l3checked", "!=", true);
        }

        return $builder->orderBy("chapter")->get();
    }

    /**
     * Update chapter
     * @param array $data
     * @param array $where
     * @return int
     */
    public function updateChapter($data, $where)
    {
        return $this->db->table("chapters")
            ->where($where)
            ->update($data);
    }

    /**
     * Delete tW group
     * @param array $where
     * @return int
     */
    public function deleteTwGroups($where)
    {
        return $this->db->table("tw_groups")
            ->where($where)
            ->delete();
    }

    /**
     * Create tW group
     * @param $data
     * @return int
     */
    public function createTwGroup($data)
    {
        return $this->db->table("tw_groups")
            ->insertGetId($data);
    }

    /**
     * Get Event Data by eventID OR by projectID and bookCode
     * @param $eventID
     * @param $projectID
     * @param $bookCode
     * @param bool $countMembers
     * @return array
     */
    public function getEvent($eventID, $projectID = null, $bookCode = null, $countMembers = false)
    {
        $table = "translators";
        $builder = $this->db->table("events");
        $select = ["events.*", "book_info.*", "projects.bookProject", "projects.targetLang"];
        if ($countMembers) {
            $select[] = $this->db->raw("COUNT(DISTINCT " . PREFIX . $table . ".memberID) AS translators");
            $select[] = $this->db->raw("COUNT(DISTINCT " . PREFIX . "checkers_l2.memberID) AS checkers_l2");
            $select[] = $this->db->raw("COUNT(DISTINCT " . PREFIX . "checkers_l3.memberID) AS checkers_l3");

            $builder
                ->leftJoin($table, "events.eventID", "=", $table . ".eventID")
                ->leftJoin("checkers_l2", "events.eventID", "=", "checkers_l2.eventID")
                ->leftJoin("checkers_l3", "events.eventID", "=", "checkers_l3.eventID");
        }

        $builder->leftJoin("book_info", "events.bookCode", "=", "book_info.code")
            ->leftJoin("projects", "events.projectID", "=", "projects.projectID")
            ->leftJoin("gateway_languages", "projects.glID", "=", "gateway_languages.glID");

        if ($eventID)
            $builder->where("events.eventID", $eventID);
        else
            $builder->where("events.projectID", $projectID)
                ->where("events.bookCode", $bookCode);

        return $builder->select($select)->get();
    }
}