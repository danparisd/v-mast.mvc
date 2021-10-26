<?php
/**
 * Created by mXaln
 */

namespace App\Controllers;

use App\Models\NewsModel;
use App\Models\ApiModel;
use App\Models\SailDictionaryModel;
use App\Repositories\Event\IEventRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Resources\IResourcesRepository;
use Helpers\Arrays;
use Helpers\Constants\OdbSections;
use Helpers\Markdownify\Converter;
use Helpers\Tools;
use Support\Facades\View;
use Config\Config;
use Helpers\Url;
use Helpers\Gump;
use Helpers\Session;
use App\Core\Controller;
use Support\Facades\Cache;
use App\Models\EventsModel;
use App\Models\MembersModel;
use App\Modules\Alma\Models\Word;
use App\Models\TranslationsModel;
use Helpers\Constants\EventSteps;
use Helpers\Constants\EventCheckSteps;
use Helpers\Constants\EventStates;
use Helpers\Constants\EventMembers;

class EventsController extends Controller {
    private $_model;
    private $_translationModel;
    private $_saildictModel;
    private $_apiModel;
    private $_newsModel;
    private $_membersModel;
    private $_notifications;
    private $_news;
    private $_newNewsCount;

    protected $memberRepo = null;
    protected $eventRepo = null;
    protected $resourcesRepo = null;
    private $_member;

    public function __construct(
        IMemberRepository $memberRepo,
        IEventRepository $eventRepo,
        IResourcesRepository $resourcesRepo
    ) {
        parent::__construct();

        $this->memberRepo = $memberRepo;
        $this->eventRepo = $eventRepo;
        $this->resourcesRepo = $resourcesRepo;

        if (Config::get("app.isMaintenance")
            && !in_array($_SERVER['REMOTE_ADDR'], Config::get("app.ips"))) {
            Url::redirect("maintenance");
        }

        if (preg_match("/^\\/events\\/rpc\\/get_saildict/", $_SERVER["REQUEST_URI"])) {
            $this->_saildictModel = new SailDictionaryModel();
            return;
        }

        if (!Session::get('memberID')
            && !preg_match("/^\\/events\\/demo|\\/events\\/faq/", $_SERVER["REQUEST_URI"])) {
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
            $this->_saildictModel = new SailDictionaryModel();
            $this->_apiModel = new ApiModel();
            $this->_newsModel = new NewsModel();
            $this->_membersModel = new MembersModel();

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

    /**
     * Show member's dashboard view
     * @return mixed
     */
    public function index()
    {
        $data["menu"] = 1;

        if (Session::get("isBookAdmin")) {
            $myFacilitatorEvents = $this->_member->adminEvents;
            $data["myFacilitatorEventsInProgress"] = [];
            $data["myFacilitatorEventsFinished"] = [];

            foreach ($myFacilitatorEvents as $myFacilitatorEvent) {
                if ($myFacilitatorEvent->state == EventStates::TRANSLATED
                    || $myFacilitatorEvent->state == EventStates::L2_CHECKED
                    || $myFacilitatorEvent->state == EventStates::COMPLETE)
                    $data["myFacilitatorEventsFinished"][] = $myFacilitatorEvent;
                else
                    $data["myFacilitatorEventsInProgress"][] = $myFacilitatorEvent;
            }
        }

        $data["myTranslatorEvents"] = $this->_model->getMemberEvents(
            Session::get("memberID"),
            null,
            null,
            true,
            false,
            false
        );

        $data["myCheckerL1Events"] = $this->_model->getMemberEventsForChecker(Session::get("memberID"));
        $notesCheckers = $this->_model->getMemberEventsForNotes(Session::get("memberID"));
        $sunCheckers = $this->_model->getMemberEventsForCheckerSun(Session::get("memberID"));
        $tqtwCheckers = $this->_model->getMemberEventsForOther(Session::get("memberID"));
        $radioCheckers = $this->_model->getMemberEventsForRadio(Session::get("memberID"));

        $data["myCheckerL1Events"] = Arrays::append(
            $data["myCheckerL1Events"],
            $notesCheckers);
        $data["myCheckerL1Events"] = Arrays::append(
            $data["myCheckerL1Events"],
            $sunCheckers);
        $data["myCheckerL1Events"] = Arrays::append(
            $data["myCheckerL1Events"],
            $tqtwCheckers);
        $data["myCheckerL1Events"] = Arrays::append(
            $data["myCheckerL1Events"],
            $radioCheckers);

        $data["myCheckerL2Events"] = $this->_model->getMemberEventsForCheckerL2(Session::get("memberID"));
        $data["myCheckerL3Events"] = $this->_model->getMemberEventsForCheckerL3(Session::get("memberID"));

        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;

        return View::make('Events/Index')
            ->shares("title", __("welcome_title"))
            ->shares("data", $data);
    }

    /**
     * @param $eventID
     * @return mixed
     */
    public function translator($eventID)
    {
        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEvents(Session::get("memberID"), $eventID);
        $data["next_step"] = EventSteps::PRAY;

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["ulb", "udb"])) {
                Url::redirect("events/translator-" . $data["event"][0]->bookProject . "/" . $eventID);
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::TRANSLATING || $data["event"][0]->state == EventStates::TRANSLATED) {
                if ($data["event"][0]->step == EventSteps::NONE)
                    Url::redirect("events/information/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                $menuPage = $data["event"][0]->langInput ? "TranslatorLangInput" : "Translator";

                switch ($data["event"][0]->step) {
                    case EventSteps::PRAY:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::FINISHED], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $postdata = [
                                    "step" => ($data["event"][0]->langInput ? EventSteps::MULTI_DRAFT : EventSteps::CONSUME),
                                    "currentChapter" => $sourceText["currentChapter"],
                                    "currentChunk" => $sourceText["currentChunk"]
                                ];
                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator/' . $data["event"][0]->eventID);
                            }
                        }

                        // Check if translator just started translating this book
                        $data["event"][0]->justStarted = $data["event"][0]->verbCheck == "";
                        $data["next_step"] = $data["event"][0]->langInput ? "multi-draft_lang_input" : EventSteps::CONSUME;

                        return View::make('Events/L1/' . $menuPage)
                            ->nest('page', 'Events/L1/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                    // Scripture Input Step 1
                    case EventSteps::MULTI_DRAFT:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel->getEventTranslation($data["event"][0]->trID, $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $arr["firstvs"] = $tv->firstvs;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::FINISHED], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);
                            if (isset($_POST["confirm_step"])) {
                                if (isset($translation) && isset($_POST["verses"]) && !empty($_POST["verses"])) {
                                    $postVerses = $_POST["verses"];

                                    // Check for empty chunks
                                    $empty = array_filter($postVerses, function ($elm) {
                                        return empty($elm);
                                    });

                                    if (empty($empty)) {
                                        $chunks = array_map(
                                            function ($verse) {
                                                return [$verse];
                                            },
                                            array_keys($postVerses)
                                        );

                                        $this->_model->updateChapter(
                                            ["chunks" => json_encode($chunks)],
                                            [
                                                "eventID" => $data["event"][0]->eventID,
                                                "chapter" => $data["event"][0]->currentChapter
                                            ]
                                        );

                                        $postdata = [
                                            "step" => EventSteps::SELF_CHECK
                                        ];

                                        $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                        Url::redirect('events/translator/' . $data["event"][0]->eventID);
                                    } else {
                                        $error[] = __("empty_draft_verses_error");
                                    }
                                } else {
                                    $error[] = __("no_translation_data");
                                }
                            }
                        }

                        $data["next_step"] = EventSteps::SELF_CHECK;

                        return View::make('Events/L1/TranslatorLangInput')
                            ->nest('page', 'Events/L1/LangInput')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::CONSUME:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::FINISHED], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $postdata = ["step" => EventSteps::VERBALIZE];

                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = EventSteps::VERBALIZE;

                        return View::make('Events/L1/Translator')
                            ->nest('page', 'Events/L1/Consume')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::VERBALIZE:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::FINISHED], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator/' . $data["event"][0]->eventID);
                        }

                        $data["event"][0]->checkerName = null;
                        $verbCheck = (array)json_decode($data["event"][0]->verbCheck, true);
                        $checkDone = false;
                        if (array_key_exists($data["event"][0]->currentChapter, $verbCheck)) {
                            $memberID = $verbCheck[$data["event"][0]->currentChapter]["memberID"];
                            $checkDone = $verbCheck[$data["event"][0]->currentChapter]["done"] > 0;
                            if (!is_numeric($memberID)) {
                                $data["event"][0]->checkerName = $memberID;
                            } else {
                                $member = $this->memberRepo->get($memberID);
                                $data["event"][0]->checkerName = $member->firstName
                                    . " " . mb_substr($member->lastName, 0, 1) . ".";
                            }
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if ($checkDone) {
                                    $postdata = ["step" => EventSteps::CHUNKING];
                                    $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("verb_checker_not_ready_error");
                                }
                            }
                        }

                        $data["next_step"] = EventSteps::CHUNKING;

                        return View::make('Events/L1/Translator')
                            ->nest('page', 'Events/L1/Verbalize')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::CHUNKING:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::FINISHED], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $_POST = Gump::xss_clean($_POST);

                                $chunks = isset($_POST["chunks_array"]) ? $_POST["chunks_array"] : "";
                                $chunks = (array)json_decode($chunks);
                                if ($this->_apiModel->testChunks($chunks, $sourceText["totalVerses"])) {
                                    if ($this->_model->updateChapter(["chunks" => json_encode($chunks)], ["eventID" => $data["event"][0]->eventID, "chapter" => $data["event"][0]->currentChapter])) {
                                        $this->_model->updateTranslator(["step" => EventSteps::READ_CHUNK], ["trID" => $data["event"][0]->trID]);
                                        Url::redirect('events/translator/' . $data["event"][0]->eventID);
                                        exit;
                                    } else {
                                        $error[] = __("error_ocured");
                                    }
                                } else {
                                    $error[] = __("wrong_chunks_error");
                                }
                            }
                        }

                        $data["next_step"] = EventSteps::BLIND_DRAFT;

                        return View::make('Events/L1/Translator')
                            ->nest('page', 'Events/L1/Chunking')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::READ_CHUNK:
                        $sourceText = $this->getScriptureSourceText($data, true);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::FINISHED], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {

                                $this->_model->updateTranslator(["step" => EventSteps::BLIND_DRAFT], ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/L1/Translator')
                            ->nest('page', 'Events/L1/ReadChunk')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::BLIND_DRAFT:
                        $sourceText = $this->getScriptureSourceText($data, true);
                        $translationData = [];

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter,
                                    $data["event"][0]->currentChunk
                                );

                                if (!empty($translationData)) {
                                    $verses = json_decode($translationData[0]->translatedVerses, true);
                                    $data["blind"] = $verses[EventMembers::TRANSLATOR]["blind"];
                                }
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::FINISHED], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);
                            $draft = isset($_POST["draft"]) ? $_POST["draft"] : "";

                            if (isset($_POST["confirm_step"])) {
                                if (trim($draft) != "") {
                                    if (empty($translationData)) {
                                        $translationVerses = [
                                            EventMembers::TRANSLATOR => [
                                                "blind" => trim($draft),
                                                "verses" => []
                                            ],
                                            EventMembers::L2_CHECKER => [
                                                "verses" => array()
                                            ],
                                            EventMembers::L3_CHECKER => [
                                                "verses" => array()
                                            ],
                                        ];

                                        $encoded = json_encode($translationVerses);
                                        $json_error = json_last_error();

                                        if ($json_error == JSON_ERROR_NONE) {
                                            $trData = [
                                                "projectID" => $data["event"][0]->projectID,
                                                "eventID" => $data["event"][0]->eventID,
                                                "trID" => $data["event"][0]->trID,
                                                "targetLang" => $data["event"][0]->targetLang,
                                                "bookProject" => $data["event"][0]->bookProject,
                                                "sort" => $data["event"][0]->sort,
                                                "bookCode" => $data["event"][0]->bookCode,
                                                "chapter" => $data["event"][0]->currentChapter,
                                                "chunk" => $data["event"][0]->currentChunk,
                                                "firstvs" => $sourceText["chunk"][0],
                                                "translatedVerses" => $encoded,
                                                "dateCreate" => date('Y-m-d H:i:s')
                                            ];

                                            $this->_translationModel->createTranslation($trData);
                                        }
                                    }

                                    $postdata["step"] = EventSteps::SELF_CHECK;

                                    // If chapter is finished go to SELF_EDIT, otherwise go to the next chunk
                                    if (array_key_exists($data["event"][0]->currentChunk + 1, $sourceText["chunks"])) {
                                        // Current chunk is finished, go to next chunk
                                        $postdata["currentChunk"] = $data["event"][0]->currentChunk + 1;
                                        $postdata["step"] = EventSteps::READ_CHUNK;
                                    }

                                    $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("empty_draft_verses_error");
                                }
                            }
                        }

                        if (array_key_exists($data["event"][0]->currentChunk + 1, $sourceText["chunks"])) {
                            $data["next_step"] = "continue_alt";
                        } else {
                            $data["next_step"] = EventSteps::SELF_CHECK;
                        }

                        return View::make('Events/L1/Translator')
                            ->nest('page', 'Events/L1/BlindDraft')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::SELF_CHECK:
                        $sourceText = $this->getScriptureSourceText($data);
                        $nextChapter = 0;

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                                $data["comments"] = $this->getComments($data["event"][0]->eventID, $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation($data["event"][0]->trID, $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $arr["firstvs"] = $tv->firstvs;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;

                                if ($data["event"][0]->sourceBible == "rsb") {
                                    $words = Word::with('translations')
                                        ->orderBy('title')
                                        ->get();

                                    $data["words"] = json_encode($words->toArray());
                                }

                                // Get next chapter if it exists
                                $nextChapterDB = $this->_model->getNextChapter($data["event"][0]->eventID, Session::get("memberID"));
                                if (!empty($nextChapterDB && isset($nextChapterDB[1])))
                                    $nextChapter = $nextChapterDB[1]->chapter;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::FINISHED], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);
                            $submitStep = isset($_POST["submitStep"]) && $_POST["submitStep"];
                            $postdata = [];
                            $chapterLink = $submitStep ? "/" . $data["event"][0]->currentChapter : "";

                            if (isset($_POST["confirm_step"])) {
                                if (isset($translationData)) {
                                    foreach ($translationData as $tv) {
                                        $this->_translationModel->updateTranslation([
                                            "translateDone" => true
                                        ], ["tID" => $tv->tID]);
                                    }
                                }

                                // Check if the member has another chapter to translate
                                // then redirect to preparation page
                                $postdata["step"] = EventSteps::PRAY;
                                $postdata["currentChapter"] = 0;
                                $postdata["currentChunk"] = 0;

                                $chapters = [];
                                for ($i = 1; $i <= $data["event"][0]->chaptersNum; $i++) {
                                    $data["chapters"][$i] = [];
                                }

                                $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                foreach ($chaptersDB as $chapter) {
                                    $tmp["trID"] = $chapter["trID"];
                                    $tmp["memberID"] = $chapter["memberID"];
                                    $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                    $tmp["done"] = $chapter["done"];

                                    $chapters[$chapter["chapter"]] = $tmp;
                                }

                                $chapters[$data["event"][0]->currentChapter]["done"] = true;
                                $this->_model->updateChapter(
                                    ["done" => true],
                                    [
                                        "eventID" => $data["event"][0]->eventID,
                                        "chapter" => $data["event"][0]->currentChapter
                                    ]);

                                if ($data["event"][0]->langInput) {
                                    // Check if whole book is finished
                                    if ($data["event"][0]->langInput && $this->checkBookFinished($chapters, $data["event"][0]->chaptersNum))
                                        $this->_model->updateEvent([
                                            "state" => EventStates::TRANSLATED,
                                            "dateTo" => date("Y-m-d H:i:s", time())],
                                            ["eventID" => $data["event"][0]->eventID]);
                                } else {
                                    $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);
                                    if (!isset($peerCheck[$data["event"][0]->currentChapter])) {
                                        $peerCheck[$data["event"][0]->currentChapter] = [
                                            "memberID" => 0,
                                            "done" => 0
                                        ];
                                    }
                                    $postdata["peerCheck"] = json_encode($peerCheck);
                                }

                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator/' . $data["event"][0]->eventID . $chapterLink);
                            }
                        }

                        $data["nextChapter"] = $nextChapter;
                        $page = $data["event"][0]->langInput ? "SelfCheckLangInput" : "SelfCheck";
                        $data["next_step"] = $data["event"][0]->langInput ? "continue_alt" : EventSteps::PEER_REVIEW;

                        return View::make('Events/L1/' . $menuPage)
                            ->nest('page', 'Events/L1/' . $page)
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::FINISHED:
                        $data["success"] = __("you_event_finished_success");

                        return View::make('Events/L1/Translator')
                            ->nest('page', 'Events/L1/Finished')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/L1/Translator')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/L1/Translator')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function translatorContinue($eventID, $chapter)
    {
        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEvents(Session::get("memberID"), $eventID, $chapter, true);

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["ulb", "udb"])) {
                Url::redirect("events/translator-" . $data["event"][0]->bookProject . "/" . $eventID);
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::TRANSLATING || $data["event"][0]->state == EventStates::TRANSLATED) {
                if ($data["event"][0]->step == EventSteps::NONE)
                    Url::redirect("events/information/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                switch ($data["event"][0]->step) {
                    case EventSteps::PEER_REVIEW:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                                $data["comments"] = $this->getComments($data["event"][0]->eventID, $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation($data["event"][0]->trID, $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::FINISHED], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);

                            if (isset($_POST["confirm_step"])) {
                                $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);
                                $kwCheck = (array)json_decode($data["event"][0]->kwCheck, true);
                                if ($peerCheck[$data["event"][0]->currentChapter]["done"] == 1) {
                                    $peerCheck[$data["event"][0]->currentChapter]["done"] = 2;
                                    $kwCheck[$data["event"][0]->currentChapter] = [
                                        "memberID" => 0,
                                        "done" => 0
                                    ];
                                    $postdata = [
                                        "peerCheck" => json_encode($peerCheck),
                                        "kwCheck" => json_encode($kwCheck)
                                    ];

                                    $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator/'
                                        . $data["event"][0]->eventID
                                        . '/' . $data["event"][0]->currentChapter);
                                } else {
                                    $error[] = __("checker_not_ready_error");
                                }
                            }
                        }

                        $data["next_step"] = EventSteps::KEYWORD_CHECK;

                        return View::make('Events/L1/Translator')
                            ->nest('page', 'Events/L1/PeerReview')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::KEYWORD_CHECK:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                                $data["comments"] = $this->getComments($data["event"][0]->eventID, $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation($data["event"][0]->trID, $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::FINISHED], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);

                            if (isset($_POST["confirm_step"])) {
                                $kwCheck = (array)json_decode($data["event"][0]->kwCheck, true);
                                $crCheck = (array)json_decode($data["event"][0]->crCheck, true);
                                if ($kwCheck[$data["event"][0]->currentChapter]["done"] == 1) {
                                    $kwCheck[$data["event"][0]->currentChapter]["done"] = 2;
                                    $crCheck[$data["event"][0]->currentChapter] = [
                                        "memberID" => 0,
                                        "done" => 0
                                    ];
                                    $postdata = [
                                        "kwCheck" => json_encode($kwCheck),
                                        "crCheck" => json_encode($crCheck)
                                    ];

                                    $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator/'
                                        . $data["event"][0]->eventID
                                        . '/' . $data["event"][0]->currentChapter);
                                } else {
                                    $error[] = __("checker_not_ready_error");
                                }
                            }
                        }

                        $data["next_step"] = EventSteps::CONTENT_REVIEW;

                        return View::make('Events/L1/Translator')
                            ->nest('page', 'Events/L1/KeywordCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::CONTENT_REVIEW:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                                $data["comments"] = $this->getComments($data["event"][0]->eventID, $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation($data["event"][0]->trID, $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::FINISHED], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);

                            if (isset($_POST["confirm_step"])) {
                                $crCheck = (array)json_decode($data["event"][0]->crCheck, true);
                                $otherCheck = (array)json_decode($data["event"][0]->otherCheck, true);
                                if ($crCheck[$data["event"][0]->currentChapter]["done"] == 1) {
                                    $crCheck[$data["event"][0]->currentChapter]["done"] = 2;
                                    $otherCheck[$data["event"][0]->currentChapter] = [
                                        "memberID" => 0,
                                        "done" => 0
                                    ];
                                    $postdata = [
                                        "crCheck" => json_encode($crCheck),
                                        "otherCheck" => json_encode($otherCheck)
                                    ];

                                    $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator/'
                                        . $data["event"][0]->eventID
                                        . '/' . $data["event"][0]->currentChapter);
                                } else {
                                    $error[] = __("checker_not_ready_error");
                                }
                            }
                        }

                        $data["next_step"] = EventSteps::FINAL_REVIEW;

                        return View::make('Events/L1/Translator')
                            ->nest('page', 'Events/L1/ContentReview')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::FINAL_REVIEW:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                                $data["comments"] = $this->getComments($data["event"][0]->eventID, $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation($data["event"][0]->trID, $data["event"][0]->currentChapter);
                                $translation = [];

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::FINISHED], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);

                            if (isset($_POST["confirm_step"])) {
                                $chunks = isset($_POST["chunks"]) ? (array)$_POST["chunks"] : array();
                                $chunks = array_map("trim", $chunks);
                                $chunks = array_filter($chunks, function ($v) {
                                    return !empty($v);
                                });

                                if (sizeof($chunks) < sizeof($data["chunks"]))
                                    $error[] = __("empty_verses_error");

                                if (!isset($error)) {
                                    $versesCombined = [];
                                    foreach ($chunks as $key => $chunk) {
                                        $verses = preg_split("/\|([0-9]+)\|/", $chunk, -1, PREG_SPLIT_NO_EMPTY);

                                        if (sizeof($data["chunks"][$key]) !=
                                            sizeof($verses)) {
                                            $error[] = __("not_equal_verse_markers");
                                            break;
                                        }

                                        $versesCombined[$key] = array_combine($data["chunks"][$key], $verses);
                                    }

                                    if (!isset($error)) {
                                        foreach ($versesCombined as $key => $chunk) {
                                            $translation[$key][EventMembers::TRANSLATOR]["verses"] = $chunk;

                                            $tID = $translation[$key]["tID"];
                                            unset($translation[$key]["tID"]);

                                            $encoded = json_encode($translation[$key]);
                                            $json_error = json_last_error();

                                            if ($json_error == JSON_ERROR_NONE) {
                                                $trData = array(
                                                    "translatedVerses" => $encoded,
                                                    "translateDone" => true
                                                );
                                                $this->_translationModel->updateTranslation(
                                                    $trData,
                                                    array(
                                                        "trID" => $data["event"][0]->trID,
                                                        "tID" => $tID));
                                            } else {
                                                $error[] = __("error_ocured", array($tID));
                                            }
                                        }

                                        $chapters = [];
                                        for ($i = 1; $i <= $data["event"][0]->chaptersNum; $i++) {
                                            $data["chapters"][$i] = [];
                                        }

                                        $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                        foreach ($chaptersDB as $chapter) {
                                            $tmp["trID"] = $chapter["trID"];
                                            $tmp["memberID"] = $chapter["memberID"];
                                            $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                            $tmp["done"] = $chapter["done"];

                                            $chapters[$chapter["chapter"]] = $tmp;
                                        }

                                        // Check if whole book is finished
                                        if ($this->checkBookFinished($chapters, $data["event"][0]->chaptersNum))
                                            $this->_model->updateEvent([
                                                "state" => EventStates::TRANSLATED,
                                                "dateTo" => date("Y-m-d H:i:s", time())],
                                                ["eventID" => $data["event"][0]->eventID]);

                                        // Check if the member has another chapter to translate
                                        // then redirect to preparation page
                                        $nextChapter = 0;
                                        $nextChapterDB = $this->_model->getNextChapter($data["event"][0]->eventID, Session::get("memberID"));
                                        if (!empty($nextChapterDB))
                                            $nextChapter = $nextChapterDB[0]->chapter;

                                        $otherCheck = (array)json_decode($data["event"][0]->otherCheck, true);
                                        $otherCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                        $postdata = [
                                            "step" => $nextChapter > 0 ? EventSteps::PRAY : EventSteps::NONE,
                                            "otherCheck" => json_encode($otherCheck)
                                        ];

                                        $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                        Url::redirect('events/translator/' . $data["event"][0]->eventID);
                                    }
                                }
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/L1/Translator')
                            ->nest('page', 'Events/L1/FinalReview')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/L1/Translator')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/L1/Translator')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function translatorNotes($eventID)
    {
        $data["menu"] = 1;
        $data["isCheckerPage"] = false;
        $data["isPeerPage"] = false;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEvents(Session::get("memberID"), $eventID);
        $data["next_step"] = EventSteps::PRAY;

        $title = "";

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["tn"])) {
                if (in_array($data["event"][0]->bookProject, ["udb", "ulb"]))
                    Url::redirect("events/translator/" . $eventID);
                else
                    Url::redirect("events/translator-" . $data["event"][0]->bookProject . "/" . $eventID);
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > -1 ? ($data["event"][0]->currentChapter == 0
                    ? __("front") : $data["event"][0]->currentChapter) : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if (($data["event"][0]->state == EventStates::TRANSLATING
                || $data["event"][0]->state == EventStates::TRANSLATED)) {
                if ($data["event"][0]->step == EventSteps::NONE)
                    Url::redirect("events/information-tn/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                switch ($data["event"][0]->step) {
                    case EventSteps::PRAY:

                        // Get scripture text
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText) && !array_key_exists("error", $sourceText))
                            $data = $sourceText;

                        // Get notes
                        $sourceTextNotes = $this->getNotesSourceText($data);

                        if ($sourceTextNotes !== false) {
                            if (!array_key_exists("error", $sourceTextNotes)) {
                                $data = $sourceTextNotes;
                            } else {
                                $error[] = $sourceTextNotes["error"];
                                $data["error"] = $sourceTextNotes["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-tn/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $postdata = [
                                    "step" => !$data["nosource"] ? EventSteps::CONSUME : EventSteps::READ_CHUNK,
                                    "currentChapter" => $data["currentChapter"],
                                    "currentChunk" => $data["currentChunk"]
                                ];
                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);

                                $nChunks = $this->_apiModel->getNotesChunks($sourceTextNotes);

                                $this->_model->updateChapter(
                                    ["chunks" => json_encode($nChunks)],
                                    [
                                        "eventID" => $data["event"][0]->eventID,
                                        "chapter" => $data['currentChapter']
                                    ]
                                );

                                Url::redirect('events/translator-tn/' . $data["event"][0]->eventID);
                            }
                        }

                        // Check if translator just started translating of this book
                        $data["event"][0]->justStarted = $data["event"][0]->otherCheck == "";
                        $data["next_step"] = EventSteps::CONSUME . "_tn";

                        return View::make('Events/Notes/Translator')
                            ->nest('page', 'Events/Notes/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                    case EventSteps::CONSUME: // Consume chapter
                        // Get scripture text
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText) && !array_key_exists("error", $sourceText))
                            $data = $sourceText;

                        // Get notes
                        $sourceTextNotes = $this->getNotesSourceText($data);

                        if ($sourceTextNotes !== false) {
                            if (!array_key_exists("error", $sourceTextNotes)) {
                                $data = $sourceTextNotes;
                            } else {
                                $error[] = $sourceTextNotes["error"];
                                $data["error"] = $sourceTextNotes["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-tn/' . $data["event"][0]->eventID);
                        }

                        if (isset($data["nosource"]) && $data["nosource"] === true) {
                            $this->_model->updateTranslator(["step" => EventSteps::READ_CHUNK], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-tn/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (empty($data["chunks"])) {
                                    $nChunks = $this->_apiModel->getNotesChunks($sourceTextNotes);

                                    $this->_model->updateChapter(
                                        ["chunks" => json_encode($nChunks)],
                                        [
                                            "eventID" => $data["event"][0]->eventID,
                                            "chapter" => $data['currentChapter']
                                        ]
                                    );
                                }

                                $postdata = [
                                    "step" => EventSteps::READ_CHUNK,
                                ];

                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator-tn/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = EventSteps::READ_CHUNK . "_tn";

                        return View::make('Events/Notes/Translator')
                            ->nest('page', 'Events/Notes/Consume')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::READ_CHUNK:
                        $sourceText = $this->getScriptureSourceText($data, true);

                        if (!empty($sourceText) && !array_key_exists("error", $sourceText))
                            $data = $sourceText;

                        // Get notes
                        $sourceTextNotes = $this->getNotesSourceText($data, true);

                        if ($sourceTextNotes !== false) {
                            if (!array_key_exists("error", $sourceTextNotes)) {
                                $data = $sourceTextNotes;
                            } else {
                                $error[] = $sourceTextNotes["error"];
                                $data["error"] = $sourceTextNotes["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-tn/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {

                                $this->_model->updateTranslator(["step" => EventSteps::BLIND_DRAFT], ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator-tn/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = EventSteps::BLIND_DRAFT;

                        return View::make('Events/Notes/Translator')
                            ->nest('page', 'Events/Notes/ReadChunk')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::BLIND_DRAFT: // Self-Check Notes
                        // Get scripture text
                        $sourceText = $this->getScriptureSourceText($data, true);

                        if (!empty($sourceText) && !array_key_exists("error", $sourceText))
                            $data = $sourceText;

                        // Get notes
                        $sourceTextNotes = $this->getNotesSourceText($data, true);

                        if ($sourceTextNotes !== false) {
                            if (!array_key_exists("error", $sourceTextNotes)) {
                                $data = $sourceTextNotes;

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter,
                                    $data["event"][0]->currentChunk);

                                if (!empty($translationData)) {
                                    $verses = json_decode($translationData[0]->translatedVerses, true);
                                    $data["translation"] = $verses[EventMembers::TRANSLATOR]["verses"];
                                }
                            } else {
                                $error[] = $sourceTextNotes["error"];
                                $data["error"] = $sourceTextNotes["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-tn/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $chunk = isset($_POST["draft"]) ? $_POST["draft"] : "";

                            if (isset($_POST["confirm_step"])) {
                                if (trim(strip_tags($chunk)) != "") {
                                    $postdata["step"] = EventSteps::SELF_CHECK;

                                    // If chapter is finished go to SELF_EDIT, otherwise go to the next chunk
                                    if (array_key_exists($data["event"][0]->currentChunk + 1, $data["chunks"])) {
                                        // Current chunk is finished, go to next chunk
                                        $postdata["currentChunk"] = $data["event"][0]->currentChunk + 1;
                                        $postdata["step"] = EventSteps::READ_CHUNK;
                                    }

                                    $upd = $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator-tn/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("empty_draft_verses_error");
                                }
                            }
                        }

                        if (array_key_exists($data["event"][0]->currentChunk + 1, $data["chunks"])) {
                            $data["next_step"] = EventSteps::READ_CHUNK . "_tn";
                        } else {
                            $data["next_step"] = EventSteps::SELF_CHECK;
                        }

                        return View::make('Events/Notes/Translator')
                            ->nest('page', 'Events/Notes/BlindDraft')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::SELF_CHECK:
                        // Get scripture text
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText) && !array_key_exists("error", $sourceText))
                            $data = $sourceText;

                        // Get notes
                        $sourceTextNotes = $this->getNotesSourceText($data);
                        $translation = [];

                        if ($sourceTextNotes !== false) {
                            if (!array_key_exists("error", $sourceTextNotes)) {
                                $data = $sourceTextNotes;
                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter);

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceTextNotes["error"];
                                $data["error"] = $sourceTextNotes["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-tn/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $this->_translationModel->updateTranslation(
                                    ["translateDone" => true],
                                    [
                                        "trID" => $data["event"][0]->trID,
                                        "chapter" => $data["event"][0]->currentChapter
                                    ]
                                );

                                $chapters = [];
                                for ($i = 0; $i <= $data["event"][0]->chaptersNum; $i++) {
                                    $data["chapters"][$i] = [];
                                }

                                $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                foreach ($chaptersDB as $chapter) {
                                    $tmp["trID"] = $chapter["trID"];
                                    $tmp["memberID"] = $chapter["memberID"];
                                    $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                    $tmp["done"] = $chapter["done"];

                                    $chapters[$chapter["chapter"]] = $tmp;
                                }

                                $chapters[$data["event"][0]->currentChapter]["done"] = true;
                                $this->_model->updateChapter(["done" => true], ["eventID" => $data["event"][0]->eventID, "chapter" => $data["event"][0]->currentChapter]);

                                // Check if the member has another chapter to translate
                                // then redirect to preparation page
                                $nextChapter = -1;
                                $nextChapterDB = $this->_model->getNextChapter($data["event"][0]->eventID, Session::get("memberID"));

                                if (!empty($nextChapterDB))
                                    $nextChapter = $nextChapterDB[0]->chapter;

                                $otherCheck = (array)json_decode($data["event"][0]->otherCheck, true);
                                if (!array_key_exists($data['currentChapter'], $otherCheck)) {
                                    $otherCheck[$data['currentChapter']] = [
                                        "memberID" => 0,
                                        "done" => 0
                                    ];
                                }

                                $postdata = [
                                    "step" => EventSteps::NONE,
                                    "currentChapter" => -1,
                                    "currentChunk" => 0,
                                    "otherCheck" => json_encode($otherCheck)
                                ];

                                if ($nextChapter > -1) {
                                    $postdata["step"] = EventSteps::PRAY;
                                    $postdata["currentChapter"] = $nextChapter;
                                }

                                $upd = $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator-tn/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/Notes/Translator')
                            ->nest('page', 'Events/Notes/SelfCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/Notes/Translator')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/Notes/Translator')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function translatorQuestions($eventID)
    {
        $data["menu"] = 1;
        $data["isCheckerPage"] = false;
        $data["isPeerPage"] = false;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEvents(Session::get("memberID"), $eventID);

        $title = "";

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["tq"])) {
                if (in_array($data["event"][0]->bookProject, ["udb", "ulb"]))
                    Url::redirect("events/translator/" . $eventID);
                else
                    Url::redirect("events/translator-" . $data["event"][0]->bookProject . "/" . $eventID);
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if (($data["event"][0]->state == EventStates::TRANSLATING
                || $data["event"][0]->state == EventStates::TRANSLATED)) {
                if ($data["event"][0]->step == EventSteps::NONE)
                    Url::redirect("events/information-tq/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                switch ($data["event"][0]->step) {
                    case EventSteps::PRAY:

                        // Get questions
                        $sourceTextQuestions = $this->getQuestionsSourceText($data);

                        if ($sourceTextQuestions !== false) {
                            if (!array_key_exists("error", $sourceTextQuestions)) {
                                $data = $sourceTextQuestions;
                            } else {
                                $error[] = $sourceTextQuestions["error"];
                                $data["error"] = $sourceTextQuestions["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-tq/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $postdata = [
                                    "step" => EventSteps::MULTI_DRAFT,
                                    "currentChapter" => $data["currentChapter"],
                                    "currentChunk" => $data["currentChunk"]
                                ];
                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);

                                $qChunks = $this->_apiModel->getQuestionsChunks($sourceTextQuestions);

                                $this->_model->updateChapter(
                                    ["chunks" => json_encode($qChunks)],
                                    [
                                        "eventID" => $data["event"][0]->eventID,
                                        "chapter" => $data['currentChapter']
                                    ]
                                );

                                Url::redirect('events/translator-tq/' . $data["event"][0]->eventID);
                            }
                        }

                        // Check if translator just started translating of this book
                        $data["event"][0]->justStarted = $data["event"][0]->otherCheck == "";
                        $data["next_step"] = EventSteps::MULTI_DRAFT;

                        return View::make('Events/Questions/Translator')
                            ->nest('page', 'Events/Questions/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);


                    case EventSteps::MULTI_DRAFT: // Consume/Verbalize/Draft Questions

                        // Get questions
                        $sourceTextQuestions = $this->getQuestionsSourceText($data);

                        if ($sourceTextQuestions !== false) {
                            if (!array_key_exists("error", $sourceTextQuestions)) {
                                $data = $sourceTextQuestions;

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceTextQuestions["error"];
                                $data["error"] = $sourceTextQuestions["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-tq/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $confirm_step = isset($_POST["confirm_step"]) ? $_POST["confirm_step"] : false;
                            if ($confirm_step) {
                                $chunks = isset($_POST["chunks"]) ? (array)$_POST["chunks"] : [];
                                $chunks = $this->_apiModel->testChunkQuestions($chunks, $data["questions"]);
                                if (!$chunks === false) {
                                    $postdata["step"] = EventSteps::SELF_CHECK;

                                    $upd = $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator-tq/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("wrong_chunks_error");
                                }
                            }
                        }

                        $data["next_step"] = EventSteps::SELF_CHECK;

                        return View::make('Events/Questions/Translator')
                            ->nest('page', 'Events/Questions/MultiDraft')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::SELF_CHECK:

                        // Get notes
                        $sourceTextQuestions = $this->getQuestionsSourceText($data);

                        if ($sourceTextQuestions !== false) {
                            if (!array_key_exists("error", $sourceTextQuestions)) {
                                $data = $sourceTextQuestions;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceTextQuestions["error"];
                                $data["error"] = $sourceTextQuestions["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-tq/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $confirm_step = isset($_POST["confirm_step"]) ? $_POST["confirm_step"] : false;
                            if ($confirm_step) {
                                $chunks = isset($_POST["chunks"]) ? (array)$_POST["chunks"] : [];
                                $chunks = $this->_apiModel->testChunkQuestions($chunks, $data["questions"]);
                                if (!$chunks === false) {
                                    $this->_translationModel->updateTranslation(
                                        ["translateDone" => true],
                                        [
                                            "trID" => $data["event"][0]->trID,
                                            "chapter" => $data["event"][0]->currentChapter
                                        ]
                                    );

                                    $chapters = [];
                                    for ($i = 1; $i <= $data["event"][0]->chaptersNum; $i++) {
                                        $data["chapters"][$i] = [];
                                    }

                                    $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                    foreach ($chaptersDB as $chapter) {
                                        $tmp["trID"] = $chapter["trID"];
                                        $tmp["memberID"] = $chapter["memberID"];
                                        $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                        $tmp["done"] = $chapter["done"];

                                        $chapters[$chapter["chapter"]] = $tmp;
                                    }

                                    $chapters[$data["event"][0]->currentChapter]["done"] = true;
                                    $this->_model->updateChapter(["done" => true], ["eventID" => $data["event"][0]->eventID, "chapter" => $data["event"][0]->currentChapter]);

                                    // Check if the member has another chapter to translate
                                    // then redirect to preparation page
                                    $nextChapter = 0;
                                    $nextChapterDB = $this->_model->getNextChapter($data["event"][0]->eventID, Session::get("memberID"));

                                    if (!empty($nextChapterDB))
                                        $nextChapter = $nextChapterDB[0]->chapter;

                                    $otherCheck = (array)json_decode($data["event"][0]->otherCheck, true);
                                    if (!array_key_exists($data['currentChapter'], $otherCheck)) {
                                        $otherCheck[$data['currentChapter']] = [
                                            "memberID" => 0,
                                            "done" => 0
                                        ];
                                    }

                                    $postdata = [
                                        "step" => EventSteps::NONE,
                                        "currentChapter" => 0,
                                        "currentChunk" => 0,
                                        "otherCheck" => json_encode($otherCheck)
                                    ];

                                    if ($nextChapter > 0) {
                                        $postdata["step"] = EventSteps::PRAY;
                                        $postdata["currentChapter"] = $nextChapter;
                                    }

                                    $upd = $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator-tq/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("wrong_chunks_error");
                                }
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/Questions/Translator')
                            ->nest('page', 'Events/Questions/SelfCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/Questions/Translator')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/Questions/Translator')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function translatorWords($eventID)
    {
        $data["menu"] = 1;
        $data["isCheckerPage"] = false;
        $data["isPeerPage"] = false;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEvents(Session::get("memberID"), $eventID);

        $title = "";

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["tw"])) {
                if (in_array($data["event"][0]->bookProject, ["udb", "ulb"]))
                    Url::redirect("events/translator/" . $eventID);
                else
                    Url::redirect("events/translator-" . $data["event"][0]->bookProject . "/" . $eventID);
            }

            if (($data["event"][0]->state == EventStates::TRANSLATING
                || $data["event"][0]->state == EventStates::TRANSLATED)) {
                if ($data["event"][0]->step == EventSteps::NONE)
                    Url::redirect("events/information-tw/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                switch ($data["event"][0]->step) {
                    case EventSteps::PRAY:

                        // Get Words
                        $sourceTextWords = $this->getWordsSourceText($data);

                        if ($sourceTextWords !== false) {
                            if (!array_key_exists("error", $sourceTextWords)) {
                                $data = $sourceTextWords;

                                $title = $data["event"][0]->name
                                    . " " . ($data["event"][0]->currentChapter > 0
                                        ? " [" . $data["group"][0] . "..." . $data["group"][sizeof($data["group"]) - 1] . "]"
                                        : "")
                                    . " - " . $data["event"][0]->tLang
                                    . " - " . __($data["event"][0]->bookProject);
                            } else {
                                $error[] = $sourceTextWords["error"];
                                $data["error"] = $sourceTextWords["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-tw/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $postdata = [
                                    "step" => EventSteps::MULTI_DRAFT,
                                    "currentChapter" => $data["currentChapter"],
                                    "currentChunk" => $data["currentChunk"]
                                ];
                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);

                                $wChunks = array_keys($sourceTextWords["words"]);

                                $this->_model->updateChapter(
                                    ["chunks" => json_encode($wChunks)],
                                    [
                                        "eventID" => $data["event"][0]->eventID,
                                        "chapter" => $data['currentChapter']
                                    ]
                                );

                                Url::redirect('events/translator-tw/' . $data["event"][0]->eventID);
                            }
                        }

                        // Check if translator just started translating of this book
                        $data["event"][0]->justStarted = $data["event"][0]->kwCheck == "";
                        $data["next_step"] = EventSteps::MULTI_DRAFT;

                        return View::make('Events/Words/Translator')
                            ->nest('page', 'Events/Words/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);


                    case EventSteps::MULTI_DRAFT: // Consume/Verbalize/Draft/Self-Check Questions

                        // Get notes
                        $sourceTextWords = $this->getWordsSourceText($data);

                        if ($sourceTextWords !== false) {
                            if (!array_key_exists("error", $sourceTextWords)) {
                                $data = $sourceTextWords;

                                $title = $data["event"][0]->name
                                    . " " . ($data["event"][0]->currentChapter > 0
                                        ? " [" . $data["group"][0] . "..." . $data["group"][sizeof($data["group"]) - 1] . "]"
                                        : "")
                                    . " - " . $data["event"][0]->tLang
                                    . " - " . __($data["event"][0]->bookProject);

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceTextWords["error"];
                                $data["error"] = $sourceTextWords["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-tw/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $confirm_step = isset($_POST["confirm_step"]) ? $_POST["confirm_step"] : false;
                            if ($confirm_step) {
                                $chunks = isset($_POST["chunks"]) ? (array)$_POST["chunks"] : [];
                                $chunks = $this->_apiModel->testChunkWords($chunks, $data["words"]);
                                if (!$chunks === false) {
                                    $postdata["step"] = EventSteps::SELF_CHECK;

                                    $upd = $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator-tw/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("wrong_chunks_error");
                                }
                            }
                        }

                        $data["next_step"] = EventSteps::SELF_CHECK;

                        return View::make('Events/Words/Translator')
                            ->nest('page', 'Events/Words/MultiDraft')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::SELF_CHECK:

                        // Get notes
                        $sourceTextWords = $this->getWordsSourceText($data);

                        if ($sourceTextWords !== false) {
                            if (!array_key_exists("error", $sourceTextWords)) {
                                $data = $sourceTextWords;

                                $title = $data["event"][0]->name
                                    . " " . ($data["event"][0]->currentChapter > 0
                                        ? " [" . $data["group"][0] . "..." . $data["group"][sizeof($data["group"]) - 1] . "]"
                                        : "")
                                    . " - " . $data["event"][0]->tLang
                                    . " - " . __($data["event"][0]->bookProject);

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceTextWords["error"];
                                $data["error"] = $sourceTextWords["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-tw/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $confirm_step = isset($_POST["confirm_step"]) ? $_POST["confirm_step"] : false;
                            if ($confirm_step) {
                                $chunks = isset($_POST["chunks"]) ? (array)$_POST["chunks"] : [];
                                $chunks = $this->_apiModel->testChunkWords($chunks, $data["words"]);
                                if (!$chunks === false) {
                                    $this->_translationModel->updateTranslation(
                                        ["translateDone" => true],
                                        [
                                            "trID" => $data["event"][0]->trID,
                                            "chapter" => $data["event"][0]->currentChapter
                                        ]
                                    );

                                    $tw_groups = $this->_model->getTwGroups([
                                        "eventID" => $data["event"][0]->eventID
                                    ]);

                                    $chapters = [];
                                    foreach ($tw_groups as $group) {
                                        $data["chapters"][$group->groupID] = [];
                                    }

                                    $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                    foreach ($chaptersDB as $chapter) {
                                        $tmp["trID"] = $chapter["trID"];
                                        $tmp["memberID"] = $chapter["memberID"];
                                        $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                        $tmp["done"] = $chapter["done"];

                                        $chapters[$chapter["chapter"]] = $tmp;
                                    }

                                    $chapters[$data["event"][0]->currentChapter]["done"] = true;
                                    $this->_model->updateChapter(["done" => true], [
                                        "eventID" => $data["event"][0]->eventID,
                                        "chapter" => $data["event"][0]->currentChapter]);

                                    // Check if the member has another chapter to translate
                                    // then redirect to preparation page
                                    $nextChapter = 0;
                                    $nextChapterDB = $this->_model->getNextChapter($data["event"][0]->eventID, Session::get("memberID"));

                                    if (!empty($nextChapterDB))
                                        $nextChapter = $nextChapterDB[0]->chapter;

                                    $otherCheck = (array)json_decode($data["event"][0]->otherCheck, true);
                                    if (!array_key_exists($data['currentChapter'], $otherCheck)) {
                                        $otherCheck[$data['currentChapter']] = [
                                            "memberID" => 0,
                                            "done" => 0
                                        ];
                                    }

                                    $postdata = [
                                        "step" => EventSteps::NONE,
                                        "currentChapter" => 0,
                                        "currentChunk" => 0,
                                        "otherCheck" => json_encode($otherCheck)
                                    ];

                                    if ($nextChapter > 0) {
                                        $postdata["step"] = EventSteps::PRAY;
                                        $postdata["currentChapter"] = $nextChapter;
                                    }

                                    $upd = $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator-tw/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("wrong_chunks_error");
                                }
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/Words/Translator')
                            ->nest('page', 'Events/Words/SelfCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/Words/Translator')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/Words/Translator')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function translatorSun($eventID)
    {
        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEvents(Session::get("memberID"), $eventID);

        $title = "";

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["sun"])) {
                if (in_array($data["event"][0]->bookProject, ["udb", "ulb"]))
                    Url::redirect("events/translator/" . $eventID);
                else
                    Url::redirect("events/translator-" . $data["event"][0]->bookProject . "/" . $eventID);
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::TRANSLATING || $data["event"][0]->state == EventStates::TRANSLATED) {
                if ($data["event"][0]->step == EventSteps::NONE)
                    Url::redirect("events/information-sun/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                switch ($data["event"][0]->step) {
                    case EventSteps::PRAY:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-sun/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {

                                $postdata = [
                                    "step" => EventSteps::CONSUME,
                                    "currentChapter" => $sourceText["currentChapter"],
                                    "currentChunk" => $sourceText["currentChunk"]
                                ];
                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator-sun/' . $data["event"][0]->eventID);
                            }
                        }

                        // Check if translator just started translating of this book
                        $data["event"][0]->justStarted = $data["event"][0]->kwCheck == "";
                        $data["next_step"] = EventSteps::CONSUME;

                        return View::make('Events/SUN/Translator')
                            ->nest('page', 'Events/SUN/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::CONSUME:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-sun/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $postdata = [
                                    "step" => EventSteps::CHUNKING
                                ];

                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator-sun/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = EventSteps::CHUNKING . "_sun";

                        return View::make('Events/SUN/Translator')
                            ->nest('page', 'Events/SUN/Consume')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::CHUNKING:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-sun/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $_POST = Gump::xss_clean($_POST);

                                $chunks = isset($_POST["chunks_array"]) ? $_POST["chunks_array"] : "";
                                $chunks = (array)json_decode($chunks);
                                if ($this->_apiModel->testChunks($chunks, $sourceText["totalVerses"])) {
                                    if ($this->_model->updateChapter(["chunks" => json_encode($chunks)], ["eventID" => $data["event"][0]->eventID, "chapter" => $data["event"][0]->currentChapter])) {
                                        $this->_model->updateTranslator(["step" => EventSteps::REARRANGE], ["trID" => $data["event"][0]->trID]);
                                        Url::redirect('events/translator-sun/' . $data["event"][0]->eventID);
                                        exit;
                                    } else {
                                        $error[] = __("error_ocured");
                                    }
                                } else {
                                    $error[] = __("wrong_chunks_error");
                                }
                            }
                        }

                        $data["next_step"] = EventSteps::REARRANGE;

                        return View::make('Events/SUN/Translator')
                            ->nest('page', 'Events/SUN/Chunking')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::REARRANGE:
                        $sourceText = $this->getScriptureSourceText($data, true);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel
                                    ->getEventTranslation($data["event"][0]->trID, $data["event"][0]->currentChapter, $data["event"][0]->currentChunk);

                                if (!empty($translationData)) {
                                    $verses = json_decode($translationData[0]->translatedVerses, true);
                                    $data["words"] = $verses[EventMembers::TRANSLATOR]["words"];
                                }
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-sun/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);
                            $words = isset($_POST["draft"]) ? $_POST["draft"] : "";

                            if (isset($_POST["confirm_step"])) {
                                if (trim($words) != "") {
                                    $postdata["step"] = EventSteps::SYMBOL_DRAFT;
                                    $postdata["currentChunk"] = 0;

                                    // If chapter is finished go to SYMBOL_DRAFT, otherwise go to the next chunk
                                    if (array_key_exists($data["event"][0]->currentChunk + 1, $sourceText["chunks"])) {
                                        // Current chunk is finished, go to next chunk
                                        $postdata["currentChunk"] = $data["event"][0]->currentChunk + 1;
                                        $postdata["step"] = EventSteps::REARRANGE;
                                    }

                                    $upd = $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator-sun/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("empty_words_error");
                                }
                            }
                        }

                        if (array_key_exists($data["event"][0]->currentChunk + 1, $sourceText["chunks"])) {
                            $data["next_step"] = "continue_alt";
                        } else {
                            $data["next_step"] = EventSteps::SYMBOL_DRAFT;
                        }

                        return View::make('Events/SUN/Translator')
                            ->nest('page', 'Events/SUN/WordsDraft')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::SYMBOL_DRAFT:
                        $sourceText = $this->getScriptureSourceText($data, true);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel
                                    ->getEventTranslation($data["event"][0]->trID, $data["event"][0]->currentChapter, $data["event"][0]->currentChunk);

                                if (!empty($translationData)) {
                                    $verses = json_decode($translationData[0]->translatedVerses, true);
                                    $data["words"] = $verses[EventMembers::TRANSLATOR]["words"];
                                    $data["symbols"] = $verses[EventMembers::TRANSLATOR]["symbols"];
                                }
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-sun/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);
                            $symbols = isset($_POST["symbols"]) ? $_POST["symbols"] : "";

                            if (isset($_POST["confirm_step"])) {
                                if (trim($symbols) != "") {
                                    $postdata["step"] = EventSteps::SELF_CHECK;

                                    // If chapter is finished go to SELF_EDIT, otherwise go to the next chunk
                                    if (array_key_exists($data["event"][0]->currentChunk + 1, $sourceText["chunks"])) {
                                        // Current chunk is finished, go to next chunk
                                        $postdata["currentChunk"] = $data["event"][0]->currentChunk + 1;
                                        $postdata["step"] = EventSteps::SYMBOL_DRAFT;
                                    }

                                    $upd = $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator-sun/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("empty_words_error");
                                }
                            }
                        }

                        if (array_key_exists($data["event"][0]->currentChunk + 1, $sourceText["chunks"])) {
                            $data["next_step"] = "continue_alt";
                        } else {
                            $data["next_step"] = EventSteps::SELF_CHECK;
                        }

                        return View::make('Events/SUN/Translator')
                            ->nest('page', 'Events/SUN/SymbolsDraft')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::SELF_CHECK:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                                $data["comments"] = $this->getComments($data["event"][0]->eventID, $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation($data["event"][0]->trID, $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-sun/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);

                            if (isset($_POST["confirm_step"])) {
                                $chapters = [];
                                for ($i = 1; $i <= $data["event"][0]->chaptersNum; $i++) {
                                    $data["chapters"][$i] = [];
                                }

                                $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                foreach ($chaptersDB as $chapter) {
                                    $tmp["trID"] = $chapter["trID"];
                                    $tmp["memberID"] = $chapter["memberID"];
                                    $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                    $tmp["done"] = $chapter["done"];

                                    $chapters[$chapter["chapter"]] = $tmp;
                                }

                                $chapters[$data["event"][0]->currentChapter]["done"] = true;
                                $this->_model->updateChapter(["done" => true], ["eventID" => $data["event"][0]->eventID, "chapter" => $data["event"][0]->currentChapter]);

                                // Check if the member has another chapter to translate
                                // then redirect to preparation page
                                $nextChapter = 0;
                                $nextChapterDB = $this->_model->getNextChapter($data["event"][0]->eventID, Session::get("memberID"));
                                if (!empty($nextChapterDB))
                                    $nextChapter = $nextChapterDB[0]->chapter;

                                $kwCheck = (array)json_decode($data["event"][0]->kwCheck, true);
                                if (!array_key_exists($data["event"][0]->currentChapter, $kwCheck)) {
                                    $kwCheck[$data["event"][0]->currentChapter] = [
                                        "memberID" => 0,
                                        "done" => 0
                                    ];
                                }

                                $postdata = [
                                    "step" => EventSteps::NONE,
                                    "currentChapter" => 0,
                                    "currentChunk" => 0,
                                    "kwCheck" => json_encode($kwCheck)
                                ];

                                if ($nextChapter > 0) {
                                    $postdata["step"] = EventSteps::PRAY;
                                    $postdata["currentChapter"] = $nextChapter;
                                }

                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator-sun/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/SUN/Translator')
                            ->nest('page', 'Events/SUN/SelfCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/SUN/Translator')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/SUN/Translator')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function translatorOdbSun($eventID)
    {
        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEvents(Session::get("memberID"), $eventID);

        $title = "";

        if (!empty($data["event"])) {
            if ($data["event"][0]->bookProject != "sun" && $data["event"][0]->sourceBible != "odb") {
                Url::redirect("events/");
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . $data["event"][0]->sourceBible
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::TRANSLATING || $data["event"][0]->state == EventStates::TRANSLATED) {
                if ($data["event"][0]->step == EventSteps::NONE)
                    Url::redirect("events/information-odb-sun/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                switch ($data["event"][0]->step) {
                    case EventSteps::PRAY:
                        $sourceText = $this->getOtherSourceText($data);

                        if ($sourceText !== false) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-odb-sun/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {


                                $keys = array_keys($sourceText["text"]);
                                $chunks = array_map(function ($elm) {
                                    return [$elm];
                                }, $keys);
                                $this->_model->updateChapter(
                                    ["chunks" => json_encode($chunks)],
                                    [
                                        "eventID" => $data["event"][0]->eventID,
                                        "chapter" => $data['currentChapter']
                                    ]
                                );

                                $postdata = [
                                    "step" => EventSteps::CONSUME,
                                    "currentChapter" => $sourceText["currentChapter"],
                                    "currentChunk" => $sourceText["currentChunk"]
                                ];
                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);

                                Url::redirect('events/translator-odb-sun/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["event"][0]->justStarted = $data["event"][0]->kwCheck == "";
                        $data["next_step"] = EventSteps::CONSUME . "_odb";

                        return View::make('Events/ODBSUN/Translator')
                            ->nest('page', 'Events/ODBSUN/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::CONSUME:
                        $sourceText = $this->getOtherSourceText($data);

                        if ($sourceText !== false) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-odb-sun/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $postdata = [
                                    "step" => EventSteps::REARRANGE
                                ];

                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator-odb-sun/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = EventSteps::REARRANGE;

                        return View::make('Events/ODBSUN/Translator')
                            ->nest('page', 'Events/ODBSUN/Consume')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::REARRANGE:
                        $sourceText = $this->getOtherSourceText($data, true);

                        if ($sourceText !== false) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel
                                    ->getEventTranslation($data["event"][0]->trID, $data["event"][0]->currentChapter, $data["event"][0]->currentChunk);

                                if (!empty($translationData)) {
                                    $verses = json_decode($translationData[0]->translatedVerses, true);
                                    $data["words"] = $verses[EventMembers::TRANSLATOR]["words"];
                                }

                                // Skip if section is empty or it is a DATE section
                                $section = key($data["text"]);
                                if (trim($data["text"][$section]) == "" || $section == OdbSections::DATE) {
                                    $translationVerses = [
                                        EventMembers::TRANSLATOR => [
                                            "words" => "",
                                            "symbols" => "",
                                            "bt" => "",
                                            "verses" => [$section => trim($data["text"][$section])]
                                        ],
                                        EventMembers::L2_CHECKER => [
                                            "verses" => array()
                                        ],
                                        EventMembers::L3_CHECKER => [
                                            "verses" => array()
                                        ],
                                    ];

                                    $encoded = json_encode($translationVerses);
                                    $json_error = json_last_error();

                                    if ($json_error == JSON_ERROR_NONE) {
                                        $trData = [
                                            "projectID" => $data["event"][0]->projectID,
                                            "eventID" => $data["event"][0]->eventID,
                                            "trID" => $data["event"][0]->trID,
                                            "targetLang" => $data["event"][0]->targetLang,
                                            "bookProject" => $data["event"][0]->bookProject,
                                            "sort" => $data["event"][0]->sort,
                                            "bookCode" => $data["event"][0]->bookCode,
                                            "chapter" => $data["event"][0]->currentChapter,
                                            "chunk" => $data["event"][0]->currentChunk,
                                            "firstvs" => $sourceText["chunk"][0],
                                            "translatedVerses" => $encoded,
                                            "dateCreate" => date('Y-m-d H:i:s')
                                        ];

                                        if (empty($translationData))
                                            $this->_translationModel->createTranslation($trData);

                                        $postdata["step"] = EventSteps::SYMBOL_DRAFT;
                                        $postdata["currentChunk"] = 0;

                                        // If chapter is finished go to SYMBOL_DRAFT, otherwise go to the next chunk
                                        if (array_key_exists($data["event"][0]->currentChunk + 1, $sourceText["chunks"])) {
                                            // Current chunk is finished, go to next chunk
                                            $postdata["currentChunk"] = $data["event"][0]->currentChunk + 1;
                                            $postdata["step"] = EventSteps::REARRANGE;
                                        }

                                        $upd = $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                        Url::redirect('events/translator-odb-sun/' . $data["event"][0]->eventID);
                                    }
                                }
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-odb-sun/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);
                            $words = isset($_POST["draft"]) ? $_POST["draft"] : "";

                            if (isset($_POST["confirm_step"])) {
                                if (trim($words) != "") {
                                    $postdata["step"] = EventSteps::SYMBOL_DRAFT;
                                    $postdata["currentChunk"] = 0;

                                    // If chapter is finished go to SYMBOL_DRAFT, otherwise go to the next chunk
                                    if (array_key_exists($data["event"][0]->currentChunk + 1, $sourceText["chunks"])) {
                                        // Current chunk is finished, go to next chunk
                                        $postdata["currentChunk"] = $data["event"][0]->currentChunk + 1;
                                        $postdata["step"] = EventSteps::REARRANGE;
                                    }

                                    $upd = $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator-odb-sun/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("empty_words_error");
                                }
                            }
                        }

                        if (array_key_exists($data["event"][0]->currentChunk + 1, $sourceText["chunks"])) {
                            $data["next_step"] = "continue_alt";
                        } else {
                            $data["next_step"] = EventSteps::SYMBOL_DRAFT;
                        }

                        return View::make('Events/ODBSUN/Translator')
                            ->nest('page', 'Events/ODBSUN/WordsDraft')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::SYMBOL_DRAFT:
                        $sourceText = $this->getOtherSourceText($data, true);

                        if ($sourceText !== false) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel
                                    ->getEventTranslation($data["event"][0]->trID, $data["event"][0]->currentChapter, $data["event"][0]->currentChunk);

                                if (!empty($translationData)) {
                                    $verses = json_decode($translationData[0]->translatedVerses, true);
                                    $data["words"] = $verses[EventMembers::TRANSLATOR]["words"];
                                    $data["symbols"] = $verses[EventMembers::TRANSLATOR]["symbols"];
                                }

                                // Skip if section is empty or it is a DATE section
                                $section = key($data["text"]);
                                if (trim($data["text"][$section]) == "" || $section == OdbSections::DATE) {
                                    $postdata["step"] = EventSteps::SELF_CHECK;

                                    // If chapter is finished go to SELF_EDIT, otherwise go to the next chunk
                                    if (array_key_exists($data["event"][0]->currentChunk + 1, $sourceText["chunks"])) {
                                        // Current chunk is finished, go to next chunk
                                        $postdata["currentChunk"] = $data["event"][0]->currentChunk + 1;
                                        $postdata["step"] = EventSteps::SYMBOL_DRAFT;
                                    }

                                    $upd = $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator-odb-sun/' . $data["event"][0]->eventID);
                                }
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-odb-sun/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);
                            $symbols = isset($_POST["symbols"]) ? $_POST["symbols"] : "";

                            if (isset($_POST["confirm_step"])) {
                                if (trim($symbols) != "") {
                                    $postdata["step"] = EventSteps::SELF_CHECK;

                                    // If chapter is finished go to SELF_EDIT, otherwise go to the next chunk
                                    if (array_key_exists($data["event"][0]->currentChunk + 1, $sourceText["chunks"])) {
                                        // Current chunk is finished, go to next chunk
                                        $postdata["currentChunk"] = $data["event"][0]->currentChunk + 1;
                                        $postdata["step"] = EventSteps::SYMBOL_DRAFT;
                                    }

                                    $upd = $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator-odb-sun/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("empty_words_error");
                                }
                            }
                        }

                        if (array_key_exists($data["event"][0]->currentChunk + 1, $sourceText["chunks"])) {
                            $data["next_step"] = "continue_alt";
                        } else {
                            $data["next_step"] = EventSteps::SELF_CHECK;
                        }

                        return View::make('Events/ODBSUN/Translator')
                            ->nest('page', 'Events/ODBSUN/SymbolsDraft')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::SELF_CHECK:
                        $sourceText = $this->getOtherSourceText($data);

                        if ($sourceText !== false) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                                $data["comments"] = $this->getComments($data["event"][0]->eventID, $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation($data["event"][0]->trID, $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-odb-sun/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);

                            if (isset($_POST["confirm_step"])) {
                                $chapters = [];
                                for ($i = 1; $i <= $data["event"][0]->chaptersNum; $i++) {
                                    $data["chapters"][$i] = [];
                                }

                                $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                foreach ($chaptersDB as $chapter) {
                                    $tmp["trID"] = $chapter["trID"];
                                    $tmp["memberID"] = $chapter["memberID"];
                                    $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                    $tmp["done"] = $chapter["done"];

                                    $chapters[$chapter["chapter"]] = $tmp;
                                }

                                $chapters[$data["event"][0]->currentChapter]["done"] = true;
                                $this->_model->updateChapter(["done" => true], ["eventID" => $data["event"][0]->eventID, "chapter" => $data["event"][0]->currentChapter]);

                                // Check if the member has another chapter to translate
                                // then redirect to preparation page
                                $nextChapter = 0;
                                $nextChapterDB = $this->_model->getNextChapter($data["event"][0]->eventID, Session::get("memberID"));
                                if (!empty($nextChapterDB))
                                    $nextChapter = $nextChapterDB[0]->chapter;

                                $kwCheck = (array)json_decode($data["event"][0]->kwCheck, true);
                                if (!array_key_exists($data["event"][0]->currentChapter, $kwCheck)) {
                                    $kwCheck[$data["event"][0]->currentChapter] = [
                                        "memberID" => 0,
                                        "done" => 0
                                    ];
                                }

                                $postdata = [
                                    "step" => EventSteps::NONE,
                                    "currentChapter" => 0,
                                    "currentChunk" => 0,
                                    "kwCheck" => json_encode($kwCheck)
                                ];

                                if ($nextChapter > 0) {
                                    $postdata["step"] = EventSteps::PRAY;
                                    $postdata["currentChapter"] = $nextChapter;
                                }

                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator-odb-sun/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/ODBSUN/Translator')
                            ->nest('page', 'Events/ODBSUN/SelfCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/ODBSUN/Translator')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/ODBSUN/Translator')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function translatorRadio($eventID)
    {
        $data["menu"] = 1;
        $data["isCheckerPage"] = false;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEvents(Session::get("memberID"), $eventID);

        $title = "";

        if (!empty($data["event"])) {
            if ($data["event"][0]->bookProject != "rad") {
                Url::redirect("events/");
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . $data["event"][0]->sourceBible
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::TRANSLATING || $data["event"][0]->state == EventStates::TRANSLATED) {
                if ($data["event"][0]->step == EventSteps::NONE)
                    Url::redirect("events/information-rad/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                switch ($data["event"][0]->step) {
                    case EventSteps::PRAY:
                        $sourceText = $this->getOtherSourceText($data);

                        if ($sourceText !== false) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-rad/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $keys = array_keys($sourceText["text"]);
                                $chunks = array_map(function ($elm) {
                                    return [$elm];
                                }, $keys);

                                $this->_model->updateChapter(
                                    ["chunks" => json_encode($chunks)],
                                    [
                                        "eventID" => $data["event"][0]->eventID,
                                        "chapter" => $data['currentChapter']
                                    ]
                                );

                                $postdata = [
                                    "step" => EventSteps::CONSUME,
                                    "currentChapter" => $sourceText["currentChapter"],
                                    "currentChunk" => $sourceText["currentChunk"]
                                ];
                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);

                                Url::redirect('events/translator-rad/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["event"][0]->justStarted = $data["event"][0]->peerCheck == "";
                        $data["next_step"] = EventSteps::CONSUME . "_odb";

                        return View::make('Events/Radio/Translator')
                            ->nest('page', 'Events/Radio/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::CONSUME:
                        $sourceText = $this->getOtherSourceText($data);

                        if ($sourceText !== false) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-odb-sun/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $postdata = [
                                    "step" => EventSteps::MULTI_DRAFT
                                ];

                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator-rad/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = EventSteps::MULTI_DRAFT;

                        return View::make('Events/Radio/Translator')
                            ->nest('page', 'Events/Radio/Consume')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::MULTI_DRAFT:
                        $sourceText = $this->getOtherSourceText($data);

                        if ($sourceText !== false) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter
                                );

                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-rad/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $confirm_step = isset($_POST["confirm_step"]) ? $_POST["confirm_step"] : false;
                            if ($confirm_step) {
                                $chunks = isset($_POST["chunks"]) ? (array)$_POST["chunks"] : [];
                                $chunks = $this->_apiModel->testChunkRadio($chunks, $data["chunks"]);

                                if (!$chunks === false) {
                                    $postdata["step"] = EventSteps::SELF_CHECK;

                                    $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/translator-rad/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("wrong_chunks_error");
                                }
                            }
                        }

                        $data["next_step"] = EventSteps::SELF_CHECK;

                        return View::make('Events/Radio/Translator')
                            ->nest('page', 'Events/Radio/MultiDraft')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::SELF_CHECK:
                        $sourceText = $this->getOtherSourceText($data);

                        if ($sourceText !== false) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                                $data["comments"] = $this->getComments($data["event"][0]->eventID, $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-rad/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $this->_translationModel->updateTranslation(
                                    ["translateDone" => true],
                                    [
                                        "trID" => $data["event"][0]->trID,
                                        "chapter" => $data["event"][0]->currentChapter
                                    ]
                                );

                                $chapters = [];
                                for ($i = 1; $i <= $data["event"][0]->chaptersNum; $i++) {
                                    $data["chapters"][$i] = [];
                                }

                                $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                foreach ($chaptersDB as $chapter) {
                                    $tmp["trID"] = $chapter["trID"];
                                    $tmp["memberID"] = $chapter["memberID"];
                                    $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                    $tmp["done"] = $chapter["done"];

                                    $chapters[$chapter["chapter"]] = $tmp;
                                }

                                $chapters[$data["event"][0]->currentChapter]["done"] = true;

                                $this->_model->updateChapter(["done" => true], ["eventID" => $data["event"][0]->eventID, "chapter" => $data["event"][0]->currentChapter]);

                                // Check if the member has another chapter to translate
                                // then redirect to preparation page
                                $nextChapter = 0;
                                $nextChapterDB = $this->_model->getNextChapter($data["event"][0]->eventID, Session::get("memberID"));
                                if (!empty($nextChapterDB))
                                    $nextChapter = $nextChapterDB[0]->chapter;

                                // For the first checker
                                $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);
                                if (!array_key_exists($data['currentChapter'], $peerCheck)) {
                                    $peerCheck[$data['currentChapter']] = [
                                        "memberID" => 0,
                                        "done" => 0
                                    ];
                                }

                                $postdata = [
                                    "step" => EventSteps::NONE,
                                    "currentChapter" => 0,
                                    "currentChunk" => 0,
                                    "peerCheck" => json_encode($peerCheck)
                                ];

                                if ($nextChapter > 0) {
                                    $postdata["step"] = EventSteps::PRAY;
                                    $postdata["currentChapter"] = $nextChapter;
                                }

                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/translator-rad/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/Radio/Translator')
                            ->nest('page', 'Events/Radio/SelfCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/Radio/Translator')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/Radio/Translator')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function translatorObs($eventID)
    {
        $data["menu"] = 1;
        $data["isCheckerPage"] = false;
        $data["isPeerPage"] = false;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEvents(Session::get("memberID"), $eventID);

        if (!empty($data["event"])) {
            $event = $data["event"][0];

            if ($event->bookProject != "obs") {
                if (in_array($event->bookProject, ["udb", "ulb"]))
                    Url::redirect("events/translator/" . $eventID);
                else
                    Url::redirect("events/translator-" . $event->bookProject . "/" . $eventID);
            }

            $title = $event->name
                . " " . ($event->currentChapter > 0 ? $event->currentChapter : "")
                . " - " . $event->tLang
                . " - " . __($event->bookProject);

            if (($event->state == EventStates::TRANSLATING
                || $event->state == EventStates::TRANSLATED)) {
                if ($event->step == EventSteps::NONE)
                    Url::redirect("events/information-obs/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                switch ($event->step) {
                    case EventSteps::PRAY:

                        if ($event->currentChapter == 0) {
                            $nextChapter = $this->_model->getNextChapter($event->eventID, $event->myMemberID);
                            if (!empty($nextChapter)) {
                                $event->currentChapter = $nextChapter[0]->chapter;
                            }
                        }

                        // Get obs
                        $sourceTextObs = $this->resourcesRepo->getObs($event->resLangID, $event->currentChapter);

                        if (!$sourceTextObs) {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $event->trID]);
                            Url::redirect('events/translator-obs/' . $event->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $obsChunks = $this->_apiModel->getObsChunks($sourceTextObs);

                                $this->_model->updateChapter(
                                    ["chunks" => json_encode($obsChunks)],
                                    [
                                        "eventID" => $event->eventID,
                                        "chapter" => $event->currentChapter
                                    ]
                                );

                                $postdata = [
                                    "step" => EventSteps::BLIND_DRAFT,
                                    "currentChapter" => $event->currentChapter,
                                    "currentChunk" => 0
                                ];
                                $this->_model->updateTranslator($postdata, ["trID" => $event->trID]);

                                Url::redirect('events/translator-obs/' . $event->eventID);
                            }
                        }

                        // Check if translator just started translating of this book
                        $event->justStarted = $event->otherCheck == "";
                        $data["next_step"] = EventSteps::BLIND_DRAFT;

                        return View::make('Events/Obs/Translator')
                            ->nest('page', 'Events/Obs/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                    case EventSteps::BLIND_DRAFT:

                        // Get obs chapter
                        $sourceTextObs = $this->resourcesRepo->getObs($event->resLangID, $event->currentChapter);

                        $data["chunks"] = $this->_apiModel->getObsChunks($sourceTextObs);

                        if ($sourceTextObs) {
                            $data["obs"] = $sourceTextObs->chunks->get($event->currentChunk);

                            $translationData = $this->_translationModel->getEventTranslation(
                                $event->trID,
                                $event->currentChapter,
                                $event->currentChunk
                            );

                            if (!empty($translationData)) {
                                $verses = json_decode($translationData[0]->translatedVerses, true);
                                $data["translation"] = $verses[EventMembers::TRANSLATOR]["verses"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $event->trID]);
                            Url::redirect('events/translator-obs/' . $event->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $title = $_POST["draft"] ?? "";

                            if (isset($_POST["confirm_step"])) {
                                if (trim(strip_tags($title)) != "") {
                                    $postdata["step"] = EventSteps::SELF_CHECK;

                                    // If chapter is finished go to SELF_EDIT, otherwise go to the next chunk
                                    if (array_key_exists($event->currentChunk + 1, $data["chunks"])) {
                                        // Current chunk is finished, go to next chunk
                                        $postdata["currentChunk"] = $event->currentChunk + 1;
                                        $postdata["step"] = EventSteps::BLIND_DRAFT;
                                    }

                                    $this->_model->updateTranslator($postdata, ["trID" => $event->trID]);
                                    Url::redirect('events/translator-obs/' . $event->eventID);
                                } else {
                                    $error[] = __("empty_draft_verses_error");
                                }
                            }
                        }

                        if (array_key_exists($event->currentChunk + 1, $data["chunks"])) {
                            $data["next_step"] = EventSteps::BLIND_DRAFT;
                        } else {
                            $data["next_step"] = EventSteps::SELF_CHECK;
                        }

                        return View::make('Events/Obs/Translator')
                            ->nest('page', 'Events/Obs/BlindDraft')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::SELF_CHECK:

                        // Get obs chapter
                        $sourceTextObs = $this->resourcesRepo->getObs($event->resLangID, $event->currentChapter);

                        $data["chunks"] = $this->_apiModel->getObsChunks($sourceTextObs);

                        if ($sourceTextObs) {
                            $data["obs"] = $sourceTextObs->chunks;

                            $data["comments"] = $this->getComments(
                                $event->eventID,
                                $event->currentChapter);

                            $translationData = $this->_translationModel->getEventTranslation(
                                $event->trID,
                                $event->currentChapter);
                            $translation = array();

                            foreach ($translationData as $tv) {
                                $arr = json_decode($tv->translatedVerses, true);
                                $arr["tID"] = $tv->tID;
                                $translation[] = $arr;
                            }
                            $data["translation"] = $translation;
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::NONE], ["trID" => $event->trID]);
                            Url::redirect('events/translator-obs/' . $event->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $confirm_step = isset($_POST["confirm_step"]) ? $_POST["confirm_step"] : false;
                            if ($confirm_step) {
                                $chunks = isset($_POST["chunks"]) ? (array)$_POST["chunks"] : [];
                                $chunks = $this->_apiModel->testChunkObs($chunks, $data["obs"]);
                                if (!$chunks === false) {
                                    $this->_translationModel->updateTranslation(
                                        ["translateDone" => true],
                                        [
                                            "trID" => $event->trID,
                                            "chapter" => $event->currentChapter
                                        ]
                                    );

                                    $chapters = [];
                                    for ($i = 1; $i <= $event->chaptersNum; $i++) {
                                        $data["chapters"][$i] = [];
                                    }

                                    $chaptersDB = $this->_model->getChapters($event->eventID);

                                    foreach ($chaptersDB as $chapter) {
                                        $tmp["trID"] = $chapter["trID"];
                                        $tmp["memberID"] = $chapter["memberID"];
                                        $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                        $tmp["done"] = $chapter["done"];

                                        $chapters[$chapter["chapter"]] = $tmp;
                                    }

                                    $chapters[$event->currentChapter]["done"] = true;
                                    $this->_model->updateChapter(
                                        ["done" => true],
                                        ["eventID" => $event->eventID, "chapter" => $event->currentChapter]
                                    );

                                    // Check if the member has another chapter to translate
                                    // then redirect to preparation page
                                    $nextChapter = 0;
                                    $nextChapterDB = $this->_model->getNextChapter($event->eventID, Session::get("memberID"));

                                    if (!empty($nextChapterDB))
                                        $nextChapter = $nextChapterDB[0]->chapter;

                                    $otherCheck = (array)json_decode($event->otherCheck, true);
                                    if (!array_key_exists($event->currentChapter, $otherCheck)) {
                                        $otherCheck[$event->currentChapter] = [
                                            "memberID" => 0,
                                            "done" => 0
                                        ];
                                    }

                                    $postdata = [
                                        "step" => EventSteps::NONE,
                                        "currentChapter" => 0,
                                        "currentChunk" => 0,
                                        "otherCheck" => json_encode($otherCheck)
                                    ];

                                    if ($nextChapter > 0) {
                                        $postdata["step"] = EventSteps::PRAY;
                                        $postdata["currentChapter"] = $nextChapter;
                                    }

                                    $this->_model->updateTranslator($postdata, ["trID" => $event->trID]);
                                    Url::redirect('events/translator-obs/' . $event->eventID);
                                } else {
                                    $error[] = __("wrong_chunks_error");
                                }
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/Obs/Translator')
                            ->nest('page', 'Events/Obs/SelfCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/Obs/Translator')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/Obs/Translator')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function checker($eventID, $memberID, $chapter)
    {
        $response = array("success" => false, "errors" => "");

        $title = "";

        if (!isset($error)) {
            $data["event"] = $this->_model->getMemberEventsForChecker(Session::get("memberID"), $eventID, $memberID, $chapter);

            if (!empty($data["event"])) {
                if ($data["event"][0]->step != EventSteps::FINISHED) {
                    if (in_array($data["event"][0]->step, [EventSteps::PEER_REVIEW, EventSteps::KEYWORD_CHECK, EventSteps::CONTENT_REVIEW])) {
                        $data["turn"] = $this->makeTurnCredentials();

                        $data["comments"] = $this->getComments($data["event"][0]->eventID, $data["event"][0]->currentChapter);
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText) && !array_key_exists("error", $sourceText)) {
                            $translationData = $this->_translationModel->getEventTranslation($data["event"][0]->trID, $data["event"][0]->currentChapter);
                            $translation = array();

                            foreach ($translationData as $tv) {
                                $arr = json_decode($tv->translatedVerses, true);
                                $arr["tID"] = $tv->tID;
                                $translation[] = $arr;
                            }

                            $data = $sourceText;
                            $data["translation"] = $translation;
                        } else {
                            $error[] = $sourceText["error"];
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);

                            if (isset($_POST["confirm_step"])) {
                                if ($data["event"][0]->step == EventSteps::PEER_REVIEW) {
                                    $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);
                                    $peerCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                    $postdata["peerCheck"] = json_encode($peerCheck);
                                } elseif ($data["event"][0]->step == EventSteps::KEYWORD_CHECK) {
                                    $kwCheck = (array)json_decode($data["event"][0]->kwCheck, true);
                                    $kwCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                    $postdata["kwCheck"] = json_encode($kwCheck);
                                } else {
                                    $crCheck = (array)json_decode($data["event"][0]->crCheck, true);
                                    $crCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                    $postdata["crCheck"] = json_encode($crCheck);
                                }

                                $this->_model->updateTranslator($postdata, array("trID" => $data["event"][0]->trID));

                                $response["success"] = true;
                                echo json_encode($response);
                                exit;
                            }
                        }
                    } else {
                        $error[] = __("checker_translator_not_ready_error");
                    }
                } else {
                    $data["success"] = __("translator_event_finished_success");
                    $data["error"] = "";
                }

                $title = $data["event"][0]->bookName . " - " . $data["event"][0]->tLang . " - " . __($data["event"][0]->bookProject);
            } else {
                $error[] = __("checker_event_error");
                $title = "Error";
            }
        }

        $data["menu"] = 1;
        $data["isCheckerPage"] = true;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;

        $page = null;
        if (!isset($error)) {
            switch ($data["event"][0]->step) {
                case EventSteps::PEER_REVIEW:
                    $page = "Events/L1/CheckerPeerReview";
                    break;

                case EventSteps::KEYWORD_CHECK:
                    $page = "Events/L1/CheckerKeywordCheck";
                    break;

                case EventSteps::CONTENT_REVIEW:
                    $page = "Events/L1/CheckerContentReview";
                    break;

                default:
                    $page = null;
                    break;
            }
        }

        $data["next_step"] = "continue_alt";

        $view = View::make('Events/L1/Translator')
            ->shares("title", $title)
            ->shares("data", $data)
            ->shares("error", @$error);

        if ($page != null) $view->nest('page', $page);

        return $view;
    }

    public function checkerNotes($eventID, $memberID, $chapter)
    {
        $isXhr = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isXhr = true;
            $response["success"] = false;
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["isCheckerPage"] = true;
        $data["isPeerPage"] = false;
        $data["event"] = $this->_model->getMemberEventsForNotes(
            Session::get("memberID"), $eventID, $memberID, $chapter);

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["tn"])) {
                Url::redirect("events/");
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > -1 ? ($data["event"][0]->currentChapter == 0
                    ? __("front") : $data["event"][0]->currentChapter) : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::TRANSLATING || $data["event"][0]->state == EventStates::TRANSLATED) {
                $data["turn"] = $this->makeTurnCredentials();

                $chapters = $this->_model->getChapters($eventID, null, $chapter);
                $data["event"][0]->chunks = [];
                if (!empty($chapters)) {
                    $data["event"][0]->chunks = $chapters[0]["chunks"];
                }
                $otherCheck = (array)json_decode($data["event"][0]->otherCheck, true);

                switch ($data["event"][0]->step) {
                    case EventSteps::PRAY:
                        // Get scripture text
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText) && !array_key_exists("error", $sourceText))
                            $data = $sourceText;

                        // Get notes
                        $sourceTextNotes = $this->getNotesSourceText($data);

                        if ($sourceTextNotes !== false) {
                            if (!array_key_exists("error", $sourceTextNotes)) {
                                $data = $sourceTextNotes;
                            } else {
                                $error[] = $sourceTextNotes["error"];
                                $data["error"] = $sourceTextNotes["error"];
                            }
                        } else {
                            $otherCheck[$data["event"][0]->currentChapter]["done"] = 6;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/checker-tn/' . $data["event"][0]->eventID .
                                "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (array_key_exists($data["event"][0]->currentChapter, $otherCheck)) {
                                    $otherCheck[$data["event"][0]->currentChapter]["done"] = !$data["nosource"] ? 1 : 3;
                                }

                                $this->_model->updateTranslator([
                                    "otherCheck" => json_encode($otherCheck)
                                ], ["trID" => $data["event"][0]->trID]);

                                Url::redirect('events/checker-tn/' . $data["event"][0]->eventID .
                                    "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                            }
                        }

                        $data["event"][0]->justStarted = true;
                        $data["next_step"] = EventSteps::CONSUME . "_tn";

                        return View::make('Events/Notes/Translator')
                            ->nest('page', 'Events/Notes/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                    case EventSteps::CONSUME: // Consume chapter
                        // Get scripture text
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText) && !array_key_exists("error", $sourceText))
                            $data = $sourceText;

                        // Get notes
                        $sourceTextNotes = $this->getNotesSourceText($data);

                        if ($sourceTextNotes !== false) {
                            if (!array_key_exists("error", $sourceTextNotes)) {
                                $data = $sourceTextNotes;

                                if (isset($data["nosource"]) && $data["nosource"]) {
                                    // 3 for SELF-CHECK step
                                    $otherCheck[$data["event"][0]->currentChapter]["done"] = 3;
                                    $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/checker-tn/' . $data["event"][0]->eventID .
                                        "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                                }
                            } else {
                                $error[] = $sourceTextNotes["error"];
                                $data["error"] = $sourceTextNotes["error"];
                            }
                        } else {
                            // 6 for the chapter finished
                            $otherCheck[$data["event"][0]->currentChapter]["done"] = 6;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/checker-tn/' . $data["event"][0]->eventID .
                                "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                // 2 for HIGHLIGHT step
                                $otherCheck[$data["event"][0]->currentChapter]["done"] = 2;

                                $this->_model->updateTranslator([
                                    "otherCheck" => json_encode($otherCheck)
                                ], ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/checker-tn/' . $data["event"][0]->eventID .
                                    "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                            }
                        }

                        $data["next_step"] = EventSteps::HIGHLIGHT . "_tn";

                        return View::make('Events/Notes/Translator')
                            ->nest('page', 'Events/Notes/Consume')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::HIGHLIGHT: // Highlight chapter
                        // Get scripture text
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText) && !array_key_exists("error", $sourceText))
                            $data = $sourceText;

                        // Get notes
                        $sourceTextNotes = $this->getNotesSourceText($data);

                        if ($sourceTextNotes !== false) {
                            if (!array_key_exists("error", $sourceTextNotes)) {
                                $data = $sourceTextNotes;

                                if (isset($data["nosource"]) && $data["nosource"]) {
                                    $otherCheck[$data["event"][0]->currentChapter]["done"] = 3;
                                    $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/checker-tn/' . $data["event"][0]->eventID .
                                        "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                                }
                            } else {
                                $error[] = $sourceTextNotes["error"];
                                $data["error"] = $sourceTextNotes["error"];
                            }
                        } else {
                            $otherCheck[$data["event"][0]->currentChapter]["done"] = 6;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/checker-tn/' . $data["event"][0]->eventID .
                                "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                // 3 for SELF_CHECK step
                                $otherCheck[$data["event"][0]->currentChapter]["done"] = 3;

                                $this->_model->updateTranslator([
                                    "otherCheck" => json_encode($otherCheck)
                                ], ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/checker-tn/' . $data["event"][0]->eventID .
                                    "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                            }
                        }

                        $data["next_step"] = EventSteps::SELF_CHECK . "_tn_chk";

                        return View::make('Events/Notes/Translator')
                            ->nest('page', 'Events/Notes/Highlight')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::SELF_CHECK: // Criteria Check Notes
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText) && !array_key_exists("error", $sourceText))
                            $data = $sourceText;

                        // Get notes

                        $sourceTextNotes = $this->getNotesSourceText($data);
                        $translation = array();

                        if ($sourceTextNotes !== false) {
                            if (!array_key_exists("error", $sourceTextNotes)) {
                                $data = $sourceTextNotes;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter);

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceTextNotes["error"];
                                $data["error"] = $sourceTextNotes["error"];
                            }
                        } else {
                            $otherCheck[$data["event"][0]->currentChapter]["done"] = 6;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/checker-tn/' . $data["event"][0]->eventID .
                                "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                // Update CHECKER if it's empty
                                foreach ($translation as $tr) {
                                    if (empty($tr[EventMembers::CHECKER]["verses"])) {
                                        $tr[EventMembers::CHECKER]["verses"] = $tr[EventMembers::TRANSLATOR]["verses"];
                                        $tID = $tr["tID"];
                                        unset($tr["tID"]);
                                        $this->_translationModel->updateTranslation(
                                            ["translatedVerses" => json_encode($tr)],
                                            ["tID" => $tID]
                                        );
                                    }
                                }

                                $postdata = [];

                                // 4 for KEYWORD_CHECK step
                                $otherCheck[$data["event"][0]->currentChapter]["done"] = 4;
                                $postdata["otherCheck"] = json_encode($otherCheck);

                                if (isset($data["nosource"]) && $data["nosource"]) {
                                    // 5 for PEER_REVIEW step
                                    $otherCheck[$data["event"][0]->currentChapter]["done"] = 5;

                                    $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);
                                    $peerCheck[$data["event"][0]->currentChapter] = [
                                        "memberID" => 0,
                                        "done" => 0
                                    ];

                                    $postdata["otherCheck"] = json_encode($otherCheck);
                                    $postdata["peerCheck"] = json_encode($peerCheck);
                                }

                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/checker-tn/' . $data["event"][0]->eventID .
                                    "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                            }
                        }

                        $data["next_step"] = EventSteps::KEYWORD_CHECK . "_tn";

                        return View::make('Events/Notes/Translator')
                            ->nest('page', 'Events/Notes/SelfCheckChecker')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::KEYWORD_CHECK: // Highlight Check Notes
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText) && !array_key_exists("error", $sourceText))
                            $data = $sourceText;

                        // Get notes
                        $sourceTextNotes = $this->getNotesSourceText($data);
                        $translation = array();

                        if ($sourceTextNotes !== false) {
                            if (!array_key_exists("error", $sourceTextNotes)) {
                                $data = $sourceTextNotes;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter);

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceTextNotes["error"];
                                $data["error"] = $sourceTextNotes["error"];
                            }
                        } else {
                            $otherCheck[$data["event"][0]->currentChapter]["done"] = 6;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/checker-tn/' . $data["event"][0]->eventID .
                                "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                // 5 for PEER_REVIEW step
                                $otherCheck[$data["event"][0]->currentChapter]["done"] = 5;

                                $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);
                                $peerCheck[$data["event"][0]->currentChapter] = [
                                    "memberID" => 0,
                                    "done" => 0
                                ];

                                $postdata = [];
                                $postdata["otherCheck"] = json_encode($otherCheck);
                                $postdata["peerCheck"] = json_encode($peerCheck);

                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                Url::redirect('events/checker-tn/' . $data["event"][0]->eventID .
                                    "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                            }
                        }

                        $data["next_step"] = EventSteps::PEER_REVIEW . "_tn";

                        return View::make('Events/Notes/Translator')
                            ->nest('page', 'Events/Notes/HighlightChecker')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::PEER_REVIEW:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText) && !array_key_exists("error", $sourceText))
                            $data = $sourceText;

                        // Get notes
                        $sourceTextNotes = $this->getNotesSourceText($data);

                        if ($sourceTextNotes !== false) {
                            if (!array_key_exists("error", $sourceTextNotes)) {
                                $data = $sourceTextNotes;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter);

                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceTextNotes["error"];
                                $data["error"] = $sourceTextNotes["error"];
                            }
                        } else {
                            $otherCheck[$data["event"][0]->currentChapter]["done"] = 6;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/checker-tn/' . $data["event"][0]->eventID .
                                "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);

                                if ($data["event"][0]->peer == 1) {
                                    if (isset($peerCheck[$data['currentChapter']]) &&
                                        $peerCheck[$data['currentChapter']]["done"]) {
                                        // 6 for chapter finished
                                        $otherCheck[$data['currentChapter']]["done"] = 6;

                                        $this->_model->updateTranslator(
                                            ["otherCheck" => json_encode($otherCheck)],
                                            ["trID" => $data["event"][0]->trID]);

                                        $chapters = [];
                                        for ($i = 0; $i <= $data["event"][0]->chaptersNum; $i++) {
                                            $data["chapters"][$i] = [];
                                        }

                                        $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                        foreach ($chaptersDB as $chapter) {
                                            $tmp["trID"] = $chapter["trID"];
                                            $tmp["memberID"] = $chapter["memberID"];
                                            $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                            $tmp["done"] = $chapter["done"];
                                            $tmp["checked"] = $chapter["checked"];

                                            $chapters[$chapter["chapter"]] = $tmp;
                                        }

                                        $chapters[$data["event"][0]->currentChapter]["checked"] = true;
                                        $this->_model->updateChapter(["checked" => true], [
                                            "eventID" => $data["event"][0]->eventID,
                                            "chapter" => $data["event"][0]->currentChapter]);

                                        // Check if whole scripture is finished
                                        if ($this->checkBookFinished($chapters, $data["event"][0]->chaptersNum + 1, true))
                                            $this->_model->updateEvent([
                                                "state" => EventStates::TRANSLATED,
                                                "dateTo" => date("Y-m-d H:i:s", time())],
                                                ["eventID" => $data["event"][0]->eventID]);

                                        Url::redirect('events');
                                    } else {
                                        $error[] = __("checker_not_ready_error");
                                    }
                                } else {
                                    $peerCheck[$data['currentChapter']]["done"] = 1;
                                    $this->_model->updateTranslator(
                                        ["peerCheck" => json_encode($peerCheck)],
                                        ["trID" => $data["event"][0]->trID]);

                                    $response["success"] = true;
                                    echo json_encode($response);
                                    exit;
                                }
                            }
                        }

                        if ($data["event"][0]->peer == 1)
                            $page = "Events/Notes/PeerReview";
                        else {
                            $page = "Events/Notes/CheckerPeerReview";
                            $data["isPeerPage"] = true;
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/Notes/Translator')
                            ->nest('page', $page)
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $error[] = __("checker_event_error");
                $title = "Error";
            }
        } else {
            $error[] = __("checker_event_error");
            $title = "Error";
        }

        return View::make('Events/Notes/Translator')
            ->shares("title", $title)
            ->shares("data", $data)
            ->shares("error", @$error);
    }

    /**
     * View for Keyword-Check and Peer-Review in Questions event
     * @param $eventID
     * @param $memberID
     * @param $chapter
     * @return View
     */
    public function checkerQuestions($eventID, $memberID, $chapter)
    {
        $isXhr = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isXhr = true;
            $response["success"] = false;
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["isCheckerPage"] = true;
        $data["isPeerPage"] = false;
        $data["event"] = $this->_model->getMemberEventsForOther(
            Session::get("memberID"), $eventID, $memberID, $chapter);

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["tq"])) {
                Url::redirect("events/");
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::TRANSLATING || $data["event"][0]->state == EventStates::TRANSLATED) {
                $data["turn"] = $this->makeTurnCredentials();

                $chapters = $this->_model->getChapters($eventID, null, $chapter);
                $data["event"][0]->chunks = [];
                if (!empty($chapters)) {
                    $data["event"][0]->chunks = $chapters[0]["chunks"];
                }
                $data["isCheckerPage"] = true;
                $otherCheck = (array)json_decode($data["event"][0]->otherCheck, true);

                switch ($data["event"][0]->step) {
                    case EventSteps::PRAY:
                        // Get questions
                        $sourceTextQuestions = $this->getQuestionsSourceText($data);

                        if ($sourceTextQuestions !== false) {
                            if (!array_key_exists("error", $sourceTextQuestions)) {
                                $data = $sourceTextQuestions;
                            } else {
                                $error[] = $sourceTextQuestions["error"];
                                $data["error"] = $sourceTextQuestions["error"];
                            }
                        } else {
                            $otherCheck[$data["event"][0]->currentChapter]["done"] = 3;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/checker-tq/' . $data["event"][0]->eventID .
                                "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (array_key_exists($data["event"][0]->currentChapter, $otherCheck)) {
                                    $otherCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                }

                                $this->_model->updateTranslator([
                                    "otherCheck" => json_encode($otherCheck)
                                ], ["trID" => $data["event"][0]->trID]);

                                Url::redirect('events/checker-tq/' . $data["event"][0]->eventID .
                                    "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                            }
                        }

                        $data["event"][0]->justStarted = true;
                        $data["next_step"] = EventSteps::KEYWORD_CHECK;

                        return View::make('Events/Questions/Translator')
                            ->nest('page', 'Events/Questions/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                    case EventSteps::KEYWORD_CHECK:

                        // Get questions
                        $sourceTextQuestions = $this->getQuestionsSourceText($data);
                        if ($sourceTextQuestions !== false) {
                            if (!array_key_exists("error", $sourceTextQuestions)) {
                                $data = $sourceTextQuestions;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceTextQuestions["error"];
                                $data["error"] = $sourceTextQuestions["error"];
                            }
                        } else {
                            $otherCheck[$data["event"][0]->currentChapter]["done"] = 3;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/checker-tq/' . $data["event"][0]->eventID .
                                "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $confirm_step = isset($_POST["confirm_step"]) ? $_POST["confirm_step"] : false;
                            if ($confirm_step) {
                                $chunks = isset($_POST["chunks"]) ? (array)$_POST["chunks"] : [];

                                $empty = array_filter($chunks, function ($elm) {
                                    return empty(Tools::trim(strip_tags($elm)));
                                });

                                if (empty($empty)) {
                                    // Update Checker if it's empty
                                    foreach ($translation as $tr) {
                                        if (empty($tr[EventMembers::CHECKER]["verses"])) {
                                            $tr[EventMembers::CHECKER]["verses"] = $tr[EventMembers::TRANSLATOR]["verses"];
                                            $tID = $tr["tID"];
                                            unset($tr["tID"]);
                                            $this->_translationModel->updateTranslation(
                                                ["translatedVerses" => json_encode($tr)],
                                                ["tID" => $tID]
                                            );
                                        }
                                    }
                                } else {
                                    $error[] = __("wrong_chunks_error");
                                }

                                if (!isset($error)) {
                                    // 2 for PEER_REVIEW step
                                    $otherCheck[$data["event"][0]->currentChapter]["done"] = 2;

                                    $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);
                                    if (!array_key_exists($data['currentChapter'], $peerCheck)) {
                                        $peerCheck[$data["event"][0]->currentChapter] = [
                                            "memberID" => 0,
                                            "done" => 0
                                        ];
                                    }

                                    $postdata = [
                                        "otherCheck" => json_encode($otherCheck),
                                        "peerCheck" => json_encode($peerCheck)
                                    ];

                                    $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/checker-tq/' . $data["event"][0]->eventID .
                                        "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                                }
                            }
                        }

                        $data["next_step"] = EventSteps::PEER_REVIEW . "_tq";

                        return View::make('Events/Questions/Translator')
                            ->nest('page', 'Events/Questions/KeywordCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::PEER_REVIEW:
                        // Get questions
                        $sourceTextQuestions = $this->getQuestionsSourceText($data);

                        if ($sourceTextQuestions !== false) {
                            if (!array_key_exists("error", $sourceTextQuestions)) {
                                $data = $sourceTextQuestions;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter);

                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceTextQuestions["error"];
                                $data["error"] = $sourceTextQuestions["error"];
                            }
                        } else {
                            $otherCheck[$data["event"][0]->currentChapter]["done"] = 3;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/checker-tq/' . $data["event"][0]->eventID .
                                "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);

                                if ($data["event"][0]->peer == 1) {
                                    if (isset($peerCheck[$data['currentChapter']]) &&
                                        $peerCheck[$data['currentChapter']]["done"]) {
                                        // 3 for chapter finished
                                        $otherCheck[$data['currentChapter']]["done"] = 3;

                                        $this->_model->updateTranslator(
                                            ["otherCheck" => json_encode($otherCheck)],
                                            ["trID" => $data["event"][0]->trID]);

                                        $chapters = [];
                                        for ($i = 1; $i <= $data["event"][0]->chaptersNum; $i++) {
                                            $data["chapters"][$i] = [];
                                        }

                                        $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                        foreach ($chaptersDB as $chapter) {
                                            $tmp["trID"] = $chapter["trID"];
                                            $tmp["memberID"] = $chapter["memberID"];
                                            $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                            $tmp["done"] = $chapter["done"];
                                            $tmp["checked"] = $chapter["checked"];

                                            $chapters[$chapter["chapter"]] = $tmp;
                                        }

                                        $chapters[$data["event"][0]->currentChapter]["checked"] = true;
                                        $this->_model->updateChapter(["checked" => true], [
                                            "eventID" => $data["event"][0]->eventID,
                                            "chapter" => $data["event"][0]->currentChapter]);

                                        // Check if whole scripture is finished
                                        if ($this->checkBookFinished($chapters, $data["event"][0]->chaptersNum, true))
                                            $this->_model->updateEvent([
                                                "state" => EventStates::TRANSLATED,
                                                "dateTo" => date("Y-m-d H:i:s", time())],
                                                ["eventID" => $data["event"][0]->eventID]);


                                        Url::redirect('events');
                                    } else {
                                        $error[] = __("checker_not_ready_error");
                                    }
                                } else {
                                    $peerCheck[$data['currentChapter']]["done"] = 1;
                                    $this->_model->updateTranslator(
                                        ["peerCheck" => json_encode($peerCheck)],
                                        ["trID" => $data["event"][0]->trID]);

                                    $response["success"] = true;
                                    echo json_encode($response);
                                    exit;
                                }
                            }
                        }

                        if ($data["event"][0]->peer == 1)
                            $page = "Events/Questions/PeerReview";
                        else {
                            $page = "Events/Questions/CheckerPeerReview";
                            $data["isPeerPage"] = true;
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/Questions/Translator')
                            ->nest('page', $page)
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $error[] = __("checker_event_error");
                $title = "Error";
            }
        } else {
            $error[] = __("checker_event_error");
            $title = "Error";
        }

        return View::make('Events/Questions/Translator')
            ->shares("title", $title)
            ->shares("data", $data)
            ->shares("error", @$error);
    }

    /**
     * View for Keyword-Check and Peer-Review in Words event
     * @param $eventID
     * @param $memberID
     * @param $chapter
     * @return View
     */
    public function checkerWords($eventID, $memberID, $chapter)
    {
        $isXhr = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isXhr = true;
            $response["success"] = false;
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["isCheckerPage"] = true;
        $data["isPeerPage"] = false;
        $data["event"] = $this->_model->getMemberEventsForOther(
            Session::get("memberID"), $eventID, $memberID, $chapter);

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["tw"])) {
                Url::redirect("events/");
            }

            $title = $data["event"][0]->name . " - " . $data["event"][0]->tLang . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::TRANSLATING || $data["event"][0]->state == EventStates::TRANSLATED) {
                $data["turn"] = $this->makeTurnCredentials();

                $chapters = $this->_model->getChapters($eventID, null, $chapter);
                $data["event"][0]->chunks = [];
                if (!empty($chapters)) {
                    $data["event"][0]->chunks = $chapters[0]["chunks"];
                }
                $otherCheck = (array)json_decode($data["event"][0]->otherCheck, true);

                switch ($data["event"][0]->step) {
                    case EventSteps::PRAY:
                        // Get questions
                        $sourceTextWords = $this->getWordsSourceText($data);

                        if ($sourceTextWords !== false) {
                            if (!array_key_exists("error", $sourceTextWords)) {
                                $data = $sourceTextWords;
                            } else {
                                $error[] = $sourceTextWords["error"];
                                $data["error"] = $sourceTextWords["error"];
                            }
                        } else {
                            $otherCheck[$data["event"][0]->currentChapter]["done"] = 3;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/checker-tw/' . $data["event"][0]->eventID .
                                "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (array_key_exists($data["event"][0]->currentChapter, $otherCheck)) {
                                    $otherCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                }

                                $this->_model->updateTranslator([
                                    "otherCheck" => json_encode($otherCheck)
                                ], ["trID" => $data["event"][0]->trID]);

                                Url::redirect('events/checker-tw/' . $data["event"][0]->eventID .
                                    "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                            }
                        }

                        $data["event"][0]->justStarted = true;
                        $data["next_step"] = EventSteps::KEYWORD_CHECK;

                        return View::make('Events/Words/Translator')
                            ->nest('page', 'Events/Words/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                    case EventSteps::KEYWORD_CHECK:

                        // Get notes
                        $sourceTextWords = $this->getWordsSourceText($data);
                        if ($sourceTextWords !== false) {
                            if (!array_key_exists("error", $sourceTextWords)) {
                                $data = $sourceTextWords;

                                $title = $data["event"][0]->name
                                    . " " . ($data["event"][0]->currentChapter > 0
                                        ? " [" . $data["group"][0] . "..." . $data["group"][sizeof($data["group"]) - 1] . "]"
                                        : "")
                                    . " - " . $data["event"][0]->tLang
                                    . " - " . __($data["event"][0]->bookProject);

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceTextWords["error"];
                                $data["error"] = $sourceTextWords["error"];
                            }
                        } else {
                            $otherCheck[$data["event"][0]->currentChapter]["done"] = 3;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/checker-tw/' . $data["event"][0]->eventID .
                                "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $confirm_step = isset($_POST["confirm_step"]) ? $_POST["confirm_step"] : false;
                            if ($confirm_step) {
                                $chunks = isset($_POST["chunks"]) ? (array)$_POST["chunks"] : [];

                                $empty = array_filter($chunks, function ($elm) {
                                    return empty(Tools::trim(strip_tags($elm)));
                                });

                                if (empty($empty)) {
                                    // Update Checker if it's empty
                                    foreach ($translation as $tr) {
                                        if (empty($tr[EventMembers::CHECKER]["verses"])) {
                                            $tr[EventMembers::CHECKER]["verses"] = $tr[EventMembers::TRANSLATOR]["verses"];
                                            $tID = $tr["tID"];
                                            unset($tr["tID"]);
                                            $this->_translationModel->updateTranslation(
                                                ["translatedVerses" => json_encode($tr)],
                                                ["tID" => $tID]
                                            );
                                        }
                                    }
                                } else {
                                    $error[] = __("wrong_chunks_error");
                                }

                                if (!isset($error)) {
                                    // 2 for PEER_REVIEW step
                                    $otherCheck[$data["event"][0]->currentChapter]["done"] = 2;

                                    $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);
                                    if (!array_key_exists($data['currentChapter'], $peerCheck)) {
                                        $peerCheck[$data["event"][0]->currentChapter] = [
                                            "memberID" => 0,
                                            "done" => 0
                                        ];
                                    }

                                    $postdata = [
                                        "otherCheck" => json_encode($otherCheck),
                                        "peerCheck" => json_encode($peerCheck)
                                    ];

                                    $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/checker-tw/' . $data["event"][0]->eventID .
                                        "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                                }
                            }
                        }

                        $data["next_step"] = EventSteps::PEER_REVIEW . "_tw";

                        return View::make('Events/Words/Translator')
                            ->nest('page', 'Events/Words/KeywordCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::PEER_REVIEW:

                        // Get notes
                        $sourceTextWords = $this->getWordsSourceText($data);
                        if ($sourceTextWords !== false) {
                            if (!array_key_exists("error", $sourceTextWords)) {
                                $data = $sourceTextWords;

                                $title = $data["event"][0]->name
                                    . " " . ($data["event"][0]->currentChapter > 0
                                        ? " [" . $data["group"][0] . "..." . $data["group"][sizeof($data["group"]) - 1] . "]"
                                        : "")
                                    . " - " . $data["event"][0]->tLang
                                    . " - " . __($data["event"][0]->bookProject);

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceTextWords["error"];
                                $data["error"] = $sourceTextWords["error"];
                            }
                        } else {
                            $this->_model->updateTranslator(["step" => EventSteps::FINISHED], ["trID" => $data["event"][0]->trID]);
                            Url::redirect('events/translator-tw/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $confirm_step = isset($_POST["confirm_step"]) ? $_POST["confirm_step"] : false;
                            if ($confirm_step) {
                                $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);

                                if ($data["event"][0]->peer == 1) {
                                    if (isset($peerCheck[$data['currentChapter']]) &&
                                        $peerCheck[$data['currentChapter']]["done"]) {
                                        // 3 for chapter finished
                                        $otherCheck[$data['currentChapter']]["done"] = 3;

                                        $this->_model->updateTranslator(
                                            ["otherCheck" => json_encode($otherCheck)],
                                            ["trID" => $data["event"][0]->trID]);

                                        $tw_groups = $this->_model->getTwGroups([
                                            "eventID" => $data["event"][0]->eventID
                                        ]);

                                        $chapters = [];
                                        foreach ($tw_groups as $group) {
                                            $data["chapters"][$group->groupID] = [];
                                        }

                                        $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                        foreach ($chaptersDB as $chapter) {
                                            $tmp["trID"] = $chapter["trID"];
                                            $tmp["memberID"] = $chapter["memberID"];
                                            $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                            $tmp["done"] = $chapter["done"];
                                            $tmp["checked"] = $chapter["checked"];

                                            $chapters[$chapter["chapter"]] = $tmp;
                                        }

                                        $chapters[$data["event"][0]->currentChapter]["checked"] = true;
                                        $this->_model->updateChapter(["checked" => true], [
                                            "eventID" => $data["event"][0]->eventID,
                                            "chapter" => $data["event"][0]->currentChapter]);

                                        Url::redirect('events');
                                    } else {
                                        $error[] = __("checker_not_ready_error");
                                    }
                                } else {
                                    $peerCheck[$data['currentChapter']]["done"] = 1;
                                    $this->_model->updateTranslator(
                                        ["peerCheck" => json_encode($peerCheck)],
                                        ["trID" => $data["event"][0]->trID]);

                                    $response["success"] = true;
                                    echo json_encode($response);
                                    exit;
                                }
                            }
                        }

                        if ($data["event"][0]->peer == 1)
                            $page = "Events/Words/PeerReview";
                        else {
                            $page = "Events/Words/CheckerPeerReview";
                            $data["isPeerPage"] = true;
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/Words/Translator')
                            ->nest('page', $page)
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $error[] = __("checker_event_error");
                $title = "Error";
            }
        } else {
            $error[] = __("checker_event_error");
            $title = "Error";
        }

        return View::make('Events/Words/Translator')
            ->shares("title", $title)
            ->shares("data", $data)
            ->shares("error", @$error);
    }

    /**
     * View for Theo check and V-b-v check in SUN event
     * @param $eventID
     * @param $memberID
     * @param $chapter
     * @return View
     */
    public function checkerSun($eventID, $memberID, $chapter)
    {
        $isXhr = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isXhr = true;
            $response["success"] = false;
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEventsForSun(
            Session::get("memberID"), $eventID, $memberID, $chapter);

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["sun"])) {
                Url::redirect("events/");
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::TRANSLATING || $data["event"][0]->state == EventStates::TRANSLATED) {
                $data["turn"] = $this->makeTurnCredentials();

                $chapters = $this->_model->getChapters($eventID, null, $chapter);
                $data["event"][0]->chunks = [];
                if (!empty($chapters)) {
                    $data["event"][0]->chunks = $chapters[0]["chunks"];
                }

                switch ($data["event"][0]->step) {
                    case EventSteps::THEO_CHECK:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            Url::redirect('events');
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (!isset($error)) {
                                    $keywords = $this->_translationModel->getKeywords([
                                        "eventID" => $data["event"][0]->eventID,
                                        "chapter" => $data["event"][0]->currentChapter
                                    ]);

                                    if (!empty($keywords)) {
                                        $kwCheck = (array)json_decode($data["event"][0]->kwCheck, true);
                                        if (array_key_exists($data["event"][0]->currentChapter, $kwCheck)) {
                                            $kwCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                        }

                                        $crCheck = (array)json_decode($data["event"][0]->crCheck, true);
                                        $crCheck[$data["event"][0]->currentChapter] = [
                                            "memberID" => 0,
                                            "done" => 0
                                        ];

                                        $postdata = [
                                            "kwCheck" => json_encode($kwCheck),
                                            "crCheck" => json_encode($crCheck)
                                        ];

                                        $this->_model->updateTranslator($postdata, [
                                            "trID" => $data["event"][0]->trID
                                        ]);
                                        Url::redirect('events/');
                                    } else {
                                        $error[] = __("keywords_empty_error");
                                    }
                                }
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/SUN/Checker')
                            ->nest('page', 'Events/SUN/TheoCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::CONTENT_REVIEW:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            Url::redirect('events');
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $crCheck = (array)json_decode($data["event"][0]->crCheck, true);
                                if (array_key_exists($data["event"][0]->currentChapter, $crCheck)) {
                                    $crCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                }

                                $postdata = [
                                    "crCheck" => json_encode($crCheck),
                                ];

                                $this->_model->updateTranslator($postdata, [
                                    "trID" => $data["event"][0]->trID
                                ]);
                                Url::redirect('events/checker-sun/' . $data["event"][0]->eventID .
                                    "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                            }
                        }

                        $data["next_step"] = EventSteps::FINAL_REVIEW;

                        return View::make('Events/SUN/Checker')
                            ->nest('page', 'Events/SUN/ContentReview')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::FINAL_REVIEW:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            Url::redirect('events');
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $_POST = Gump::xss_clean($_POST);

                            if (isset($_POST["confirm_step"])) {
                                $chunks = isset($_POST["chunks"]) ? (array)$_POST["chunks"] : array();
                                $chunks = array_map("trim", $chunks);
                                $chunks = array_filter($chunks, function ($v) {
                                    return !empty($v);
                                });

                                if (sizeof($chunks) < sizeof($data["chunks"]))
                                    $error[] = __("empty_verses_error");

                                if (!isset($error)) {
                                    $versesCombined = [];
                                    foreach ($chunks as $key => $chunk) {
                                        $verses = preg_split("/\|([0-9]+)\|/", $chunk, -1, PREG_SPLIT_NO_EMPTY);

                                        if (sizeof($data["chunks"][$key]) != sizeof($verses)) {
                                            $error[] = __("not_equal_verse_markers");
                                            break;
                                        }

                                        $versesCombined[$key] = array_combine($data["chunks"][$key], $verses);
                                    }

                                    if (!isset($error)) {
                                        foreach ($versesCombined as $key => $chunk) {
                                            $translation[$key][EventMembers::TRANSLATOR]["verses"] = $chunk;

                                            $tID = $translation[$key]["tID"];
                                            unset($translation[$key]["tID"]);

                                            $encoded = json_encode($translation[$key]);
                                            $json_error = json_last_error();

                                            if ($json_error == JSON_ERROR_NONE) {
                                                $trData = array(
                                                    "translatedVerses" => $encoded,
                                                    "translateDone" => true
                                                );
                                                $this->_translationModel->updateTranslation(
                                                    $trData,
                                                    array(
                                                        "trID" => $data["event"][0]->trID,
                                                        "tID" => $tID));
                                            } else {
                                                $error[] = __("error_ocured", array($tID));
                                            }
                                        }

                                        if (!isset($error)) {
                                            $chapters = [];
                                            for ($i = 1; $i <= $data["event"][0]->chaptersNum; $i++) {
                                                $data["chapters"][$i] = [];
                                            }

                                            $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                            foreach ($chaptersDB as $chapter) {
                                                $tmp["trID"] = $chapter["trID"];
                                                $tmp["memberID"] = $chapter["memberID"];
                                                $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                                $tmp["checked"] = $chapter["checked"];

                                                $chapters[$chapter["chapter"]] = $tmp;
                                            }

                                            $chapters[$data["event"][0]->currentChapter]["checked"] = true;

                                            // Check if whole book is finished
                                            if ($this->checkBookFinished($chapters, $data["event"][0]->chaptersNum, true))
                                                $this->_model->updateEvent([
                                                    "state" => EventStates::TRANSLATED,
                                                    "dateTo" => date("Y-m-d H:i:s", time())],
                                                    ["eventID" => $data["event"][0]->eventID]);

                                            $this->_model->updateChapter([
                                                "done" => true,
                                                "checked" => true
                                            ], [
                                                "eventID" => $data["event"][0]->eventID,
                                                "chapter" => $data["event"][0]->currentChapter
                                            ]);

                                            $crCheck = (array)json_decode($data["event"][0]->crCheck, true);
                                            if (array_key_exists($data["event"][0]->currentChapter, $crCheck)) {
                                                $crCheck[$data["event"][0]->currentChapter]["done"] = 2;
                                            }

                                            $postdata = [
                                                "crCheck" => json_encode($crCheck),
                                            ];

                                            $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                            Url::redirect('events/');
                                        }
                                    }
                                }
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/SUN/Checker')
                            ->nest('page', 'Events/SUN/FinalReview')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/SUN/Checker')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/SUN/Checker')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    /**
     * View for Theo check and V-b-v check in ODB SUN event
     * @param $eventID
     * @param $memberID
     * @param $chapter
     * @return View
     */
    public function checkerOdbSun($eventID, $memberID, $chapter)
    {
        $isXhr = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isXhr = true;
            $response["success"] = false;
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEventsForSun(
            Session::get("memberID"), $eventID, $memberID, $chapter);

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["sun"])) {
                Url::redirect("events/");
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::TRANSLATING || $data["event"][0]->state == EventStates::TRANSLATED) {
                $data["turn"] = $this->makeTurnCredentials();

                $chapters = $this->_model->getChapters($eventID, null, $chapter);
                $data["event"][0]->chunks = [];
                if (!empty($chapters)) {
                    $data["event"][0]->chunks = $chapters[0]["chunks"];
                }

                switch ($data["event"][0]->step) {
                    case EventSteps::THEO_CHECK:
                        $sourceText = $this->getOtherSourceText($data);

                        if ($sourceText !== false) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            Url::redirect('events');
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $kwCheck = (array)json_decode($data["event"][0]->kwCheck, true);
                                if (array_key_exists($data["event"][0]->currentChapter, $kwCheck)) {
                                    $kwCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                }

                                $crCheck = (array)json_decode($data["event"][0]->crCheck, true);
                                $crCheck[$data["event"][0]->currentChapter] = [
                                    "memberID" => 0,
                                    "done" => 0
                                ];

                                $postdata = [
                                    "kwCheck" => json_encode($kwCheck),
                                    "crCheck" => json_encode($crCheck)
                                ];

                                $this->_model->updateTranslator($postdata, [
                                    "trID" => $data["event"][0]->trID
                                ]);
                                Url::redirect('events/');
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/ODBSUN/Checker')
                            ->nest('page', 'Events/ODBSUN/TheoCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::CONTENT_REVIEW:
                        $sourceText = $this->getOtherSourceText($data);

                        if ($sourceText !== false) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            Url::redirect('events');
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                foreach ($translation as $key => $chunk) {
                                    $translation[$key][EventMembers::TRANSLATOR]["verses"] = [
                                        ($key + 1) => $chunk[EventMembers::TRANSLATOR]["symbols"]
                                    ];

                                    $tID = $translation[$key]["tID"];
                                    unset($translation[$key]["tID"]);

                                    $encoded = json_encode($translation[$key]);
                                    $json_error = json_last_error();

                                    if ($json_error == JSON_ERROR_NONE) {
                                        $trData = array(
                                            "translatedVerses" => $encoded,
                                            "translateDone" => true
                                        );
                                        $this->_translationModel->updateTranslation(
                                            $trData,
                                            array(
                                                "trID" => $data["event"][0]->trID,
                                                "tID" => $tID));
                                    } else {
                                        $error[] = __("error_ocured", array($tID));
                                    }
                                }

                                if (!isset($error)) {
                                    $chapters = [];
                                    for ($i = 1; $i <= $data["event"][0]->chaptersNum; $i++) {
                                        $data["chapters"][$i] = [];
                                    }

                                    $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                    foreach ($chaptersDB as $chapter) {
                                        $tmp["trID"] = $chapter["trID"];
                                        $tmp["memberID"] = $chapter["memberID"];
                                        $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                        $tmp["checked"] = $chapter["checked"];

                                        $chapters[$chapter["chapter"]] = $tmp;
                                    }

                                    $chapters[$data["event"][0]->currentChapter]["done"] = true;

                                    // Check if whole book is finished
                                    if ($this->checkBookFinished($chapters, $data["event"][0]->chaptersNum, true))
                                        $this->_model->updateEvent([
                                            "state" => EventStates::TRANSLATED,
                                            "dateTo" => date("Y-m-d H:i:s", time())],
                                            ["eventID" => $data["event"][0]->eventID]);

                                    $this->_model->updateChapter(["checked" => true], ["eventID" => $data["event"][0]->eventID, "chapter" => $data["event"][0]->currentChapter]);

                                    $crCheck = (array)json_decode($data["event"][0]->crCheck, true);
                                    if (array_key_exists($data["event"][0]->currentChapter, $crCheck)) {
                                        $crCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                    }

                                    $postdata = [
                                        "crCheck" => json_encode($crCheck),
                                    ];

                                    $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);
                                    Url::redirect('events/');
                                }
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/ODBSUN/Checker')
                            ->nest('page', 'Events/ODBSUN/ContentReview')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/ODBSUN/Checker')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/ODBSUN/Checker')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function checkerRadio($eventID, $memberID, $chapter)
    {
        $isXhr = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isXhr = true;
            $response["success"] = false;
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["isCheckerPage"] = true;
        $data["event"] = $this->_model->getMemberEventsForRadio(
            Session::get("memberID"), $eventID, $memberID, $chapter);

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["rad"])) {
                Url::redirect("events/");
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::TRANSLATING || $data["event"][0]->state == EventStates::TRANSLATED) {
                $data["turn"] = $this->makeTurnCredentials();

                $chapters = $this->_model->getChapters($eventID, null, $chapter);
                $data["event"][0]->chunks = [];
                if (!empty($chapters)) {
                    $data["event"][0]->chunks = $chapters[0]["chunks"];
                }
                $data["isCheckerPage"] = true;

                switch ($data["event"][0]->step) {
                    case EventSteps::PEER_REVIEW:

                        $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);

                        // Get radio source
                        $sourceText = $this->getOtherSourceText($data);
                        if ($sourceText !== false) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $data["event"][0]->trID,
                                    $data["event"][0]->currentChapter);
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $peerCheck[$data["event"][0]->currentChapter]["done"] = 1;
                            $this->_model->updateTranslator(
                                [
                                    "peerCheck" => json_encode($peerCheck)
                                ],
                                [
                                    "trID" => $data["event"][0]->trID
                                ]
                            );
                            Url::redirect('events/checker-rad/' . $data["event"][0]->eventID .
                                "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $confirm_step = isset($_POST["confirm_step"]) ? $_POST["confirm_step"] : false;
                            if ($confirm_step) {
                                // Update Checker if it's empty
                                foreach ($translation as $tr) {
                                    if (empty($tr[EventMembers::CHECKER]["verses"])) {
                                        $tr[EventMembers::CHECKER]["verses"] = $tr[EventMembers::TRANSLATOR]["verses"];
                                        $tID = $tr["tID"];
                                        unset($tr["tID"]);
                                        $this->_translationModel->updateTranslation(
                                            ["translatedVerses" => json_encode($tr)],
                                            ["tID" => $tID]
                                        );
                                    }
                                }

                                $peerCheck[$data["event"][0]->currentChapter]["done"] = 1;

                                $postdata = [
                                    "peerCheck" => json_encode($peerCheck)
                                ];

                                $this->_model->updateTranslator($postdata, ["trID" => $data["event"][0]->trID]);

                                $chapters = [];
                                for ($i = 0; $i <= $data["event"][0]->chaptersNum; $i++) {
                                    $data["chapters"][$i] = [];
                                }

                                // Set chapter checked
                                $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                foreach ($chaptersDB as $chapter) {
                                    $tmp["trID"] = $chapter["trID"];
                                    $tmp["memberID"] = $chapter["memberID"];
                                    $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                    $tmp["done"] = $chapter["done"];
                                    $tmp["checked"] = $chapter["checked"];

                                    $chapters[$chapter["chapter"]] = $tmp;
                                }

                                $chapters[$data["event"][0]->currentChapter]["checked"] = true;
                                $this->_model->updateChapter(["checked" => true], [
                                    "eventID" => $data["event"][0]->eventID,
                                    "chapter" => $data["event"][0]->currentChapter]);

                                // Check if whole scripture is finished
                                if ($this->checkBookFinished($chapters, $data["event"][0]->chaptersNum + 1, true))
                                    $this->_model->updateEvent([
                                        "state" => EventStates::TRANSLATED,
                                        "dateTo" => date("Y-m-d H:i:s", time())],
                                        ["eventID" => $data["event"][0]->eventID]);

                                Url::redirect('events');
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/Radio/Translator')
                            ->nest('page', "Events/Radio/PeerReview")
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $error[] = __("checker_event_error");
                $title = "Error";
            }
        } else {
            $error[] = __("checker_event_error");
            $title = "Error";
        }

        return View::make('Events/Radio/Translator')
            ->shares("title", $title)
            ->shares("data", $data)
            ->shares("error", @$error);
    }


    /**
     * View for Keyword-Check and Peer-Review in Questions event
     * @param $eventID
     * @param $memberID
     * @param $chapter
     * @return View
     */
    public function checkerObs($eventID, $memberID, $chapter)
    {
        $isXhr = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isXhr = true;
            $response["success"] = false;
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["isCheckerPage"] = true;
        $data["isPeerPage"] = false;
        $data["event"] = $this->_model->getMemberEventsForOther(
            Session::get("memberID"),
            $eventID,
            $memberID,
            $chapter
        );

        if (!empty($data["event"])) {
            $event = $data["event"][0];

            if ($event->bookProject != "obs") {
                Url::redirect("events/");
            }

            $title = $data["event"][0]->name
                . " " . ($event->currentChapter > 0 ? $event->currentChapter : "")
                . " - " . $event->tLang
                . " - " . __($event->bookProject);

            if ($event->state == EventStates::TRANSLATING || $event->state == EventStates::TRANSLATED) {
                $data["turn"] = $this->makeTurnCredentials();

                $chapters = $this->_model->getChapters($eventID, null, $chapter);
                $event->chunks = [];
                if (!empty($chapters)) {
                    $event->chunks = $chapters[0]["chunks"];
                }
                $data["isCheckerPage"] = true;
                $otherCheck = (array)json_decode($data["event"][0]->otherCheck, true);

                switch ($event->step) {
                    case EventSteps::PRAY:
                        // Get obs
                        $sourceTextObs = $this->resourcesRepo->getObs($event->resLangID, $event->currentChapter);

                        if (!$sourceTextObs) {
                            $otherCheck[$event->currentChapter]["done"] = 3;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $event->trID]);
                            Url::redirect('events/checker-obs/' . $event->eventID .
                                "/" . $event->memberID . "/" . $event->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (array_key_exists($event->currentChapter, $otherCheck)) {
                                    $otherCheck[$event->currentChapter]["done"] = 1;
                                }

                                $this->_model->updateTranslator([
                                    "otherCheck" => json_encode($otherCheck)
                                ], ["trID" => $event->trID]);

                                Url::redirect('events/checker-obs/' . $event->eventID .
                                    "/" . $event->memberID . "/" . $event->currentChapter);
                            }
                        }

                        $data["event"][0]->justStarted = true;
                        $data["next_step"] = EventSteps::KEYWORD_CHECK;

                        return View::make('Events/Obs/Translator')
                            ->nest('page', 'Events/Obs/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                    case EventSteps::KEYWORD_CHECK:

                        // Get obs
                        $sourceTextObs = $this->resourcesRepo->getObs($event->resLangID, $event->currentChapter);
                        $data["chunks"] = $this->_apiModel->getObsChunks($sourceTextObs);

                        if ($sourceTextObs) {
                            $data["obs"] = $sourceTextObs->chunks;

                            $data["comments"] = $this->getComments(
                                $event->eventID,
                                $event->currentChapter);

                            $translationData = $this->_translationModel->getEventTranslation(
                                $event->trID,
                                $event->currentChapter);
                            $translation = array();

                            foreach ($translationData as $tv) {
                                $arr = json_decode($tv->translatedVerses, true);
                                $arr["tID"] = $tv->tID;
                                $translation[] = $arr;
                            }
                            $data["translation"] = $translation;
                        } else {
                            $otherCheck[$data["event"][0]->currentChapter]["done"] = 3;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $event->trID]);
                            Url::redirect('events/checker-obs/' . $event->eventID .
                                "/" . $event->memberID . "/" . $event->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            $confirm_step = $_POST["confirm_step"] ?? false;
                            if ($confirm_step) {
                                $chunks = isset($_POST["chunks"]) ? (array)$_POST["chunks"] : [];
                                $chunks = $this->_apiModel->testChunkObs($chunks, $data["obs"]);
                                if (!$chunks === false) {
                                    // Update Checker if it's empty
                                    foreach ($translation as $tr) {
                                        if (empty($tr[EventMembers::CHECKER]["verses"])) {
                                            $tr[EventMembers::CHECKER]["verses"] = $tr[EventMembers::TRANSLATOR]["verses"];
                                            $tID = $tr["tID"];
                                            unset($tr["tID"]);
                                            $this->_translationModel->updateTranslation(
                                                ["translatedVerses" => json_encode($tr)],
                                                ["tID" => $tID]
                                            );
                                        }
                                    }
                                } else {
                                    $error[] = __("wrong_chunks_error");
                                }

                                if (!isset($error)) {
                                    // 2 for PEER_REVIEW step
                                    $otherCheck[$event->currentChapter]["done"] = 2;

                                    $peerCheck = (array)json_decode($event->peerCheck, true);
                                    if (!array_key_exists($event->currentChapter, $peerCheck)) {
                                        $peerCheck[$event->currentChapter] = [
                                            "memberID" => 0,
                                            "done" => 0
                                        ];
                                    }

                                    $postdata = [
                                        "otherCheck" => json_encode($otherCheck),
                                        "peerCheck" => json_encode($peerCheck)
                                    ];

                                    $this->_model->updateTranslator($postdata, ["trID" => $event->trID]);
                                    Url::redirect('events/checker-obs/' . $event->eventID .
                                        "/" . $event->memberID . "/" . $event->currentChapter);
                                }
                            }
                        }

                        $data["next_step"] = EventSteps::PEER_REVIEW . "_tq";

                        return View::make('Events/Obs/Translator')
                            ->nest('page', 'Events/Obs/KeywordCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventSteps::PEER_REVIEW:

                        // Get obs
                        $sourceTextObs = $this->resourcesRepo->getObs($event->resLangID, $event->currentChapter);
                        $data["chunks"] = $this->_apiModel->getObsChunks($sourceTextObs);

                        if ($sourceTextObs) {
                            $data["obs"] = $sourceTextObs->chunks;

                            $data["comments"] = $this->getComments(
                                $event->eventID,
                                $event->currentChapter);

                            $translationData = $this->_translationModel->getEventTranslation(
                                $event->trID,
                                $event->currentChapter);
                            $translation = array();

                            foreach ($translationData as $tv) {
                                $arr = json_decode($tv->translatedVerses, true);
                                $arr["tID"] = $tv->tID;
                                $translation[] = $arr;
                            }
                            $data["translation"] = $translation;
                        } else {
                            $otherCheck[$data["event"][0]->currentChapter]["done"] = 3;
                            $this->_model->updateTranslator(["otherCheck" => json_encode($otherCheck)], ["trID" => $event->trID]);
                            Url::redirect('events/checker-obs/' . $event->eventID .
                                "/" . $event->memberID . "/" . $event->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $peerCheck = (array)json_decode($event->peerCheck, true);

                                if ($event->peer == 1) {
                                    if (isset($peerCheck[$event->currentChapter]) &&
                                        $peerCheck[$event->currentChapter]["done"]) {
                                        // 3 for chapter finished
                                        $otherCheck[$event->currentChapter]["done"] = 3;

                                        $this->_model->updateTranslator(
                                            ["otherCheck" => json_encode($otherCheck)],
                                            ["trID" => $event->trID]);

                                        $chapters = [];
                                        for ($i = 1; $i <= $event->chaptersNum; $i++) {
                                            $data["chapters"][$i] = [];
                                        }

                                        $chaptersDB = $this->_model->getChapters($event->eventID);

                                        foreach ($chaptersDB as $chapter) {
                                            $tmp["trID"] = $chapter["trID"];
                                            $tmp["memberID"] = $chapter["memberID"];
                                            $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                            $tmp["done"] = $chapter["done"];
                                            $tmp["checked"] = $chapter["checked"];

                                            $chapters[$chapter["chapter"]] = $tmp;
                                        }

                                        $chapters[$event->currentChapter]["checked"] = true;
                                        $this->_model->updateChapter(["checked" => true], [
                                            "eventID" => $event->eventID,
                                            "chapter" => $event->currentChapter]);

                                        // Check if whole scripture is finished
                                        if ($this->checkBookFinished($chapters, $event->chaptersNum, true))
                                            $this->_model->updateEvent([
                                                "state" => EventStates::TRANSLATED,
                                                "dateTo" => date("Y-m-d H:i:s", time())],
                                                ["eventID" => $event->eventID]);

                                        Url::redirect('events');
                                    } else {
                                        $error[] = __("checker_not_ready_error");
                                    }
                                } else {
                                    $peerCheck[$event->currentChapter]["done"] = 1;
                                    $this->_model->updateTranslator(
                                        ["peerCheck" => json_encode($peerCheck)],
                                        ["trID" => $event->trID]);

                                    $response["success"] = true;
                                    echo json_encode($response);
                                    exit;
                                }
                            }
                        }

                        if ($data["event"][0]->peer == 1)
                            $page = "Events/Obs/PeerReview";
                        else {
                            $page = "Events/Obs/CheckerPeerReview";
                            $data["isPeerPage"] = true;
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/Obs/Translator')
                            ->nest('page', $page)
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $error[] = __("checker_event_error");
                $title = "Error";
            }
        } else {
            $error[] = __("checker_event_error");
            $title = "Error";
        }

        return View::make('Events/Obs/Translator')
            ->shares("title", $title)
            ->shares("data", $data)
            ->shares("error", @$error);
    }

    /**
     * View for Level 2 check page
     * @url /events/check-l2
     * @param $eventID
     * @return View
     */
    public function checkerL2($eventID)
    {
        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["isCheckerPage"] = true;
        $data["event"] = $this->_model->getMemberEventsForCheckerL2(
            Session::get("memberID"),
            $eventID
        );

        $title = "";

        if (!empty($data["event"])) {
            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::L2_CHECK || $data["event"][0]->state == EventStates::L2_CHECKED) {
                if ($data["event"][0]->step == EventCheckSteps::NONE)
                    Url::redirect("events/information-l2/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                switch ($data["event"][0]->step) {
                    case EventCheckSteps::PRAY:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateL2Checker([
                                "step" => EventCheckSteps::NONE
                            ], [
                                "l2chID" => $data["event"][0]->l2chID
                            ]);
                            Url::redirect('events/checker-l2/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {

                                $postdata = [
                                    "step" => EventCheckSteps::CONSUME,
                                    "currentChapter" => $sourceText["currentChapter"]
                                ];
                                $this->_model->updateL2Checker($postdata, [
                                    "l2chID" => $data["event"][0]->l2chID
                                ]);
                                Url::redirect('events/checker-l2/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = EventCheckSteps::CONSUME;

                        return View::make('Events/L2/Checker')
                            ->nest('page', 'Events/L2/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                    case EventCheckSteps::CONSUME:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateL2Checker([
                                "step" => EventCheckSteps::NONE
                            ], [
                                "l2chID" => $data["event"][0]->l2chID
                            ]);
                            Url::redirect('events/checker-l2/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {

                                $this->_model->updateL2Checker([
                                    "step" => EventCheckSteps::FST_CHECK
                                ], [
                                    "l2chID" => $data["event"][0]->l2chID
                                ]);
                                Url::redirect('events/checker-l2/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = EventCheckSteps::FST_CHECK;

                        return View::make('Events/L2/Checker')
                            ->nest('page', 'Events/L2/Consume')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventCheckSteps::FST_CHECK:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateL2Checker([
                                "step" => EventCheckSteps::NONE
                            ], [
                                "l2chID" => $data["event"][0]->l2chID
                            ]);
                            Url::redirect('events/checker-l2/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                // Update L2 if it's empty
                                foreach ($translation as $tr) {
                                    if (empty($tr[EventMembers::L2_CHECKER]["verses"])) {
                                        $tr[EventMembers::L2_CHECKER]["verses"] = $tr[EventMembers::TRANSLATOR]["verses"];
                                        $tID = $tr["tID"];
                                        unset($tr["tID"]);
                                        $this->_translationModel->updateTranslation(
                                            ["translatedVerses" => json_encode($tr)],
                                            ["tID" => $tID]
                                        );
                                    }
                                }

                                $chapters = [];
                                for ($i = 1; $i <= $data["event"][0]->chaptersNum; $i++) {
                                    $data["chapters"][$i] = [];
                                }

                                $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                foreach ($chaptersDB as $chapter) {
                                    $tmp["trID"] = $chapter["trID"];
                                    $tmp["memberID"] = $chapter["memberID"];
                                    $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                    $tmp["done"] = $chapter["done"];
                                    $tmp["l2memberID"] = $chapter["l2memberID"];
                                    $tmp["l2chID"] = $chapter["l2chID"];
                                    $tmp["l2checked"] = $chapter["l2checked"];

                                    $chapters[$chapter["chapter"]] = $tmp;
                                }

                                $this->_model->updateChapter([
                                    "l2checked" => true
                                ], [
                                    "eventID" => $data["event"][0]->eventID,
                                    "chapter" => $data["event"][0]->currentChapter
                                ]);

                                $chapters[$data["event"][0]->currentChapter]["l2checked"] = true;

                                // Check if the member has another chapter to check
                                // then redirect to preparation page
                                $nextChapter = 0;
                                $nextChapterDB = $this->_model->getNextChapter(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->memberID,
                                    "l2");
                                if (!empty($nextChapterDB))
                                    $nextChapter = $nextChapterDB[0]->chapter;

                                $sndCheck = (array)json_decode($data["event"][0]->sndCheck, true);
                                if (!array_key_exists($data["event"][0]->currentChapter, $sndCheck)) {
                                    $sndCheck[$data["event"][0]->currentChapter] = [
                                        "memberID" => 0,
                                        "done" => 0
                                    ];
                                }

                                $postdata = [
                                    "step" => EventSteps::NONE,
                                    "currentChapter" => 0,
                                    "sndCheck" => json_encode($sndCheck)
                                ];

                                if ($nextChapter > 0) {
                                    $postdata["step"] = EventSteps::PRAY;
                                    $postdata["currentChapter"] = $nextChapter;
                                }

                                $this->_model->updateL2Checker($postdata, [
                                    "l2chID" => $data["event"][0]->l2chID
                                ]);

                                if ($nextChapter > 0)
                                    Url::redirect('events/checker-l2/' . $data["event"][0]->eventID);
                                else
                                    Url::redirect('events/');
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/L2/Checker')
                            ->nest('page', 'Events/L2/FstCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/L2/Checker')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/L2/Checker')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    /**
     * View for 2nd check and Peer check in Level2 event
     * @param $eventID
     * @param $memberID
     * @return View
     */
    public function checkerL2Continue($eventID, $memberID, $chapter)
    {
        $isXhr = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isXhr = true;
            $response["success"] = false;
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["isCheckerPage"] = true;
        $data["event"] = $this->_model->getMemberEventsForCheckerL2(
            Session::get("memberID"), $eventID, $memberID, $chapter);

        if (!empty($data["event"])) {
            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::L2_CHECK || $data["event"][0]->state == EventStates::L2_CHECKED) {
                $data["turn"] = $this->makeTurnCredentials();

                $chapters = $this->_model->getChapters($eventID, null, $chapter);
                $data["event"][0]->chunks = [];
                if (!empty($chapters)) {
                    $data["event"][0]->chunks = $chapters[0]["chunks"];
                }

                switch ($data["event"][0]->step) {
                    case EventCheckSteps::SND_CHECK:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            Url::redirect('events');
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $sndCheck = (array)json_decode($data["event"][0]->sndCheck, true);
                                if (array_key_exists($data["event"][0]->currentChapter, $sndCheck)) {
                                    $sndCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                }

                                $postdata = [
                                    "sndCheck" => json_encode($sndCheck)
                                ];

                                $this->_model->updateL2Checker($postdata, [
                                    "l2chID" => $data["event"][0]->l2chID
                                ]);
                                Url::redirect('events/checker-l2/' . $data["event"][0]->eventID .
                                    "/" . $data["event"][0]->memberID . "/" . $data["event"][0]->currentChapter);
                            }
                        }

                        $data["next_step"] = EventCheckSteps::KEYWORD_CHECK_L2;

                        return View::make('Events/L2/Checker')
                            ->nest('page', 'Events/L2/SndCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventCheckSteps::KEYWORD_CHECK_L2:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            Url::redirect('events');
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $sndCheck = (array)json_decode($data["event"][0]->sndCheck, true);
                                if (array_key_exists($data["event"][0]->currentChapter, $sndCheck)) {
                                    $sndCheck[$data["event"][0]->currentChapter]["done"] = 2;
                                }
                                $peer1Check = (array)json_decode($data["event"][0]->peer1Check, true);
                                $peer2Check = (array)json_decode($data["event"][0]->peer2Check, true);
                                if (!array_key_exists($data["event"][0]->currentChapter, $peer1Check)) {
                                    $peer1Check[$data["event"][0]->currentChapter] = [
                                        "memberID" => 0,
                                        "done" => 0
                                    ];
                                    $peer2Check[$data["event"][0]->currentChapter] = [
                                        "memberID" => 0,
                                        "done" => 0
                                    ];
                                }

                                $postdata = [
                                    "sndCheck" => json_encode($sndCheck),
                                    "peer1Check" => json_encode($peer1Check),
                                    "peer2Check" => json_encode($peer2Check),
                                ];

                                $this->_model->updateL2Checker($postdata, [
                                    "l2chID" => $data["event"][0]->l2chID
                                ]);
                                Url::redirect('events/');
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/L2/Checker')
                            ->nest('page', 'Events/L2/KeywordCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventCheckSteps::PEER_REVIEW_L2:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;

                                if ($data["event"][0]->peer == 2) {
                                    unset($data["isCheckerPage"]);
                                }
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            Url::redirect('events');
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $skip_kw = isset($_POST["skip_kw"]) && $_POST["skip_kw"] == 1 ? true : false;
                                $peer1Check = (array)json_decode($data["event"][0]->peer1Check, true);
                                $peer2Check = (array)json_decode($data["event"][0]->peer2Check, true);

                                if (!isset($error)) {
                                    if ($data["event"][0]->peer == 1) {
                                        if (array_key_exists($data["event"][0]->currentChapter, $peer2Check)
                                            && $peer2Check[$data["event"][0]->currentChapter]["done"] == 1) {
                                            $peer1Check[$data["event"][0]->currentChapter]["done"] = 1;
                                            $postdata = [
                                                "peer1Check" => json_encode($peer1Check)
                                            ];

                                            // Check if the whole book was checked and set its state to L2_CHECKED
                                            $chapters = [];
                                            $events = $this->_model->getMembersForL2Event($data["event"][0]->eventID);

                                            foreach ($events as $event) {
                                                $peer2 = (array)json_decode($event["peer2Check"], true);
                                                if (!empty($peer2)) {
                                                    $chapters += $peer2;
                                                }
                                            }

                                            if (sizeof($chapters) == $data["event"][0]->chaptersNum) {
                                                $allDone = true;
                                                foreach ($chapters as $chapter) {
                                                    if ($chapter["done"] == 0) {
                                                        $allDone = false;
                                                        break;
                                                    }
                                                }

                                                if ($allDone) {
                                                    $this->_model->updateEvent([
                                                        "state" => EventStates::L2_CHECKED
                                                    ], [
                                                        "eventID" => $data["event"][0]->eventID
                                                    ]);
                                                }
                                            }
                                        } else {
                                            $error[] = __("checker_not_ready_error");
                                        }
                                    } else {
                                        $keywords = $this->_translationModel->getKeywords([
                                            "eventID" => $data["event"][0]->eventID,
                                            "chapter" => $data["event"][0]->currentChapter
                                        ]);

                                        if (empty($keywords) || $skip_kw) {
                                            $peer2Check[$data["event"][0]->currentChapter]["done"] = 1;
                                            $postdata = [
                                                "peer2Check" => json_encode($peer2Check)
                                            ];
                                        } else {
                                            $response["kw_exist"] = true;
                                            $error[] = __("keywords_still_exist_error");
                                        }
                                    }

                                    if (!isset($error)) {

                                        $this->_model->updateL2Checker($postdata, [
                                            "l2chID" => $data["event"][0]->l2chID
                                        ]);

                                        if (!$isXhr) {
                                            Url::redirect('events/');
                                        } else {
                                            $response["success"] = true;
                                            echo json_encode($response);
                                            exit;
                                        }
                                    } else {
                                        if ($isXhr) {
                                            $response["errors"] = $error;
                                            echo json_encode($response);
                                            exit;
                                        }
                                    }
                                }
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/L2/Checker')
                            ->nest('page', 'Events/L2/PeerCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/L2/Checker')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/L2/Checker')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function checkerNotesL3($eventID)
    {
        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["isChecker"] = false;
        $data["isCheckerPage"] = true;
        $data["event"] = $this->_model->getMemberEventsForCheckerL3(Session::get("memberID"), $eventID);

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["tn"])) {
                if (in_array($data["event"][0]->bookProject, ["udb", "ulb"]))
                    Url::redirect("events/checker-l3/" . $eventID);
                else
                    Url::redirect("events/checker-" . $data["event"][0]->bookProject . "-l3/" . $eventID);
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > -1 ? ($data["event"][0]->currentChapter == 0
                    ? __("front") : $data["event"][0]->currentChapter) : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if (($data["event"][0]->state == EventStates::L3_CHECK
                || $data["event"][0]->state == EventStates::COMPLETE)) {
                if ($data["event"][0]->step == EventSteps::NONE)
                    Url::redirect("events/information-tn-l3/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                switch ($data["event"][0]->step) {
                    case EventCheckSteps::PRAY:

                        $data["currentChapter"] = $data["event"][0]->currentChapter;
                        if ($data["event"][0]->currentChapter < 0) {
                            $nextChapter = $this->_model->getNextChapter(
                                $data["event"][0]->eventID,
                                $data["event"][0]->memberID,
                                "l3");

                            if (!empty($nextChapter)) {
                                $data["currentChapter"] = $nextChapter[0]->chapter;
                            } else {
                                $postdata = [
                                    "step" => EventCheckSteps::NONE,
                                    "currentChapter" => -1
                                ];
                                $this->_model->updateL3Checker($postdata, ["l3chID" => $data["event"][0]->l3chID]);

                                Url::redirect('events/checker-tn-l3/' . $data["event"][0]->eventID);
                            }
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);
                                $peerCheck[$data["currentChapter"]] = [
                                    "memberID" => 0,
                                    "done" => 0
                                ];

                                $postdata = [
                                    "step" => EventCheckSteps::PEER_REVIEW_L3,
                                    "currentChapter" => $data["currentChapter"],
                                    "peerCheck" => json_encode($peerCheck)
                                ];
                                $this->_model->updateL3Checker($postdata, ["l3chID" => $data["event"][0]->l3chID]);

                                Url::redirect('events/checker-tn-l3/' . $data["event"][0]->eventID);
                            }
                        }

                        // Check if translator just started translating of this book
                        $data["event"][0]->justStarted = $data["event"][0]->peerCheck == "";
                        $data["next_step"] = EventCheckSteps::PEER_REVIEW_L3;

                        return View::make('Events/L3Notes/Checker')
                            ->nest('page', 'Events/L3Notes/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                    case EventCheckSteps::PEER_REVIEW_L3:
                        $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);
                        $data["currentChapter"] = $data["event"][0]->currentChapter;

                        // Get related ulb project
                        $ulbProject = $this->_model->getProject(["projects.projectID"], [
                            ["projects.glID", $data["event"][0]->glID],
                            ["projects.gwLang", $data["event"][0]->gwLang],
                            ["projects.targetLang", $data["event"][0]->targetLang],
                            ["projects.bookProject", "ulb"]
                        ]);

                        if (!empty($ulbProject))
                            $ulbEvent = $this->_model->getEvent(null, $ulbProject[0]->projectID, $data["event"][0]->bookCode);

                        if (empty($ulbEvent) || $ulbEvent[0]->state != EventStates::COMPLETE)
                            $error[] = __("l2_l3_event_notexist_error");

                        if (!isset($error)) {
                            // Get ulb translation
                            $ulbTranslationData = $this->_translationModel->getEventTranslationByEventID(
                                $ulbEvent[0]->eventID,
                                $data["event"][0]->currentChapter
                            );
                            $ulbTranslation = ["l2" => [], "l3" => []];

                            foreach ($ulbTranslationData as $tv) {
                                $arr = (array)json_decode($tv->translatedVerses, true);

                                $ulbTranslation["l2"] += $arr[EventMembers::L2_CHECKER]["verses"];
                                $ulbTranslation["l3"] += $arr[EventMembers::L3_CHECKER]["verses"];
                            }
                            $data["ulb_translation"] = $ulbTranslation;

                            // Remove footnotes to avoid comparison errors
                            $data["ulb_translation"]["l2"] = array_map(function ($elm) {
                                return preg_replace("/<span.*<\/span>/mU", "", $elm);
                            }, $data["ulb_translation"]["l2"]);

                            $data["ulb_translation"]["l3"] = array_map(function ($elm) {
                                return preg_replace("/<span.*<\/span>/mU", "", $elm);
                            }, $data["ulb_translation"]["l3"]);

                            // Get Notes L2 translation
                            $translationData = $this->_translationModel->getEventTranslationByEventID(
                                $data["event"][0]->eventID,
                                $data["event"][0]->currentChapter
                            );
                            $translation = array();

                            foreach ($translationData as $tv) {
                                $arr = (array)json_decode($tv->translatedVerses, true);
                                $arr["tID"] = $tv->tID;
                                $translation[] = $arr;
                            }
                            $data["translation"] = $translation;

                            $chapters = $this->_model->getChapters(
                                $data["event"][0]->eventID, null,
                                $data["event"][0]->currentChapter);

                            $data["chunks"] = (array)json_decode($chapters[0]["chunks"], true);
                            $lastChunk = $data["chunks"][sizeof($data["chunks"]) - 1];
                            $data["totalVerses"] = $lastChunk[sizeof($lastChunk) - 1];

                            $data["comments"] = $this->getComments(
                                $data["event"][0]->eventID,
                                $data["event"][0]->currentChapter);

                            $data["event"][0]->checkerFName = null;
                            $data["event"][0]->checkerLName = null;

                            if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)
                                && $peerCheck[$data["event"][0]->currentChapter]["memberID"] > 0) {
                                $member = $this->memberRepo->get($peerCheck[$data["event"][0]->currentChapter]["memberID"]);

                                if ($member) {
                                    $data["event"][0]->chkMemberID = $member->memberID;
                                    $data["event"][0]->checkerFName = $member->firstName;
                                    $data["event"][0]->checkerLName = $member->lastName;
                                }
                            }
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)
                                    && $peerCheck[$data["event"][0]->currentChapter]["done"] == 1) {
                                    $postdata = [
                                        "step" => EventCheckSteps::PEER_EDIT_L3
                                    ];
                                    $this->_model->updateL3Checker($postdata, ["l3chID" => $data["event"][0]->l3chID]);
                                    Url::redirect('events/checker-tn-l3/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("checker_not_ready_error");
                                }
                            }
                        }

                        $data["next_step"] = EventCheckSteps::PEER_EDIT_L3;

                        return View::make('Events/L3Notes/Checker')
                            ->nest('page', 'Events/L3Notes/PeerReview')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                        break;

                    case EventCheckSteps::PEER_EDIT_L3:
                        $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);
                        $data["currentChapter"] = $data["event"][0]->currentChapter;

                        // Get related ulb project
                        $ulbProject = $this->_model->getProject(["projects.projectID"], [
                            ["projects.glID", $data["event"][0]->glID],
                            ["projects.gwLang", $data["event"][0]->gwLang],
                            ["projects.targetLang", $data["event"][0]->targetLang],
                            ["projects.bookProject", "ulb"]
                        ]);

                        if (!empty($ulbProject))
                            $ulbEvent = $this->_model->getEvent(null, $ulbProject[0]->projectID, $data["event"][0]->bookCode);

                        if (empty($ulbEvent) || $ulbEvent[0]->state != EventStates::COMPLETE)
                            $error[] = __("l2_l3_event_notexist_error");

                        if (!isset($error)) {
                            // Get ulb translation
                            $ulbTranslationData = $this->_translationModel->getEventTranslationByEventID(
                                $ulbEvent[0]->eventID,
                                $data["event"][0]->currentChapter
                            );
                            $ulbTranslation = ["l2" => [], "l3" => []];

                            foreach ($ulbTranslationData as $tv) {
                                $arr = (array)json_decode($tv->translatedVerses, true);

                                $ulbTranslation["l2"] += $arr[EventMembers::L2_CHECKER]["verses"];
                                $ulbTranslation["l3"] += $arr[EventMembers::L3_CHECKER]["verses"];
                            }
                            $data["ulb_translation"] = $ulbTranslation;

                            // Remove footnotes to avoid comparison errors
                            $data["ulb_translation"]["l2"] = array_map(function ($elm) {
                                return preg_replace("/<span.*<\/span>/mU", "", $elm);
                            }, $data["ulb_translation"]["l2"]);

                            $data["ulb_translation"]["l3"] = array_map(function ($elm) {
                                return preg_replace("/<span.*<\/span>/mU", "", $elm);
                            }, $data["ulb_translation"]["l3"]);

                            // Get Notes L2 translation
                            $translationData = $this->_translationModel->getEventTranslationByEventID(
                                $data["event"][0]->eventID,
                                $data["event"][0]->currentChapter
                            );
                            $translation = array();

                            foreach ($translationData as $tv) {
                                $arr = (array)json_decode($tv->translatedVerses, true);
                                $arr["tID"] = $tv->tID;
                                $translation[] = $arr;
                            }
                            $data["translation"] = $translation;

                            $chapters = $this->_model->getChapters(
                                $data["event"][0]->eventID, null,
                                $data["event"][0]->currentChapter);

                            $data["chunks"] = (array)json_decode($chapters[0]["chunks"], true);
                            $lastChunk = $data["chunks"][sizeof($data["chunks"]) - 1];
                            $data["totalVerses"] = $lastChunk[sizeof($lastChunk) - 1];

                            $data["comments"] = $this->getComments(
                                $data["event"][0]->eventID,
                                $data["event"][0]->currentChapter);

                            $data["event"][0]->checkerFName = null;
                            $data["event"][0]->checkerLName = null;

                            if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)
                                && $peerCheck[$data["event"][0]->currentChapter]["memberID"] > 0) {
                                $member = $this->memberRepo->get($peerCheck[$data["event"][0]->currentChapter]["memberID"]);
                                if ($member) {
                                    $data["event"][0]->chkMemberID = $member->memberID;
                                    $data["event"][0]->checkerFName = $member->firstName;
                                    $data["event"][0]->checkerLName = $member->lastName;
                                }
                            }
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)
                                    && $peerCheck[$data["event"][0]->currentChapter]["done"] == 2) {
                                    // Update L3 if it's empty
                                    foreach ($translation as $tr) {
                                        if (empty($tr[EventMembers::L3_CHECKER]["verses"])) {
                                            $tr[EventMembers::L3_CHECKER]["verses"] = $tr[EventMembers::CHECKER]["verses"];
                                            $tID = $tr["tID"];
                                            unset($tr["tID"]);
                                            $this->_translationModel->updateTranslation(
                                                ["translatedVerses" => json_encode($tr)],
                                                ["tID" => $tID]
                                            );
                                        }
                                    }

                                    $chapters = [];
                                    for ($i = 0; $i <= $data["event"][0]->chaptersNum; $i++) {
                                        $data["chapters"][$i] = [];
                                    }

                                    $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                    foreach ($chaptersDB as $chapter) {
                                        $tmp["trID"] = $chapter["trID"];
                                        $tmp["memberID"] = $chapter["memberID"];
                                        $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                        $tmp["l3checked"] = $chapter["l3checked"];

                                        $chapters[$chapter["chapter"]] = $tmp;
                                    }

                                    $chapters[$data["event"][0]->currentChapter]["l3checked"] = true;
                                    $this->_model->updateChapter(["l3checked" => true], [
                                        "eventID" => $data["event"][0]->eventID,
                                        "chapter" => $data["event"][0]->currentChapter]);

                                    // Check if whole scripture is finished
                                    if ($this->checkBookFinished($chapters, $data["event"][0]->chaptersNum + 1, false, 3))
                                        $this->_model->updateEvent([
                                            "state" => EventStates::COMPLETE,
                                            "dateTo" => date("Y-m-d H:i:s", time())],
                                            ["eventID" => $data["event"][0]->eventID]);

                                    // Check if the member has another chapter to check
                                    // then redirect to preparation page
                                    $nextChapter = -1;
                                    $nextChapterDB = $this->_model->getNextChapter($data["event"][0]->eventID, Session::get("memberID"), "l3");

                                    if (!empty($nextChapterDB))
                                        $nextChapter = $nextChapterDB[0]->chapter;

                                    $postdata = [
                                        "step" => EventSteps::NONE,
                                        "currentChapter" => -1
                                    ];

                                    if ($nextChapter > -1) {
                                        $postdata["step"] = EventSteps::PRAY;
                                        $postdata["currentChapter"] = $nextChapter;
                                    }

                                    $this->_model->updateL3Checker($postdata, ["l3chID" => $data["event"][0]->l3chID]);

                                    if ($nextChapter > -1)
                                        Url::redirect('events/checker-tn-l3/' . $data["event"][0]->eventID);
                                    else
                                        Url::redirect('events/');
                                } else {
                                    $error[] = __("checker_not_ready_error");
                                }
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/L3Notes/Checker')
                            ->nest('page', 'Events/L3Notes/PeerEdit')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                        break;

                    case EventSteps::FINISHED:
                        $data["success"] = __("you_event_finished_success");

                        return View::make('Events/L3Notes/Checker')
                            ->nest('page', 'Events/L3Notes/Finished')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/L3Notes/Checker')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/L3Notes/Checker')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function checkerNotesL3Peer($eventID, $memberID, $chapter)
    {
        $isXhr = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isXhr = true;
            $response["success"] = false;
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEventsForCheckerL3(
            Session::get("memberID"), $eventID, $memberID, $chapter);
        $data["isChecker"] = true;

        if (!empty($data["event"])) {
            if (Session::get("memberID") == $data["event"][0]->memberID) {
                Url::redirect('events/');
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::L3_CHECK || $data["event"][0]->state == EventStates::COMPLETE) {
                $data["turn"] = $this->makeTurnCredentials();

                $chapters = $this->_model->getChapters($eventID, null, $chapter);
                $data["event"][0]->chunks = [];
                if (!empty($chapters)) {
                    $data["event"][0]->chunks = $chapters[0]["chunks"];
                }

                switch ($data["event"][0]->step) {
                    case EventCheckSteps::PEER_REVIEW_L3:
                    case EventCheckSteps::PEER_EDIT_L3:

                        $data["currentChapter"] = $data["event"][0]->currentChapter;

                        // Get related ulb project
                        $ulbProject = $this->_model->getProject(["projects.projectID"], [
                            ["projects.glID", $data["event"][0]->glID],
                            ["projects.gwLang", $data["event"][0]->gwLang],
                            ["projects.targetLang", $data["event"][0]->targetLang],
                            ["projects.bookProject", "ulb"]
                        ]);

                        if (!empty($ulbProject))
                            $ulbEvent = $this->_model->getEvent(null, $ulbProject[0]->projectID, $data["event"][0]->bookCode);

                        if (empty($ulbEvent) || $ulbEvent[0]->state != EventStates::COMPLETE)
                            $error[] = __("l2_l3_event_notexist_error");

                        $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);

                        if (!isset($error)) {
                            // Get ulb translation
                            $ulbTranslationData = $this->_translationModel->getEventTranslationByEventID(
                                $ulbEvent[0]->eventID,
                                $data["event"][0]->currentChapter
                            );
                            $ulbTranslation = ["l2" => [], "l3" => []];

                            foreach ($ulbTranslationData as $tv) {
                                $arr = (array)json_decode($tv->translatedVerses, true);

                                $ulbTranslation["l2"] += $arr[EventMembers::L2_CHECKER]["verses"];
                                $ulbTranslation["l3"] += $arr[EventMembers::L3_CHECKER]["verses"];
                            }
                            $data["ulb_translation"] = $ulbTranslation;

                            // Remove footnotes to avoid comparison errors
                            $data["ulb_translation"]["l2"] = array_map(function ($elm) {
                                return preg_replace("/<span.*<\/span>/mU", "", $elm);
                            }, $data["ulb_translation"]["l2"]);

                            $data["ulb_translation"]["l3"] = array_map(function ($elm) {
                                return preg_replace("/<span.*<\/span>/mU", "", $elm);
                            }, $data["ulb_translation"]["l3"]);

                            // Get Notes L2 translation
                            $translationData = $this->_translationModel->getEventTranslationByEventID(
                                $data["event"][0]->eventID,
                                $data["event"][0]->currentChapter
                            );
                            $translation = array();

                            foreach ($translationData as $tv) {
                                $arr = (array)json_decode($tv->translatedVerses, true);
                                $arr["tID"] = $tv->tID;
                                $translation[] = $arr;
                            }
                            $data["translation"] = $translation;

                            $chapters = $this->_model->getChapters(
                                $data["event"][0]->eventID, null,
                                $data["event"][0]->currentChapter);

                            $data["chunks"] = (array)json_decode($chapters[0]["chunks"], true);
                            $lastChunk = $data["chunks"][sizeof($data["chunks"]) - 1];
                            $data["totalVerses"] = $lastChunk[sizeof($lastChunk) - 1];

                            $data["comments"] = $this->getComments(
                                $data["event"][0]->eventID,
                                $data["event"][0]->currentChapter);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)) {
                                    if ($data["event"][0]->step == $data["event"][0]->peerStep) {
                                        if ($peerCheck[$data["event"][0]->currentChapter]["done"] == 0)
                                            $peerCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                        else
                                            $peerCheck[$data["event"][0]->currentChapter]["done"] = 2;

                                        $this->_model->updateL3Checker([
                                                "peerCheck" => json_encode($peerCheck)
                                            ]
                                            , ["l3chID" => $data["event"][0]->l3chID]);

                                        $response["success"] = true;
                                    } else {
                                        $error[] = __("peer_checker_not_ready_error");
                                        $response["errors"] = $error;
                                    }
                                    echo json_encode($response);
                                    exit;
                                }
                            }
                        }

                        $data["next_step"] = $data["event"][0]->step == EventCheckSteps::PEER_REVIEW_L3
                            ? EventCheckSteps::PEER_EDIT_L3
                            : "continue_alt";

                        return View::make('Events/L3Notes/Checker')
                            ->nest('page', 'Events/L3Notes/PeerReview')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/L3Notes/Checker')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/L3Notes/Checker')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    /**
     * View for SUN Level 2 check page
     * @url /events/check-sun-l2
     * @param $eventID
     * @return View
     */
    public function checkerSunL2($eventID)
    {
        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["isCheckerPage"] = true;
        $data["event"] = $this->_model->getMemberEventsForCheckerL2(
            Session::get("memberID"),
            $eventID
        );

        $title = "";

        if (!empty($data["event"])) {
            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::L2_CHECK || $data["event"][0]->state == EventStates::L2_CHECKED) {
                if ($data["event"][0]->step == EventCheckSteps::NONE)
                    Url::redirect("events/information-sun-l2/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                switch ($data["event"][0]->step) {
                    case EventCheckSteps::PRAY:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateL2Checker([
                                "step" => EventCheckSteps::NONE
                            ], [
                                "l2chID" => $data["event"][0]->l2chID
                            ]);
                            Url::redirect('events/checker-sun-l2/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {

                                $postdata = [
                                    "step" => EventCheckSteps::CONSUME,
                                    "currentChapter" => $sourceText["currentChapter"]
                                ];
                                $this->_model->updateL2Checker($postdata, [
                                    "l2chID" => $data["event"][0]->l2chID
                                ]);
                                Url::redirect('events/checker-sun-l2/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = EventCheckSteps::CONSUME;

                        return View::make('Events/L2Sun/Checker')
                            ->nest('page', 'Events/L2Sun/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                    case EventCheckSteps::CONSUME:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateL2Checker([
                                "step" => EventCheckSteps::NONE
                            ], [
                                "l2chID" => $data["event"][0]->l2chID
                            ]);
                            Url::redirect('events/checker-sun-l2/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {

                                $this->_model->updateL2Checker([
                                    "step" => EventCheckSteps::FST_CHECK
                                ], [
                                    "l2chID" => $data["event"][0]->l2chID
                                ]);
                                Url::redirect('events/checker-sun-l2/' . $data["event"][0]->eventID);
                            }
                        }

                        $data["next_step"] = EventCheckSteps::FST_CHECK . "_sun";

                        return View::make('Events/L2Sun/Checker')
                            ->nest('page', 'Events/L2Sun/Consume')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;

                    case EventCheckSteps::FST_CHECK:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateL2Checker([
                                "step" => EventCheckSteps::NONE
                            ], [
                                "l2chID" => $data["event"][0]->l2chID
                            ]);
                            Url::redirect('events/checker-sun-l2/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                // Update L2 if it's empty
                                foreach ($translation as $tr) {
                                    if (empty($tr[EventMembers::L2_CHECKER]["verses"])) {
                                        $tr[EventMembers::L2_CHECKER]["verses"] = $tr[EventMembers::TRANSLATOR]["verses"];
                                        $tID = $tr["tID"];
                                        unset($tr["tID"]);
                                        $this->_translationModel->updateTranslation(
                                            ["translatedVerses" => json_encode($tr)],
                                            ["tID" => $tID]
                                        );
                                    }
                                }

                                $chapters = [];
                                for ($i = 1; $i <= $data["event"][0]->chaptersNum; $i++) {
                                    $data["chapters"][$i] = [];
                                }

                                $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                foreach ($chaptersDB as $chapter) {
                                    $tmp["trID"] = $chapter["trID"];
                                    $tmp["memberID"] = $chapter["memberID"];
                                    $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                    $tmp["done"] = $chapter["done"];
                                    $tmp["l2memberID"] = $chapter["l2memberID"];
                                    $tmp["l2chID"] = $chapter["l2chID"];
                                    $tmp["l2checked"] = $chapter["l2checked"];

                                    $chapters[$chapter["chapter"]] = $tmp;
                                }

                                $this->_model->updateChapter([
                                    "l2checked" => true
                                ], [
                                    "eventID" => $data["event"][0]->eventID,
                                    "chapter" => $data["event"][0]->currentChapter
                                ]);

                                $chapters[$data["event"][0]->currentChapter]["l2checked"] = true;

                                // Check if the member has another chapter to check
                                // then redirect to preparation page
                                $nextChapter = 0;
                                $nextChapterDB = $this->_model->getNextChapter(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->memberID,
                                    "l2");
                                if (!empty($nextChapterDB))
                                    $nextChapter = $nextChapterDB[0]->chapter;

                                $sndCheck = (array)json_decode($data["event"][0]->sndCheck, true);
                                if (!array_key_exists($data["event"][0]->currentChapter, $sndCheck)) {
                                    $sndCheck[$data["event"][0]->currentChapter] = [
                                        "memberID" => 0,
                                        "done" => 0
                                    ];
                                }

                                $postdata = [
                                    "step" => EventSteps::NONE,
                                    "currentChapter" => 0,
                                    "sndCheck" => json_encode($sndCheck)
                                ];

                                if ($nextChapter > 0) {
                                    $postdata["step"] = EventSteps::PRAY;
                                    $postdata["currentChapter"] = $nextChapter;
                                }

                                $this->_model->updateL2Checker($postdata, [
                                    "l2chID" => $data["event"][0]->l2chID
                                ]);

                                if ($nextChapter > 0)
                                    Url::redirect('events/checker-sun-l2/' . $data["event"][0]->eventID);
                                else
                                    Url::redirect('events/');
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/L2Sun/Checker')
                            ->nest('page', 'Events/L2Sun/FstCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/L2Sun/Checker')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/L2Sun/Checker')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    /**
     * View for 2nd check in Sun Level2 event
     * @param $eventID
     * @param $memberID
     * @return View
     */
    public function checkerSunL2Continue($eventID, $memberID, $chapter)
    {
        $isXhr = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isXhr = true;
            $response["success"] = false;
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["isCheckerPage"] = true;
        $data["event"] = $this->_model->getMemberEventsForCheckerL2(
            Session::get("memberID"), $eventID, $memberID, $chapter);

        if (!empty($data["event"])) {
            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::L2_CHECK || $data["event"][0]->state == EventStates::L2_CHECKED) {
                $data["turn"] = $this->makeTurnCredentials();

                $chapters = $this->_model->getChapters($eventID, null, $chapter);
                $data["event"][0]->chunks = [];
                if (!empty($chapters)) {
                    $data["event"][0]->chunks = $chapters[0]["chunks"];
                }

                switch ($data["event"][0]->step) {
                    case EventCheckSteps::SND_CHECK:
                        $sourceText = $this->getScriptureSourceText($data);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            Url::redirect('events');
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $sndCheck = (array)json_decode($data["event"][0]->sndCheck, true);
                                if (array_key_exists($data["event"][0]->currentChapter, $sndCheck)) {
                                    $sndCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                }

                                $postdata = [
                                    "sndCheck" => json_encode($sndCheck)
                                ];

                                $this->_model->updateL2Checker($postdata, [
                                    "l2chID" => $data["event"][0]->l2chID
                                ]);

                                // Check if the whole book was checked and set its state to L2_CHECKED
                                $chapters = [];
                                $events = $this->_model->getMembersForL2Event($data["event"][0]->eventID);

                                foreach ($events as $event) {
                                    $snd = (array)json_decode($event["sndCheck"], true);
                                    if (!empty($snd)) {
                                        $chapters += $snd;
                                    }
                                }

                                if (sizeof($chapters) == $data["event"][0]->chaptersNum) {
                                    $allDone = true;
                                    foreach ($chapters as $chapter) {
                                        if ($chapter["done"] == 0) {
                                            $allDone = false;
                                            break;
                                        }
                                    }

                                    if ($allDone) {
                                        $this->_model->updateEvent([
                                            "state" => EventStates::L2_CHECKED
                                        ], [
                                            "eventID" => $data["event"][0]->eventID
                                        ]);
                                    }
                                }

                                Url::redirect('events');
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/L2Sun/Checker')
                            ->nest('page', 'Events/L2Sun/SndCheck')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/L2Sun/Checker')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/L2Sun/Checker')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function checkerSunL3($eventID)
    {
        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["isChecker"] = false;
        $data["isCheckerPage"] = true;
        $data["event"] = $this->_model->getMemberEventsForCheckerL3(Session::get("memberID"), $eventID);

        if (!empty($data["event"])) {
            if ($data["event"][0]->bookProject != "sun") {
                if (in_array($data["event"][0]->bookProject, ["udb", "ulb"]))
                    Url::redirect("events/checker-l3/" . $eventID);
                else
                    Url::redirect("events/checker-" . $data["event"][0]->bookProject . "-l3/" . $eventID);
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if (($data["event"][0]->state == EventStates::L3_CHECK
                || $data["event"][0]->state == EventStates::COMPLETE)) {
                if ($data["event"][0]->step == EventSteps::NONE)
                    Url::redirect("events/information-sun-l3/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                switch ($data["event"][0]->step) {
                    case EventCheckSteps::PRAY:

                        $data["currentChapter"] = $data["event"][0]->currentChapter;
                        if ($data["event"][0]->currentChapter == 0) {
                            $nextChapter = $this->_model->getNextChapter(
                                $data["event"][0]->eventID,
                                $data["event"][0]->memberID,
                                "l3");
                            $data["currentChapter"] = $nextChapter[0]->chapter;
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);
                                $peerCheck[$data["currentChapter"]] = [
                                    "memberID" => 0,
                                    "done" => 0
                                ];

                                $postdata = [
                                    "step" => EventCheckSteps::PEER_REVIEW_L3,
                                    "currentChapter" => $data["currentChapter"],
                                    "peerCheck" => json_encode($peerCheck)
                                ];
                                $this->_model->updateL3Checker($postdata, ["l3chID" => $data["event"][0]->l3chID]);

                                Url::redirect('events/checker-sun-l3/' . $data["event"][0]->eventID);
                            }
                        }

                        // Check if translator just started translating of this book
                        $data["event"][0]->justStarted = $data["event"][0]->peerCheck == "";
                        $data["next_step"] = EventCheckSteps::PEER_REVIEW_L3;

                        return View::make('Events/L3Sun/Checker')
                            ->nest('page', 'Events/L3Sun/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                    case EventCheckSteps::PEER_REVIEW_L3:
                        $sourceText = $this->getScriptureSourceText($data);
                        $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $data["event"][0]->chkMemberID = null;
                                $data["event"][0]->checkerFName = null;
                                $data["event"][0]->checkerLName = null;

                                if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)
                                    && $peerCheck[$data["event"][0]->currentChapter]["memberID"] > 0) {
                                    $member = $this->memberRepo->get($peerCheck[$data["event"][0]->currentChapter]["memberID"]);
                                    if ($member) {
                                        $data["event"][0]->chkMemberID = $member->memberID;
                                        $data["event"][0]->checkerFName = $member->firstName;
                                        $data["event"][0]->checkerLName = $member->lastName;
                                    }
                                }
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateL3Checker([
                                "step" => EventCheckSteps::NONE
                            ], [
                                "l3chID" => $data["event"][0]->l3chID
                            ]);
                            Url::redirect('events/checker-sun-l3/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)
                                    && $peerCheck[$data["event"][0]->currentChapter]["done"] == 1) {
                                    $postdata = [
                                        "step" => EventCheckSteps::PEER_EDIT_L3
                                    ];
                                    $this->_model->updateL3Checker($postdata, ["l3chID" => $data["event"][0]->l3chID]);
                                    Url::redirect('events/checker-sun-l3/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("checker_not_ready_error");
                                }
                            }
                        }

                        $data["next_step"] = EventCheckSteps::PEER_EDIT_L3;

                        return View::make('Events/L3Sun/Checker')
                            ->nest('page', 'Events/L3Sun/PeerReview')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                        break;

                    case EventCheckSteps::PEER_EDIT_L3:
                        $sourceText = $this->getScriptureSourceText($data);
                        $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $data["event"][0]->chkMemberID = null;
                                $data["event"][0]->checkerFName = null;
                                $data["event"][0]->checkerLName = null;

                                if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)
                                    && $peerCheck[$data["event"][0]->currentChapter]["memberID"] > 0) {
                                    $member = $this->memberRepo->get($peerCheck[$data["event"][0]->currentChapter]["memberID"]);
                                    if ($member) {
                                        $data["event"][0]->chkMemberID = $member->memberID;
                                        $data["event"][0]->checkerFName = $member->firstName;
                                        $data["event"][0]->checkerLName = $member->lastName;
                                    }
                                }
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateL3Checker([
                                "step" => EventCheckSteps::NONE
                            ], [
                                "l3chID" => $data["event"][0]->l3chID
                            ]);
                            Url::redirect('events/checker-sun-l3/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)
                                    && $peerCheck[$data["event"][0]->currentChapter]["done"] == 2) {
                                    // Update L3 if it's empty
                                    foreach ($translation as $tr) {
                                        if (empty($tr[EventMembers::L3_CHECKER]["verses"])) {
                                            $tr[EventMembers::L3_CHECKER]["verses"] = $tr[EventMembers::TRANSLATOR]["verses"];
                                            $tID = $tr["tID"];
                                            unset($tr["tID"]);
                                            $this->_translationModel->updateTranslation(
                                                ["translatedVerses" => json_encode($tr)],
                                                ["tID" => $tID]
                                            );
                                        }
                                    }

                                    $chapters = [];
                                    for ($i = 1; $i <= $data["event"][0]->chaptersNum; $i++) {
                                        $data["chapters"][$i] = [];
                                    }

                                    $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                    foreach ($chaptersDB as $chapter) {
                                        $tmp["trID"] = $chapter["trID"];
                                        $tmp["memberID"] = $chapter["memberID"];
                                        $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                        $tmp["l3checked"] = $chapter["l3checked"];

                                        $chapters[$chapter["chapter"]] = $tmp;
                                    }

                                    $chapters[$data["event"][0]->currentChapter]["l3checked"] = true;
                                    $this->_model->updateChapter(["l3checked" => true], [
                                        "eventID" => $data["event"][0]->eventID,
                                        "chapter" => $data["event"][0]->currentChapter]);

                                    // Check if whole scripture is finished
                                    if ($this->checkBookFinished($chapters, $data["event"][0]->chaptersNum, false, 3))
                                        $this->_model->updateEvent([
                                            "state" => EventStates::COMPLETE,
                                            "dateTo" => date("Y-m-d H:i:s", time())],
                                            ["eventID" => $data["event"][0]->eventID]);

                                    // Check if the member has another chapter to check
                                    // then redirect to preparation page
                                    $nextChapter = 0;
                                    $nextChapterDB = $this->_model->getNextChapter($data["event"][0]->eventID, Session::get("memberID"), "l3");

                                    if (!empty($nextChapterDB))
                                        $nextChapter = $nextChapterDB[0]->chapter;

                                    $postdata = [
                                        "step" => EventSteps::NONE,
                                        "currentChapter" => 0
                                    ];

                                    if ($nextChapter > 0) {
                                        $postdata["step"] = EventSteps::PRAY;
                                        $postdata["currentChapter"] = $nextChapter;
                                    }

                                    $this->_model->updateL3Checker($postdata, ["l3chID" => $data["event"][0]->l3chID]);

                                    if ($nextChapter > 0)
                                        Url::redirect('events/checker-sun-l3/' . $data["event"][0]->eventID);
                                    else
                                        Url::redirect('events/');
                                } else {
                                    $error[] = __("checker_not_ready_error");
                                }
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/L3Sun/Checker')
                            ->nest('page', 'Events/L3Sun/PeerEdit')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                        break;

                    case EventSteps::FINISHED:
                        $data["success"] = __("you_event_finished_success");

                        return View::make('Events/L3Sun/Checker')
                            ->nest('page', 'Events/L3Sun/Finished')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/L3Sun/Checker')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/L3Sun/Checker')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function checkerSunL3Peer($eventID, $memberID, $chapter)
    {
        $isXhr = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isXhr = true;
            $response["success"] = false;
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEventsForCheckerL3(
            Session::get("memberID"), $eventID, $memberID, $chapter);
        $data["isChecker"] = true;

        if (!empty($data["event"])) {
            if (Session::get("memberID") == $data["event"][0]->memberID) {
                Url::redirect('events/');
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::L3_CHECK || $data["event"][0]->state == EventStates::COMPLETE) {
                $data["turn"] = $this->makeTurnCredentials();

                $chapters = $this->_model->getChapters($eventID, null, $chapter);
                $data["event"][0]->chunks = [];
                if (!empty($chapters)) {
                    $data["event"][0]->chunks = $chapters[0]["chunks"];
                }

                switch ($data["event"][0]->step) {
                    case EventCheckSteps::PEER_REVIEW_L3:
                    case EventCheckSteps::PEER_EDIT_L3:
                        $sourceText = $this->getScriptureSourceText($data);
                        $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $error[] = $sourceText["error"];
                            $data["error"] = $sourceText["error"];
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)) {
                                    if ($data["event"][0]->step == $data["event"][0]->peerStep) {
                                        if ($peerCheck[$data["event"][0]->currentChapter]["done"] == 0)
                                            $peerCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                        else
                                            $peerCheck[$data["event"][0]->currentChapter]["done"] = 2;

                                        $this->_model->updateL3Checker([
                                                "peerCheck" => json_encode($peerCheck)
                                            ]
                                            , ["l3chID" => $data["event"][0]->l3chID]);

                                        $response["success"] = true;
                                    } else {
                                        $error[] = __("peer_checker_not_ready_error");
                                        $response["errors"] = $error;
                                    }
                                    echo json_encode($response);
                                    exit;
                                }
                            }
                        }

                        $data["next_step"] = $data["event"][0]->step == EventCheckSteps::PEER_REVIEW_L3
                            ? EventCheckSteps::PEER_EDIT_L3
                            : "continue_alt";

                        return View::make('Events/L3Sun/Checker')
                            ->nest('page', 'Events/L3Sun/PeerReview')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/L3Sun/Checker')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/L3Sun/Checker')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function checkerL3($eventID)
    {
        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["isChecker"] = false;
        $data["isCheckerPage"] = true;
        $data["event"] = $this->_model->getMemberEventsForCheckerL3(Session::get("memberID"), $eventID);

        if (!empty($data["event"])) {
            if (!in_array($data["event"][0]->bookProject, ["ulb", "udb"])) {
                Url::redirect("events/checker-" . $data["event"][0]->bookProject . "-l3/" . $eventID);
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if (($data["event"][0]->state == EventStates::L3_CHECK
                || $data["event"][0]->state == EventStates::COMPLETE)) {
                if ($data["event"][0]->step == EventSteps::NONE)
                    Url::redirect("events/information-l3/" . $eventID);

                $data["turn"] = $this->makeTurnCredentials();

                switch ($data["event"][0]->step) {
                    case EventCheckSteps::PRAY:

                        $data["currentChapter"] = $data["event"][0]->currentChapter;
                        if ($data["event"][0]->currentChapter == 0) {
                            $nextChapter = $this->_model->getNextChapter(
                                $data["event"][0]->eventID,
                                $data["event"][0]->memberID,
                                "l3");
                            $data["currentChapter"] = $nextChapter[0]->chapter;
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);
                                $peerCheck[$data["currentChapter"]] = [
                                    "memberID" => 0,
                                    "done" => 0
                                ];

                                $postdata = [
                                    "step" => EventCheckSteps::PEER_REVIEW_L3,
                                    "currentChapter" => $data["currentChapter"],
                                    "peerCheck" => json_encode($peerCheck)
                                ];
                                $this->_model->updateL3Checker($postdata, ["l3chID" => $data["event"][0]->l3chID]);

                                Url::redirect('events/checker-l3/' . $data["event"][0]->eventID);
                            }
                        }

                        // Check if translator just started translating of this book
                        $data["event"][0]->justStarted = $data["event"][0]->peerCheck == "";
                        $data["next_step"] = EventCheckSteps::PEER_REVIEW_L3;

                        return View::make('Events/L3/Checker')
                            ->nest('page', 'Events/L3/Pray')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                    case EventCheckSteps::PEER_REVIEW_L3:
                        $sourceText = $this->getScriptureSourceText($data);
                        $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $data["event"][0]->chkMemberID = null;
                                $data["event"][0]->checkerFName = null;
                                $data["event"][0]->checkerLName = null;

                                if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)
                                    && $peerCheck[$data["event"][0]->currentChapter]["memberID"] > 0) {
                                    $member = $this->memberRepo->get($peerCheck[$data["event"][0]->currentChapter]["memberID"]);
                                    if ($member) {
                                        $data["event"][0]->chkMemberID = $member->memberID;
                                        $data["event"][0]->checkerFName = $member->firstName;
                                        $data["event"][0]->checkerLName = $member->lastName;
                                    }
                                }
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateL3Checker([
                                "step" => EventCheckSteps::NONE
                            ], [
                                "l3chID" => $data["event"][0]->l3chID
                            ]);
                            Url::redirect('events/checker-l3/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)
                                    && $peerCheck[$data["event"][0]->currentChapter]["done"] == 1) {
                                    $postdata = [
                                        "step" => EventCheckSteps::PEER_EDIT_L3
                                    ];
                                    $this->_model->updateL3Checker($postdata, ["l3chID" => $data["event"][0]->l3chID]);
                                    Url::redirect('events/checker-l3/' . $data["event"][0]->eventID);
                                } else {
                                    $error[] = __("checker_not_ready_error");
                                }
                            }
                        }

                        $data["next_step"] = EventCheckSteps::PEER_EDIT_L3;

                        return View::make('Events/L3/Checker')
                            ->nest('page', 'Events/L3/PeerReview')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                        break;

                    case EventCheckSteps::PEER_EDIT_L3:
                        $sourceText = $this->getScriptureSourceText($data);
                        $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);

                                $data["event"][0]->chkMemberID = null;
                                $data["event"][0]->checkerFName = null;
                                $data["event"][0]->checkerLName = null;

                                if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)
                                    && $peerCheck[$data["event"][0]->currentChapter]["memberID"] > 0) {
                                    $member = $this->memberRepo->get($peerCheck[$data["event"][0]->currentChapter]["memberID"]);
                                    if ($member) {
                                        $data["event"][0]->chkMemberID = $member->memberID;
                                        $data["event"][0]->checkerFName = $member->firstName;
                                        $data["event"][0]->checkerLName = $member->lastName;
                                    }
                                }
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $this->_model->updateL3Checker([
                                "step" => EventCheckSteps::NONE
                            ], [
                                "l3chID" => $data["event"][0]->l3chID
                            ]);
                            Url::redirect('events/checker-l3/' . $data["event"][0]->eventID);
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)
                                    && $peerCheck[$data["event"][0]->currentChapter]["done"] == 2) {
                                    // Update L3 if it's empty
                                    foreach ($translation as $tr) {
                                        if (empty($tr[EventMembers::L3_CHECKER]["verses"])) {
                                            $tr[EventMembers::L3_CHECKER]["verses"] = $tr[EventMembers::L2_CHECKER]["verses"];
                                            $tID = $tr["tID"];
                                            unset($tr["tID"]);
                                            $this->_translationModel->updateTranslation(
                                                ["translatedVerses" => json_encode($tr)],
                                                ["tID" => $tID]
                                            );
                                        }
                                    }

                                    $chapters = [];
                                    for ($i = 1; $i <= $data["event"][0]->chaptersNum; $i++) {
                                        $data["chapters"][$i] = [];
                                    }

                                    $chaptersDB = $this->_model->getChapters($data["event"][0]->eventID);

                                    foreach ($chaptersDB as $chapter) {
                                        $tmp["trID"] = $chapter["trID"];
                                        $tmp["memberID"] = $chapter["memberID"];
                                        $tmp["chunks"] = json_decode($chapter["chunks"], true);
                                        $tmp["l3checked"] = $chapter["l3checked"];

                                        $chapters[$chapter["chapter"]] = $tmp;
                                    }

                                    $chapters[$data["event"][0]->currentChapter]["l3checked"] = true;
                                    $this->_model->updateChapter(["l3checked" => true], [
                                        "eventID" => $data["event"][0]->eventID,
                                        "chapter" => $data["event"][0]->currentChapter]);

                                    // Check if whole scripture is finished
                                    if ($this->checkBookFinished($chapters, $data["event"][0]->chaptersNum, false, 3))
                                        $this->_model->updateEvent([
                                            "state" => EventStates::COMPLETE,
                                            "dateTo" => date("Y-m-d H:i:s", time())],
                                            ["eventID" => $data["event"][0]->eventID]);

                                    // Check if the member has another chapter to check
                                    // then redirect to preparation page
                                    $nextChapter = 0;
                                    $nextChapterDB = $this->_model->getNextChapter($data["event"][0]->eventID, Session::get("memberID"), "l3");

                                    if (!empty($nextChapterDB))
                                        $nextChapter = $nextChapterDB[0]->chapter;

                                    $postdata = [
                                        "step" => EventSteps::NONE,
                                        "currentChapter" => 0
                                    ];

                                    if ($nextChapter > 0) {
                                        $postdata["step"] = EventSteps::PRAY;
                                        $postdata["currentChapter"] = $nextChapter;
                                    }

                                    $this->_model->updateL3Checker($postdata, ["l3chID" => $data["event"][0]->l3chID]);

                                    if ($nextChapter > 0)
                                        Url::redirect('events/checker-l3/' . $data["event"][0]->eventID);
                                    else
                                        Url::redirect('events/');
                                } else {
                                    $error[] = __("checker_not_ready_error");
                                }
                            }
                        }

                        $data["next_step"] = "continue_alt";

                        return View::make('Events/L3/Checker')
                            ->nest('page', 'Events/L3/PeerEdit')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);

                        break;

                    case EventSteps::FINISHED:
                        $data["success"] = __("you_event_finished_success");

                        return View::make('Events/L3/Checker')
                            ->nest('page', 'Events/L3/Finished')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/L3/Checker')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/L3/Checker')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function checkerL3Peer($eventID, $memberID, $chapter)
    {
        $isXhr = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isXhr = true;
            $response["success"] = false;
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data["event"] = $this->_model->getMemberEventsForCheckerL3(
            Session::get("memberID"), $eventID, $memberID, $chapter);
        $data["isChecker"] = true;

        if (!empty($data["event"])) {
            if (Session::get("memberID") == $data["event"][0]->memberID) {
                Url::redirect('events/');
            }

            $title = $data["event"][0]->name
                . " " . ($data["event"][0]->currentChapter > 0 ? $data["event"][0]->currentChapter : "")
                . " - " . $data["event"][0]->tLang
                . " - " . __($data["event"][0]->bookProject);

            if ($data["event"][0]->state == EventStates::L3_CHECK || $data["event"][0]->state == EventStates::COMPLETE) {
                $data["turn"] = $this->makeTurnCredentials();

                $chapters = $this->_model->getChapters($eventID, null, $chapter);
                $data["event"][0]->chunks = [];
                if (!empty($chapters)) {
                    $data["event"][0]->chunks = $chapters[0]["chunks"];
                }

                switch ($data["event"][0]->step) {
                    case EventCheckSteps::PEER_REVIEW_L3:
                    case EventCheckSteps::PEER_EDIT_L3:
                        $sourceText = $this->getScriptureSourceText($data);
                        $peerCheck = (array)json_decode($data["event"][0]->peerCheck, true);

                        if (!empty($sourceText)) {
                            if (!array_key_exists("error", $sourceText)) {
                                $data = $sourceText;

                                $translationData = $this->_translationModel->getEventTranslationByEventID(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter
                                );
                                $translation = array();

                                foreach ($translationData as $tv) {
                                    $arr = (array)json_decode($tv->translatedVerses, true);
                                    $arr["tID"] = $tv->tID;
                                    $translation[] = $arr;
                                }
                                $data["translation"] = $translation;

                                $data["comments"] = $this->getComments(
                                    $data["event"][0]->eventID,
                                    $data["event"][0]->currentChapter);
                            } else {
                                $error[] = $sourceText["error"];
                                $data["error"] = $sourceText["error"];
                            }
                        } else {
                            $error[] = $sourceText["error"];
                            $data["error"] = $sourceText["error"];
                        }

                        if (isset($_POST) && !empty($_POST)) {
                            if (isset($_POST["confirm_step"])) {
                                if (array_key_exists($data["event"][0]->currentChapter, $peerCheck)) {
                                    if ($data["event"][0]->step == $data["event"][0]->peerStep) {
                                        if ($peerCheck[$data["event"][0]->currentChapter]["done"] == 0)
                                            $peerCheck[$data["event"][0]->currentChapter]["done"] = 1;
                                        else
                                            $peerCheck[$data["event"][0]->currentChapter]["done"] = 2;

                                        $this->_model->updateL3Checker([
                                                "peerCheck" => json_encode($peerCheck)
                                            ]
                                            , ["l3chID" => $data["event"][0]->l3chID]);

                                        $response["success"] = true;
                                    } else {
                                        $error[] = __("peer_checker_not_ready_error");
                                        $response["errors"] = $error;
                                    }
                                    echo json_encode($response);
                                    exit;
                                }
                            }
                        }

                    $data["next_step"] = $data["event"][0]->step == EventCheckSteps::PEER_REVIEW_L3
                        ? EventCheckSteps::PEER_EDIT_L3
                        : "continue_alt";

                        return View::make('Events/L3/Checker')
                            ->nest('page', 'Events/L3/PeerReview')
                            ->shares("title", $title)
                            ->shares("data", $data)
                            ->shares("error", @$error);
                        break;
                }
            } else {
                $data["error"] = true;
                $error[] = __("wrong_event_state_error");

                return View::make('Events/L3/Checker')
                    ->shares("title", $title)
                    ->shares("data", $data)
                    ->shares("error", @$error);
            }
        } else {
            $error[] = __("not_in_event_error");
            $title = "Error";

            return View::make('Events/L3/Checker')
                ->shares("title", $title)
                ->shares("data", $data)
                ->shares("error", @$error);
        }
    }

    public function news()
    {
        if (!$this->_member) Url::redirect("members/login");

        $data["menu"] = 6;
        $data["notifications"] = $this->_notifications;
        $data["news"] = $this->_news;
        $data["newNewsCount"] = 0;

        return View::make('Events/News')
            ->shares("title", __("news_title"))
            ->shares("data", $data);
    }

    public function faqs()
    {
        $this->_newsModel = new NewsModel();
        $data["menu"] = 0;
        $data["faqs"] = $this->_newsModel->getFaqs();

        return View::make('Events/Faq')
            ->shares("title", __("faq_title"))
            ->shares("data", $data);
    }

    public function autosaveChunk()
    {
        $response = array("success" => false);
        $post = $_REQUEST;
        $eventID = isset($post["eventID"]) && is_numeric($post["eventID"]) ? $post["eventID"] : null;

        if ($eventID !== null) {
            $level = isset($post["level"]) && $post["level"] != "" ? $post["level"] : "l1";
            $isCheckingStep = isset($post["isChecking"]) && $post["isChecking"];
            $checkingChapter = $post["checkingChapter"] ?? null;

            $memberType = EventMembers::TRANSLATOR;
            if ($level == "l2" || $level == "l2continue")
                $memberType = EventMembers::L2_CHECKER;
            elseif ($level == "l3")
                $memberType = EventMembers::L3_CHECKER;

            if ($level == "l1") {
                $event = $this->_model->getMemberEvents(
                    Session::get("memberID"),
                    $eventID,
                    $checkingChapter,
                    $isCheckingStep,
                    false,
                    false
                );
            } elseif ($level == "l2") {
                $event = $this->_model->getCheckerL2Events(Session::get("memberID"), $eventID);
            } elseif ($level == "l2continue") {
                if (isset($post["memberID"]) && isset($post["chapter"])) {
                    $event = $this->_model->getMemberEventsForCheckerL2(
                        Session::get("memberID"),
                        $eventID,
                        $post["memberID"],
                        $post["chapter"]
                    );
                } else {
                    $response["errorType"] = "error";
                    $response["error"] = "POST data incorrect: memberID, chapter";
                    echo json_encode($response);
                    exit;
                }
            } elseif ($level == "l3") {
                $event = $this->_model->getCheckerL3Events(Session::get("memberID"), $eventID);
            } elseif ($level == "sunContinue") {
                if (isset($post["memberID"]) && isset($post["chapter"])) {
                    $event = $this->_model->getMemberEventsForSun(
                        Session::get("memberID"),
                        $eventID,
                        $post["memberID"],
                        $post["chapter"]
                    );
                } else {
                    $response["errorType"] = "error";
                    $response["error"] = "POST data incorrect: memberID, chapter";
                    echo json_encode($response);
                    exit;
                }
            } elseif ($level == "tnContinue") {
                if (isset($post["memberID"]) && isset($post["chapter"])) {
                    $event = $this->_model->getMemberEventsForNotes(
                        Session::get("memberID"),
                        $eventID,
                        $post["memberID"],
                        $post["chapter"]
                    );
                } else {
                    $response["errorType"] = "error";
                    $response["error"] = "POST data incorrect: memberID, chapter";
                    echo json_encode($response);
                    exit;
                }
            } elseif (in_array($level, ["tqContinue", "twContinue", "obsContinue"])) {
                if (isset($post["memberID"]) && isset($post["chapter"])) {
                    $event = $this->_model->getMemberEventsForOther(
                        Session::get("memberID"),
                        $eventID,
                        $post["memberID"],
                        $post["chapter"]
                    );
                } else {
                    $response["errorType"] = "error";
                    $response["error"] = "POST data incorrect: memberID, chapter";
                    echo json_encode($response);
                    exit;
                }
            } elseif ($level == "radContinue") {
                if (isset($post["memberID"]) && isset($post["chapter"])) {
                    $event = $this->_model->getMemberEventsForRadio(
                        Session::get("memberID"),
                        $eventID,
                        $post["memberID"],
                        $post["chapter"]
                    );
                } else {
                    $response["errorType"] = "error";
                    $response["error"] = "POST data incorrect: memberID, chapter";
                    echo json_encode($response);
                    exit;
                }
            }

            if (!empty($event)) {
                $mode = $event[0]->bookProject;

                switch ($event[0]->step) {
                    case EventSteps::BLIND_DRAFT:
                    case EventSteps::REARRANGE:
                    case EventSteps::SYMBOL_DRAFT:
                        if ($event[0]->step == EventSteps::SYMBOL_DRAFT)
                            $post["draft"] = $post["symbols"];

                        if (isset($post["draft"]) && Tools::trim(Tools::strip_tags($post["draft"])) != "") {
                            $chunks = json_decode($event[0]->chunks, true);
                            $chunk = $chunks[$event[0]->currentChunk];

                            $post["draft"] = preg_replace("/[\\r\\n]/", " ", $post["draft"]);
                            $post["draft"] = Tools::html_entity_decode($post["draft"]);

                            if (in_array($mode, ["tn"])) {
                                $converter = new Converter;
                                $converter->setKeepHTML(false);
                                $post["draft"] = $converter->parseString($post["draft"]);
                            } else {
                                $post["draft"] = Tools::htmlentities($post["draft"]);
                            }

                            $role = EventMembers::TRANSLATOR;

                            $translationData = $this->_translationModel->getEventTranslationByEventID(
                                $event[0]->eventID,
                                $event[0]->currentChapter,
                                $event[0]->currentChunk
                            );

                            if (in_array($mode, ["tn"]) && isset($event[0]->isCheckerPage) && $event[0]->isCheckerPage) {
                                $role = EventMembers::CHECKER;
                            }

                            $shoudUpdate = false;

                            if (!empty($translationData)) {
                                if ($translationData[0]->chapter == $event[0]->currentChapter &&
                                    $translationData[0]->chunk == $event[0]->currentChunk) {
                                    $translationVerses = json_decode($translationData[0]->translatedVerses, true);
                                    $shoudUpdate = true;
                                }
                            }

                            if (!$shoudUpdate) {
                                $trArr = [];
                                if (in_array($mode, ["tn"])) {
                                    $trArr["verses"] = trim($post["draft"]);
                                } elseif ($mode == "sun") {
                                    $trArr["words"] = trim($post["draft"]);
                                    $trArr["symbols"] = "";
                                    $trArr["bt"] = "";
                                    $trArr["verses"] = [];
                                } elseif ($mode == "obs") {
                                    $trArr["verses"]["title"] = trim($post["draft"]);
                                    $trArr["verses"]["img"] = trim($post["img"]);
                                } else {
                                    $trArr["blind"] = trim($post["draft"]);
                                    $trArr["verses"] = [];
                                }

                                $translationVerses = array(
                                    EventMembers::TRANSLATOR => $trArr,
                                    EventMembers::L2_CHECKER => array(
                                        "verses" => array()
                                    ),
                                    EventMembers::L3_CHECKER => array(
                                        "verses" => array()
                                    ),
                                );

                                if (in_array($mode, ["tn","obs"]))
                                    $translationVerses[EventMembers::CHECKER] = [
                                        "verses" => array()
                                    ];

                                $encoded = json_encode($translationVerses);
                                $json_error = json_last_error();
                                if ($json_error === JSON_ERROR_NONE) {
                                    $trData = array(
                                        "projectID" => $event[0]->projectID,
                                        "eventID" => $event[0]->eventID,
                                        "trID" => $event[0]->trID,
                                        "targetLang" => $event[0]->targetLang,
                                        "bookProject" => $event[0]->bookProject,
                                        "sort" => $event[0]->sort,
                                        "bookCode" => $event[0]->bookCode,
                                        "chapter" => $event[0]->currentChapter,
                                        "chunk" => $event[0]->currentChunk,
                                        "firstvs" => $chunk[0],
                                        "translatedVerses" => $encoded,
                                        "dateCreate" => date('Y-m-d H:i:s')
                                    );

                                    $tID = $this->_translationModel->createTranslation($trData);

                                    if (is_numeric($tID)) {
                                        $response["chapter"] = $event[0]->currentChapter;
                                        $response["chunk"] = $event[0]->currentChunk;
                                        $response["success"] = true;
                                    }
                                } else {
                                    $response["errorType"] = "json";
                                    $response["error"] = "Json error: " . $json_error;
                                }
                            } else {
                                if (in_array($mode, ["tn"])) {
                                    $translationVerses[$role]["verses"] = trim($post["draft"]);
                                } elseif ($mode == "sun") {
                                    if ($event[0]->step == EventSteps::SYMBOL_DRAFT)
                                        $translationVerses[$role]["symbols"] = trim($post["draft"]);
                                    else
                                        $translationVerses[$role]["words"] = trim($post["draft"]);
                                } elseif ($mode == "obs") {
                                    $translationVerses[$role]["verses"]["title"] = trim($post["draft"]);
                                    $translationVerses[$role]["verses"]["img"] = trim($post["img"]);
                                } else {
                                    $translationVerses[$role]["blind"] = trim($post["draft"]);
                                }

                                $encoded = json_encode($translationVerses);
                                $json_error = json_last_error();
                                if ($json_error === JSON_ERROR_NONE) {
                                    $trData = array(
                                        "translatedVerses" => $encoded,
                                    );

                                    $this->_translationModel->updateTranslation($trData, array("tID" => $translationData[0]->tID));
                                    $response["chapter"] = $event[0]->currentChapter;
                                    $response["chunk"] = $event[0]->currentChunk;
                                    $response["success"] = true;
                                } else {
                                    $response["errorType"] = "json";
                                    $response["error"] = "Json error: " . $json_error;
                                }
                            }
                        }
                        break;

                    case EventSteps::MULTI_DRAFT:
                    case EventSteps::SELF_CHECK:
                    case EventSteps::PEER_REVIEW:
                    case EventSteps::KEYWORD_CHECK:
                    case EventSteps::CONTENT_REVIEW:
                    case EventSteps::THEO_CHECK:
                        if (isset($post["chunks"]) && is_array($post["chunks"]) && !empty($post["chunks"])) {
                            if ($event[0]->step == EventSteps::PEER_REVIEW
                                || $event[0]->step == EventSteps::KEYWORD_CHECK
                                || $event[0]->step == EventSteps::CONTENT_REVIEW) {
                                if ($event[0]->checkDone) {
                                    $response["errorType"] = "checkDone";
                                    $response["error"] = __("not_possible_to_save_error");
                                    echo json_encode($response);
                                    exit;
                                }
                            }

                            $role = EventMembers::TRANSLATOR;
                            $trID = $event[0]->trID;

                            if (in_array($mode, ["tn", "tq", "tw", "rad","obs"]) && isset($event[0]->isCheckerPage) && $event[0]->isCheckerPage) {
                                $role = EventMembers::CHECKER;
                            }

                            $translationData = $this->_translationModel->getEventTranslation(
                                $trID,
                                $event[0]->currentChapter);

                            if ($event[0]->step == EventSteps::MULTI_DRAFT && empty($translationData)) {
                                $translationVerses = array(
                                    EventMembers::TRANSLATOR => array(
                                        "verses" => ""
                                    ),
                                    EventMembers::CHECKER => array(
                                        "verses" => ""
                                    ),
                                    EventMembers::L2_CHECKER => array(
                                        "verses" => array()
                                    ),
                                    EventMembers::L3_CHECKER => array(
                                        "verses" => array()
                                    ),
                                );
                                $encoded = json_encode($translationVerses);
                                $chunks = json_decode($event[0]->chunks, true);

                                foreach ($post["chunks"] as $key => $chunk) {
                                    $chunk = $chunks[$key];
                                    $trData = array(
                                        "projectID" => $event[0]->projectID,
                                        "eventID" => $event[0]->eventID,
                                        "trID" => $event[0]->trID,
                                        "targetLang" => $event[0]->targetLang,
                                        "bookProject" => $event[0]->bookProject,
                                        "sort" => $event[0]->sort,
                                        "bookCode" => $event[0]->bookCode,
                                        "chapter" => $event[0]->currentChapter,
                                        "chunk" => $key,
                                        "firstvs" => $mode == "tw" ? $key : $chunk[0],
                                        "translatedVerses" => $encoded,
                                        "dateCreate" => date('Y-m-d H:i:s')
                                    );

                                    $this->_translationModel->createTranslation($trData);
                                }

                                $translationData = $this->_translationModel->getEventTranslation(
                                    $trID,
                                    $event[0]->currentChapter);
                            }

                            $translation = array();

                            foreach ($translationData as $tv) {
                                $arr = json_decode($tv->translatedVerses, true);
                                $arr["tID"] = $tv->tID;
                                $translation[] = $arr;
                            }

                            if (!empty($translation)) {
                                // Clean empty spaces
                                $post["chunks"] = array_map(function ($elm) {
                                    return Tools::trim($elm);
                                }, $post["chunks"]);

                                // filter out empty chunks
                                $post["chunks"] = array_filter($post["chunks"], function ($v) {
                                    return !empty(Tools::trim(Tools::strip_tags($v)));
                                });

                                $section = "blind";
                                $symbols = [];
                                if ($mode == "sun") {
                                    if ($event[0]->step == EventSteps::SELF_CHECK)
                                        $section = "bt";
                                    elseif ($event[0]->step == EventSteps::CONTENT_REVIEW)
                                        $section = "symbols";
                                    elseif ($event[0]->step == EventSteps::THEO_CHECK || $event[0]->sourceBible == "odb")
                                        $section = "symbols";

                                    if (isset($post["symbols"]) && is_array($post["symbols"]) && !empty($post["symbols"])) {
                                        $post["symbols"] = array_map(function ($elm) {
                                            return Tools::trim($elm);
                                        }, $post["symbols"]);
                                        $post["symbols"] = array_filter($post["symbols"], function ($v) {
                                            return !empty(Tools::trim(strip_tags($v)));
                                        });

                                        $symbols = $post["symbols"];
                                    }
                                } elseif (in_array($mode, ["tn", "tq", "tw", "rad", "obs"])) {
                                    $section = "verses";
                                }

                                $updated = 0;
                                foreach ($translation as $key => $chunk) {
                                    if (!isset($post["chunks"][$key])) continue;

                                    $post["chunks"][$key] = Tools::html_entity_decode($post["chunks"][$key]);

                                    if (in_array($mode, ["tn", "tq", "tw"])) {
                                        $converter = new Converter;
                                        $converter->setKeepHTML(false);
                                        $post["chunks"][$key] = $converter->parseString($post["chunks"][$key]);

                                        if (!array_key_exists(EventMembers::CHECKER, $chunk)) {
                                            $chunk[EventMembers::CHECKER] = ["verses" => ""];
                                        }
                                    } else {
                                        $post["chunks"][$key] = Tools::htmlentities($post["chunks"][$key]);
                                    }

                                    $shouldUpdate = false;
                                    if ($chunk[$role][$section] != $post["chunks"][$key])
                                        $shouldUpdate = true;

                                    if ($mode == "sun" && !empty($symbols)) {
                                        if (!isset($symbols[$key])) continue;

                                        if ($chunk[$role]["symbols"] != $symbols[$key])
                                            $shouldUpdate = true;

                                        $symbols[$key] = htmlentities(html_entity_decode($symbols[$key]));
                                        $translation[$key][$role]["symbols"] = $symbols[$key];
                                    }

                                    $translation[$key][$role][$section] = $post["chunks"][$key];

                                    if ($shouldUpdate) {
                                        $tID = $translation[$key]["tID"];
                                        unset($translation[$key]["tID"]);

                                        $encoded = json_encode($translation[$key]);
                                        $json_error = json_last_error();
                                        if ($json_error === JSON_ERROR_NONE) {
                                            $trData = array(
                                                "translatedVerses" => $encoded
                                            );
                                            $this->_translationModel->updateTranslation(
                                                $trData,
                                                array(
                                                    "trID" => $trID,
                                                    "tID" => $tID));
                                            $updated++;
                                        }
                                    }
                                }

                                if ($updated)
                                    $response["success"] = true;
                                else {
                                    $response["errorType"] = "noChange";
                                    $response["error"] = "no_change";
                                }
                            }
                        }
                        break;

                    case EventCheckSteps::FST_CHECK:
                    case EventCheckSteps::SND_CHECK:
                    case EventCheckSteps::PEER_REVIEW_L2:
                    case EventCheckSteps::PEER_EDIT_L3:
                        if (isset($post["chunks"]) && is_array($post["chunks"]) && !empty($post["chunks"])) {
                            if ($event[0]->step == EventCheckSteps::PEER_REVIEW_L2) {
                                if ($event[0]->peer == 1) {
                                    $peer2Check = (array)json_decode($event[0]->peer2Check, true);
                                    if (array_key_exists($event[0]->currentChapter, $peer2Check)) {
                                        if ($peer2Check[$event[0]->currentChapter]["done"] == 1) {
                                            $response["errorType"] = "checkDone";
                                            $response["error"] = __("not_possible_to_save_error");
                                            echo json_encode($response);
                                            exit;
                                        }
                                    } else {
                                        echo json_encode($response);
                                        exit;
                                    }
                                } else {
                                    echo json_encode($response);
                                    exit;
                                }
                            } elseif ($event[0]->step == EventCheckSteps::PEER_EDIT_L3) {
                                $peerCheck = (array)json_decode($event[0]->peerCheck, true);
                                if (array_key_exists($event[0]->currentChapter, $peerCheck) &&
                                    $peerCheck[$event[0]->currentChapter]["done"] == 2) {
                                    $response["errorType"] = "checkDone";
                                    $response["error"] = __("not_possible_to_save_error");
                                    echo json_encode($response);
                                    exit;
                                }
                            }

                            $translationData = $this->_translationModel->getEventTranslationByEventID(
                                $event[0]->eventID,
                                $event[0]->currentChapter);
                            $translation = array();

                            foreach ($translationData as $tv) {
                                $arr = json_decode($tv->translatedVerses, true);
                                $arr["tID"] = $tv->tID;
                                $translation[] = $arr;
                            }

                            if (!empty($translation)) {
                                if (in_array($mode, ["tn", "tq", "tw"])) {
                                    array_walk_recursive($post["chunks"], function (&$item) {
                                        $item = trim($item);
                                    });

                                    $post["chunks"] = array_map("trim", $post["chunks"]);
                                    $post["chunks"] = array_filter($post["chunks"], function ($v) {
                                        return !empty(Tools::strip_tags($v));
                                    });
                                } else {
                                    $post["chunks"] = array_filter($post["chunks"], function ($chunk) {
                                        $verses = array_filter($chunk, function ($v) {
                                            return !empty(Tools::strip_tags(trim($v)));
                                        });
                                        $isEqual = sizeof($chunk) == sizeof($verses);
                                        return !empty($chunk) && $isEqual;
                                    });
                                }

                                $updated = 0;
                                foreach ($translation as $key => $chunk) {
                                    if (!isset($post["chunks"][$key])) continue;

                                    $post["chunks"][$key] = Tools::html_entity_decode($post["chunks"][$key]);

                                    $shouldUpdate = false;

                                    if (in_array($mode, ["tn", "tq", "tw"])) {
                                        $converter = new Converter;
                                        $converter->setKeepHTML(false);
                                        $post["chunks"][$key] = $converter->parseString($post["chunks"][$key]);

                                        if ($chunk[$memberType]["verses"] != $post["chunks"][$key])
                                            $shouldUpdate = true;
                                    } else {
                                        $post["chunks"][$key] = Tools::htmlentities($post["chunks"][$key]);
                                        if (is_array($post["chunks"][$key])) {
                                            foreach ($post["chunks"][$key] as $verse => $vText) {
                                                if (!isset($chunk[$memberType]["verses"][$verse])
                                                    || $chunk[$memberType]["verses"][$verse] != $vText) {
                                                    $shouldUpdate = true;
                                                }

                                            }
                                        } else {
                                            if ($chunk[$memberType]["verses"] != $post["chunks"][$key]) {
                                                $shouldUpdate = true;
                                            }
                                        }
                                    }

                                    $translation[$key][$memberType]["verses"] = $post["chunks"][$key];

                                    if ($shouldUpdate) {
                                        $tID = $translation[$key]["tID"];
                                        unset($translation[$key]["tID"]);

                                        $encoded = json_encode($translation[$key]);
                                        $json_error = json_last_error();
                                        if ($json_error === JSON_ERROR_NONE) {
                                            $trData = array(
                                                "translatedVerses" => $encoded
                                            );
                                            $this->_translationModel->updateTranslation(
                                                $trData,
                                                array(
                                                    "tID" => $tID));
                                            $updated++;
                                        }
                                    }
                                }

                                if ($updated)
                                    $response["success"] = true;
                                else {
                                    $response["errorType"] = "noChange";
                                    $response["error"] = "no_change";
                                }
                            }
                        }
                        break;
                }
            }
        }

        echo json_encode($response);
    }

    public function autosaveVerseLangInput()
    {
        $response = array("success" => false);
        $post = Gump::xss_clean($_REQUEST);
        $eventID = isset($post["eventID"]) && is_numeric($post["eventID"]) ? $post["eventID"] : null;

        if ($eventID !== null) {
            $event = $this->_model->getMemberEvents(
                Session::get("memberID"),
                $eventID,
                null,
                false,
                false,
                false
            );

            if (!empty($event)) {
                switch ($event[0]->step) {
                    case EventSteps::MULTI_DRAFT:
                    case EventSteps::SELF_CHECK:
                        if (is_array($post["verses"]) && !empty($post["verses"])) {
                            $trID = $event[0]->trID;
                            $translationData = $this->_translationModel->getEventTranslation(
                                $trID,
                                $event[0]->currentChapter);

                            $translationVerses = array(
                                EventMembers::TRANSLATOR => array(
                                    "blind" => "",
                                    "verses" => ""
                                ),
                                EventMembers::L2_CHECKER => array(
                                    "verses" => array()
                                ),
                                EventMembers::L3_CHECKER => array(
                                    "verses" => array()
                                ),
                            );

                            // Store verses and their related ids
                            $ids = [];

                            foreach ($post["verses"] as $verse => $text) {
                                $text = strip_tags(html_entity_decode($text));

                                if (empty(trim($text)) || !is_integer($verse) || $verse < 1) {
                                    if ($event[0]->step == EventSteps::SELF_CHECK) {
                                        $response["error"] = "empty imput";
                                        echo json_encode($response);
                                        exit;
                                    } else {
                                        continue;
                                    }
                                }

                                $updated = false;
                                foreach ($translationData as $chunk) {
                                    if ($chunk->firstvs == $verse) {
                                        // Update verse
                                        $translationVerses[EventMembers::TRANSLATOR]["verses"] = [];
                                        $translationVerses[EventMembers::TRANSLATOR]["verses"][$verse] = $text;

                                        $encoded = json_encode($translationVerses);
                                        $json_error = json_last_error();
                                        if ($json_error === JSON_ERROR_NONE) {
                                            $this->_translationModel->updateTranslation(
                                                ["translatedVerses" => $encoded],
                                                array(
                                                    "trID" => $trID,
                                                    "tID" => $chunk->tID));
                                            $ids[$verse] = $chunk->tID;
                                            $updated = true;
                                        } else {
                                            $response["errorType"] = "json";
                                            $response["error"] = "Json error: " . $json_error;
                                            echo json_encode($response);
                                            exit;
                                        }
                                        break;
                                    }
                                }

                                if (!$updated) {
                                    // Create verse
                                    $translationVerses[EventMembers::TRANSLATOR]["verses"] = [];
                                    $translationVerses[EventMembers::TRANSLATOR]["verses"][$verse] = $text;

                                    $encoded = json_encode($translationVerses);
                                    $json_error = json_last_error();

                                    if ($json_error === JSON_ERROR_NONE) {
                                        $trData = array(
                                            "projectID" => $event[0]->projectID,
                                            "eventID" => $event[0]->eventID,
                                            "trID" => $event[0]->trID,
                                            "targetLang" => $event[0]->targetLang,
                                            "bookProject" => $event[0]->bookProject,
                                            "sort" => $event[0]->sort,
                                            "bookCode" => $event[0]->bookCode,
                                            "chapter" => $event[0]->currentChapter,
                                            "chunk" => $verse - 1,
                                            "firstvs" => $verse,
                                            "translatedVerses" => $encoded,
                                            "dateCreate" => date('Y-m-d H:i:s')
                                        );
                                        $id = $this->_translationModel->createTranslation($trData);
                                        if ($id)
                                            $ids[$verse] = $id;
                                    } else {
                                        $response["errorType"] = "json";
                                        $response["error"] = "Json error: " . $json_error;
                                        echo json_encode($response);
                                        exit;
                                    }
                                }
                            }

                            $response["success"] = true;
                            $response["ids"] = $ids;
                        }
                        break;
                }
            }
        }

        echo json_encode($response);
    }

    public function deleteVerseLangInput()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && is_numeric($_POST["eventID"]) ? $_POST["eventID"] : null;
        $tID = isset($_POST["tID"]) && is_numeric($_POST["tID"]) ? $_POST["tID"] : null;

        if ($eventID !== null && $tID !== null) {
            $event = $this->_model->getMemberEvents(
                Session::get("memberID"),
                $eventID,
                null,
                false,
                false,
                false
            );

            if (!empty($event)) {
                if ($event[0]->step == EventSteps::MULTI_DRAFT) {
                    $deleted = $this->_translationModel->deleteTranslation([
                        "eventID" => $eventID,
                        "chapter" => $event[0]->currentChapter,
                        "tID" => $tID
                    ]);

                    if ($deleted) {
                        $response["success"] = true;
                    }
                }
            }
        }

        echo json_encode($response);
    }

    public function saveComment()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && $_POST["eventID"] != "" ? (integer)$_POST["eventID"] : null;
        $chapter = isset($_POST["chapter"]) && $_POST["chapter"] != "" ? (integer)$_POST["chapter"] : null;
        $chunk = isset($_POST["chunk"]) && $_POST["chunk"] != "" ? (integer)$_POST["chunk"] : null;
        $comment = isset($_POST["comment"]) ? $_POST["comment"] : "";
        $level = isset($_POST["level"]) ? (integer)$_POST["level"] : 1;
        $memberID = Session::get("memberID");
        $canSave = false;

        if ($eventID !== null && $chapter !== null && $chunk !== null) {
            $memberInfo = $this->_model->getEventMemberInfo($eventID, $memberID);
            foreach ($memberInfo as $info) {
                if ($info->translator == $memberID
                    || $info->l2checker == $memberID
                    || $info->l3checker == $memberID) {
                    $canSave = true;
                    break;
                }
            }

            if ($canSave) {
                $commentDB = (array)$this->_translationModel->getComment(
                    $eventID,
                    $chapter,
                    $chunk,
                    Session::get("memberID"),
                    $level
                );

                $postdata = array(
                    "text" => $comment,
                );

                if (!empty($commentDB)) {
                    if ($comment == "") {
                        $result = $this->_translationModel->deleteComment(array("cID" => $commentDB[0]->cID));
                    } else {
                        $result = $this->_translationModel->updateComment($postdata, array("cID" => $commentDB[0]->cID));
                    }
                } else {
                    $postdata += array(
                        "eventID" => $eventID,
                        "chapter" => $chapter,
                        "chunk" => $chunk,
                        "memberID" => Session::get("memberID"),
                        "level" => $level
                    );

                    $result = $this->_translationModel->createComment($postdata);
                }

                if ($result) {
                    $response["success"] = true;
                    $response["text"] = $comment;
                }
            }
        }

        echo json_encode($response);
    }

    public function getKeywords()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && $_POST["eventID"] != "" ? (integer)$_POST["eventID"] : null;
        $chapter = isset($_POST["chapter"]) && $_POST["chapter"] != "" ? (integer)$_POST["chapter"] : null;

        if ($eventID !== null && $chapter !== null) {
            $keywords = $this->_translationModel->getKeywords([
                "eventID" => $eventID,
                "chapter" => $chapter
            ]);

            if (!empty($keywords)) {
                $response["success"] = true;
                $response["text"] = $keywords;
            }
        }

        echo json_encode($response);
    }

    public function saveKeyword()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && $_POST["eventID"] != "" ? (integer)$_POST["eventID"] : null;
        $chapter = isset($_POST["chapter"]) && $_POST["chapter"] != "" ? (integer)$_POST["chapter"] : null;
        $chunk = isset($_POST["chunk"]) && $_POST["chunk"] != "" ? (integer)$_POST["chunk"] : null;
        $index = isset($_POST["index"]) && $_POST["index"] != "" ? (integer)$_POST["index"] : null;
        $verse = isset($_POST["verse"]) ? $_POST["verse"] : "";
        $text = isset($_POST["text"]) ? $_POST["text"] : "";
        $remove = isset($_POST["remove"]) && $_POST["remove"] == "true";
        $memberID = Session::get("memberID");

        if ($eventID !== null && $chapter !== null && $chunk !== null && $index > -1 && $verse != null) {
            $memberInfo = $this->_model->getEventMemberInfo($eventID, $memberID);

            $canKeyword = false;
            $canCreate = true;

            if (!empty($memberInfo) || Session::get("isSuperAdmin")) {
                foreach ($memberInfo as $info) {
                    if (!in_array($info->bookProject, ["tn"])) {
                        if ($info->bookProject == "sun" && $info->translator == $memberID) {
                            $events = $this->_model->getMemberEventsForSun(
                                $memberID,
                                $eventID,
                                null,
                                $chapter
                            );

                            foreach ($events as $event) {
                                if ($event->step == EventSteps::THEO_CHECK) {
                                    if ($chapter == $event->currentChapter) {
                                        $canKeyword = true;
                                        break;
                                    }
                                }
                            }
                        } elseif ($info->translator == $memberID) {
                            $events = $this->_model->getMemberEventsForChecker($memberID, $eventID, null, $chapter);
                            foreach ($events as $event) {
                                if ($event->step == EventSteps::KEYWORD_CHECK) {
                                    $canKeyword = true;
                                    break;
                                }
                            }
                        } elseif ($info->l2checker == $memberID) {
                            $canCreate = false;
                            $events = $this->_model->getMemberEventsForCheckerL2(
                                $memberID,
                                $eventID,
                                null,
                                $chapter
                            );
                            foreach ($events as $event) {
                                if ($event->step == EventCheckSteps::KEYWORD_CHECK_L2
                                    || $event->step == EventCheckSteps::PEER_REVIEW_L2) {
                                    if ($chapter == $event->currentChapter) {
                                        $canKeyword = true;
                                        break;
                                    }
                                }
                            }
                        } elseif (Session::get("isSuperAdmin")) {
                            $canKeyword = true;
                        }
                    } else {
                        $events = $this->_model->getMemberEventsForNotes(
                            $memberID,
                            $eventID,
                            null,
                            $chapter
                        );

                        foreach ($events as $event) {
                            if ($event->step == EventSteps::HIGHLIGHT) {
                                if ($chapter == $event->currentChapter) {
                                    $canKeyword = true;
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($canKeyword) {
                    $result = null;

                    $keyword = $this->_translationModel->getKeywords([
                        "eventID" => $eventID,
                        "chapter" => $chapter,
                        "chunk" => $chunk,
                        "verse" => $verse,
                        "indexOrder" => $index,
                        "text" => $text
                    ]);

                    if (!empty($keyword)) {
                        if ($remove) {
                            $response["type"] = "remove";
                            $result = $this->_translationModel->deleteKeyword($keyword[0]->kID);
                        } else {
                            $response["error"] = __("keyword_exists_error");
                            echo json_encode($response);
                            return;
                        }
                    } else {
                        if ($canCreate) {
                            $postdata = [
                                "eventID" => $eventID,
                                "chapter" => $chapter,
                                "chunk" => $chunk,
                                "verse" => $verse,
                                "indexOrder" => $index,
                                "text" => $text,
                                "memberID" => Session::get("memberID")
                            ];

                            $response["type"] = "add";
                            $result = $this->_translationModel->createKeyword($postdata);
                        }
                    }

                    if ($result) {
                        $response["success"] = true;
                        $response["text"] = $text;
                    }
                }
            }
        }

        echo json_encode($response);
    }

    /**
     * Make member a level 1 checker, who picks from notification area
     * @param $eventID
     * @param $memberID
     * @param $step
     * @return mixed
     */
    public function applyChecker($eventID, $memberID, $chapter, $step)
    {
        $canApply = false;
        $notif = null;

        foreach ($this->_notifications as $notification) {
            if ($eventID == $notification->eventID
                && $memberID == $notification->memberID
                && $chapter == $notification->currentChapter
                && $step == $notification->step) {
                if ($notification->checkerID == 0) {
                    $canApply = true;
                    $notif = $notification;
                    break;
                }
            }
        }

        if ($canApply && $notif) {
            $postData = [];
            switch ($step) {
                case EventSteps::PEER_REVIEW:
                    $peerCheck = (array)json_decode($notif->peerCheck, true);
                    if (isset($peerCheck[$chapter])) {
                        $peerCheck[$chapter] = ["memberID" => Session::get("memberID"), "done" => 0];
                    }
                    $postData["peerCheck"] = json_encode($peerCheck);
                    break;
                case EventSteps::KEYWORD_CHECK:
                    $kwCheck = (array)json_decode($notif->kwCheck, true);
                    if (isset($kwCheck[$chapter])) {
                        $kwCheck[$chapter] = ["memberID" => Session::get("memberID"), "done" => 0];
                    }
                    $postData["kwCheck"] = json_encode($kwCheck);
                case EventSteps::CONTENT_REVIEW:
                    $crCheck = (array)json_decode($notif->crCheck, true);
                    if (isset($crCheck[$chapter])) {
                        $crCheck[$chapter] = ["memberID" => Session::get("memberID"), "done" => 0];
                    }
                    $postData["crCheck"] = json_encode($crCheck);
            }
            $this->_model->updateTranslator($postData, array("eventID" => $eventID, "memberID" => $memberID));
            Url::redirect('events/checker/' . $eventID . '/' . $memberID . '/' . $chapter);
        } else {
            $error[] = __("cannot_apply_checker");
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;

        return View::make("Events/CheckerApply")
            ->shares("title", __("apply_checker_l1"))
            ->shares("data", $data)
            ->shares("error", @$error);
    }

    /**
     * Make member a checker for Notes, who picks from notification area
     * @param $eventID
     * @param $memberID
     * @param $chapter
     * @return mixed
     */
    public function applyCheckerOther($bookProject, $eventID, $memberID, $chapter)
    {
        $canApply = false;
        $notif = null;

        foreach ($this->_notifications as $notification) {
            if ($bookProject == $notification->bookProject
                && $eventID == $notification->eventID
                && $memberID == $notification->memberID
                && $chapter == $notification->currentChapter) {
                $postdata = [];

                if ($notification->peer == 1) {
                    $otherCheck = (array)json_decode($notification->otherCheck, true);
                    if (isset($otherCheck[$chapter]) && $otherCheck[$chapter]["memberID"] == 0) {
                        $otherCheck[$chapter]["memberID"] = Session::get("memberID");
                        $notification->otherCheck = json_encode($otherCheck);
                        $notif = $notification;

                        $postdata = ["otherCheck" => $notif->otherCheck];
                        $canApply = true;
                    }
                } else {
                    $peerCheck = (array)json_decode($notification->peerCheck, true);
                    if (isset($peerCheck[$chapter]) && $peerCheck[$chapter]["memberID"] == 0) {
                        $peerCheck[$chapter]["memberID"] = Session::get("memberID");
                        $notification->peerCheck = json_encode($peerCheck);
                        $notif = $notification;

                        $postdata = ["peerCheck" => $notif->peerCheck];
                        $canApply = true;
                    }
                }
            }
        }

        if ($canApply && $notif !== null) {
            $this->_model->updateTranslator(
                $postdata,
                array(
                    "eventID" => $eventID,
                    "memberID" => $memberID));

            Url::redirect('events/checker-' . $bookProject . '/' . $eventID . '/' . $memberID . '/' . $chapter);
            exit;
        } else {
            $error[] = __("cannot_apply_checker");
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;

        return View::make("Events/CheckerApply")
            ->shares("title", __("apply_checker_l1"))
            ->shares("data", $data)
            ->shares("error", @$error);
        exit;
    }

    /**
     * Make member a level 2 checker, who picks from notification area
     * @param $eventID
     * @param $memberID
     * @param $step
     * @param $chapter
     * @return mixed
     */
    public function applyCheckerL2L3($eventID, $memberID, $step, $chapter)
    {
        $canApply = false;
        $notif = null;

        foreach ($this->_notifications as $notification) {
            if ($eventID == $notification->eventID
                && $memberID == $notification->memberID
                && $step == $notification->step
                && $chapter == $notification->currentChapter) {
                if ($step == EventCheckSteps::SND_CHECK) {
                    $sndCheck = (array)json_decode($notification->sndCheck, true);
                    if (isset($sndCheck[$chapter]) && $sndCheck[$chapter]["memberID"] == 0) {
                        $sndCheck[$chapter]["memberID"] = Session::get("memberID");
                        $notification->sndCheck = json_encode($sndCheck);
                        $notif = $notification;
                        $canApply = true;
                    }
                } elseif ($step == EventCheckSteps::PEER_REVIEW_L2) {
                    $peer1Check = (array)json_decode($notification->peer1Check, true);
                    $peer2Check = (array)json_decode($notification->peer2Check, true);
                    if (isset($peer1Check[$chapter])) {
                        if ($peer1Check[$chapter]["memberID"] == 0) {
                            $peer1Check[$chapter]["memberID"] = Session::get("memberID");
                            $notification->peer1Check = json_encode($peer1Check);
                            $notif = $notification;
                            $canApply = true;
                        } else if ($peer2Check[$chapter]["memberID"] == 0) {
                            $peer2Check[$chapter]["memberID"] = Session::get("memberID");
                            $notification->peer2Check = json_encode($peer2Check);
                            $notif = $notification;
                            $canApply = true;
                        }
                    }
                } elseif ($step == EventSteps::KEYWORD_CHECK) {
                    $kwCheck = (array)json_decode($notification->kwCheck, true);
                    if (isset($kwCheck[$chapter]) && $kwCheck[$chapter]["memberID"] == 0) {
                        $kwCheck[$chapter]["memberID"] = Session::get("memberID");
                        $notification->kwCheck = json_encode($kwCheck);
                        $notif = $notification;
                        $canApply = true;
                    }
                } elseif ($step == EventSteps::CONTENT_REVIEW) {
                    $crCheck = (array)json_decode($notification->crCheck, true);
                    if (isset($crCheck[$chapter]) && $crCheck[$chapter]["memberID"] == 0) {
                        $crCheck[$chapter]["memberID"] = Session::get("memberID");
                        $notification->crCheck = json_encode($crCheck);
                        $notif = $notification;
                        $canApply = true;
                    }
                } elseif ($step == EventCheckSteps::PEER_REVIEW_L3) {
                    $peerCheck = (array)json_decode($notification->peerCheck, true);
                    if (isset($peerCheck[$chapter]) && $peerCheck[$chapter]["memberID"] == 0) {
                        $peerCheck[$chapter]["memberID"] = Session::get("memberID");
                        $notification->peerCheck = json_encode($peerCheck);
                        $notif = $notification;
                        $canApply = true;
                    }
                }
            }
        }

        if ($canApply && $notif) {
            if ($notif->manageMode == "l2") {
                $postdata = [
                    "sndCheck" => $notif->sndCheck,
                    "peer1Check" => $notif->peer1Check,
                    "peer2Check" => $notif->peer2Check,
                ];
                $this->_model->updateL2Checker($postdata, [
                    "eventID" => $eventID,
                    "memberID" => $memberID
                ]);

                Url::redirect('events/checker' .
                    (!in_array($notif->bookProject, ["ulb", "udb"])
                        ? "-" . $notif->bookProject : "") . '-l2/' . $eventID . '/' . $memberID . '/' . $chapter);
            } elseif ($notif->manageMode == "l3") {
                $postdata = [
                    "peerCheck" => $notif->peerCheck,
                ];
                $this->_model->updateL3Checker($postdata, [
                    "eventID" => $eventID,
                    "memberID" => $memberID
                ]);

                Url::redirect('events/checker' .
                    (!in_array($notif->bookProject, ["ulb", "udb"])
                        ? "-" . $notif->bookProject : "") . '-l3/' . $eventID . '/' . $memberID . '/' . $chapter);
            }
        } else {
            $error[] = __("cannot_apply_checker");
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;

        return View::make("Events/CheckerApply")
            ->shares("title", __("apply_checker_l1"))
            ->shares("data", $data)
            ->shares("error", @$error);
    }

    /**
     * Make member a SUN checker, who picks from notification area
     * @param $eventID
     * @param $memberID
     * @param $step
     * @param $chapter
     * @return mixed
     */
    public function applyCheckerSun($eventID, $memberID, $step, $chapter)
    {
        $canApply = false;
        $notif = null;

        foreach ($this->_notifications as $notification) {
            if ($eventID == $notification->eventID
                && $memberID == $notification->memberID
                && $step == $notification->step
                && $chapter == $notification->currentChapter) {
                if ($step == EventSteps::THEO_CHECK) {
                    $kwCheck = (array)json_decode($notification->kwCheck, true);
                    if (isset($kwCheck[$chapter]) && $kwCheck[$chapter]["memberID"] == 0) {
                        $kwCheck[$chapter]["memberID"] = Session::get("memberID");
                        $notification->kwCheck = json_encode($kwCheck);
                        $notif = $notification;
                        $canApply = true;
                    }
                } elseif ($step == EventSteps::CONTENT_REVIEW) {
                    $crCheck = (array)json_decode($notification->crCheck, true);
                    if (isset($crCheck[$chapter]) && $crCheck[$chapter]["memberID"] == 0) {
                        $crCheck[$chapter]["memberID"] = Session::get("memberID");
                        $notification->crCheck = json_encode($crCheck);
                        $notif = $notification;
                        $canApply = true;
                    }
                }
            }
        }

        if ($canApply && $notif) {
            $postdata = [
                "kwCheck" => $notif->kwCheck,
                "crCheck" => $notif->crCheck,
            ];
            $this->_model->updateTranslator($postdata, [
                "eventID" => $eventID,
                "memberID" => $memberID
            ]);

            Url::redirect('events/checker' . ($notif->sourceBible == "odb" ? "-odb" : "") . '-sun/' . $eventID . '/' . $memberID . '/' . $chapter);
        } else {
            $error[] = __("cannot_apply_checker");
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;

        return View::make("Events/CheckerApply")
            ->shares("title", __("apply_checker_sun"))
            ->shares("data", $data)
            ->shares("error", @$error);
    }

    /**
     * Make member a RADIO checker, who picks from notification area
     * @param $eventID
     * @param $memberID
     * @param $step
     * @param $chapter
     * @return mixed
     */
    public function applyCheckerRadio($eventID, $memberID, $step, $chapter)
    {
        $canApply = false;
        $notif = null;

        foreach ($this->_notifications as $notification) {
            if ($eventID == $notification->eventID
                && $memberID == $notification->memberID
                && $step == $notification->step
                && $chapter == $notification->currentChapter) {
                if ($step == EventSteps::PEER_REVIEW) {
                    $peerCheck = (array)json_decode($notification->peerCheck, true);
                    if (isset($peerCheck[$chapter]) && $peerCheck[$chapter]["memberID"] == 0) {
                        $peerCheck[$chapter]["memberID"] = Session::get("memberID");
                        $notification->peerCheck = json_encode($peerCheck);
                        $notif = $notification;
                        $canApply = true;
                    }
                }
            }
        }

        if ($canApply && $notif) {
            $postdata = [
                "peerCheck" => $notif->peerCheck
            ];
            $this->_model->updateTranslator($postdata, [
                "eventID" => $eventID,
                "memberID" => $memberID
            ]);

            Url::redirect('events/checker' . '-rad/' . $eventID . '/' . $memberID . '/' . $chapter);
        } else {
            $error[] = __("cannot_apply_checker");
        }

        $data["menu"] = 1;
        $data["notifications"] = $this->_notifications;

        return View::make("Events/CheckerApply")
            ->shares("title", __("apply_checker_sun"))
            ->shares("data", $data)
            ->shares("error", @$error);
    }

    /**
     * Make member a verbalize checker
     */
    public function applyVerbChecker()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && $_POST["eventID"] != "" ? (integer)$_POST["eventID"] : null;
        $chkID = isset($_POST["chkID"]) && $_POST["chkID"] != "" ? (integer)$_POST["chkID"] : null;
        $chkName = isset($_POST["chkName"]) && preg_match("/^[^0-9!@#\$%\^&\*\(\)_\-\+=\.,\?\/\\\[\]\{\}\|\"]+$/", $_POST["chkName"]) ? trim($_POST["chkName"]) : null;
        $memberID = Session::get("memberID");

        if ($eventID !== null && ($chkID != null || $chkName != null)) {
            $event = $this->_model->getMemberEvents($memberID, $eventID);
            if ($chkID != null) {
                $chkMember = $this->_membersModel->getMembers([$chkID]);
                if (!empty($chkMember))
                    $chkName = $chkMember[0]->firstName . " " . mb_substr($chkMember[0]->lastName, 0, 1) . ".";
                else {
                    $chkID = null;
                    $chkName = null;
                }
            }

            if (!empty($event) && $chkName != null) {
                $verbCheck = (array)json_decode($event[0]->verbCheck, true);
                $checker = $chkID != null ? $chkID : $chkName;

                if ($event[0]->step == EventSteps::VERBALIZE && !array_key_exists($event[0]->currentChapter, $verbCheck)) {
                    $verbCheck[$event[0]->currentChapter] = ["memberID" => $checker, "done" => 1];
                    $postdata["verbCheck"] = json_encode($verbCheck);

                    $upd = $this->_model->updateTranslator($postdata, array("eventID" => $eventID, "memberID" => $memberID));
                    if ($upd) {
                        $response["success"] = true;
                        $response["chkName"] = $chkName;
                    } else {
                        $response["error"] = "not_saved";
                    }
                } else {
                    $response["error"] = "wrong_step";
                }
            } else {
                $response["error"] = "wrong_event_or_member";
            }
        } else {
            $response["error"] = "forbidden_name_format";
        }

        echo json_encode($response);
    }

    public function checkEvent()
    {
        $response = array("success" => false);

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST["eventID"]) && $_POST["eventID"] != "" ? (integer)$_POST["eventID"] : null;

        $event = $this->eventRepo->get($eventID);

        if ($event && $event->translators->contains(Session::get("memberID")) && $event->state != 'started') {
            $response["success"] = true;
        }

        echo json_encode($response);
    }

    /**
     * Get notifications for user
     */
    public function getNotifications()
    {
        $data["notifs"] = array();

        if (!empty($this->_notifications)) {
            foreach ($this->_notifications as $notification) {
                $text_data = [
                    "name" => $notification->firstName . " " . mb_substr($notification->lastName, 0, 1) . ".",
                    "step" => ($notification->step != "other" ? "(" . __($notification->step .
                            (in_array($notification->bookProject, ["tq","tw","obs"])
                            && $notification->step == EventSteps::PEER_REVIEW
                                ? "_" . $notification->bookProject : ($notification->sourceBible == "odb"
                                    ? "_odb"
                                    : (isset($notification->manageMode) && $notification->manageMode == "l2" && $notification->bookProject == "sun" ? "_sun" : "")))) . ")" : ""),
                    "book" => $notification->bookName,
                    "chapter" => ($notification->bookProject == "tw"
                        ? $notification->group
                        : ($notification->currentChapter == 0
                            ? __("intro")
                            : $notification->currentChapter)),
                    "language" => $notification->tLang,
                    "project" => ($notification->sourceBible == "odb"
                        ? __($notification->sourceBible)
                        : $notification->bookProject)
                ];

                if ($notification->bookProject == "tw")
                    $text = __('checker_apply_tw', $text_data) . (
                        $notification->manageMode != "l3"
                            ? " (" . ($notification->step == "other" ? "#1" : "#2") . ")" : ""
                        );
                else
                    $text = __('checker_apply', $text_data) . (
                        in_array($notification->bookProject, ["tn","tq","obs"]) && $notification->manageMode != "l3"
                            ? " (" . ($notification->step == "other" ? "#1" : "#2") . ")" : ""
                        );

                $note["link"] = "/events/checker" . (isset($notification->manageMode)
                    && in_array($notification->manageMode, ["sun","tn","tq","tw","rad","obs"]) ? "-" . $notification->manageMode : "")
                    . "/" . $notification->eventID . "/"
                    . $notification->memberID . "/"
                    . (!isset($notification->manageMode) && in_array($notification->bookProject, ["ulb", "udb"]) ? $notification->currentChapter . "/" : "")
                    . $notification->step . "/"
                    . (isset($notification->manageMode) ? $notification->currentChapter . "/" : "")
                    . "apply";

                $note["anchor"] = "check:" . $notification->eventID . ":" . $notification->memberID;
                $note["text"] = $text;
                $note["step"] = $notification->step;

                $data["notifs"][] = $note;
            }
        } else {
            $data["noNotifs"] = __("no_notifs_msg");
        }

        $data["success"] = true;
        echo json_encode($data);
    }


    //-------------------- Functions --------------------------//

    /**
     * Get Scripture source text for chapter or chunk
     * @param $data
     * @param bool $getChunk
     * @return array
     */
    private function getScriptureSourceText($data, $getChunk = false)
    {
        $currentChapter = $data["event"][0]->currentChapter;
        $currentChunk = $data["event"][0]->state == EventStates::TRANSLATING
            ? $data["event"][0]->currentChunk : 0;

        $initChapter = $data["event"][0]->bookProject != "tn" ? 0 : -1;
        $currentChunkText = [];
        $chunks = json_decode($data["event"][0]->chunks, true);
        $data["chunks"] = $chunks;

        if ($currentChapter == $initChapter) {
            $level = "l1";
            if ($data["event"][0]->state == EventStates::L2_CHECK) {
                $level = "l2";
                $memberID = $data["event"][0]->memberID;
            } elseif ($data["event"][0]->state == EventStates::L3_CHECK) {
                $level = "l3";
                $memberID = $data["event"][0]->memberID;
            } else {
                $memberID = $data["event"][0]->myMemberID;
            }

            $nextChapter = $this->_model->getNextChapter(
                $data["event"][0]->eventID,
                $memberID,
                $level);
            if (!empty($nextChapter))
                $currentChapter = $nextChapter[0]->chapter;
        }

        if ($currentChapter <= $initChapter) [];

        $usfm = $this->resourcesRepo->getScripture(
            $data["event"][0]->sourceLangID,
            $data["event"][0]->sourceBible,
            $data["event"][0]->bookCode,
            $data["event"][0]->sort,
            $currentChapter
        );

        if (!empty($usfm)) {
            $data["text"] = $usfm["text"];
            $data["totalVerses"] = $usfm["totalVerses"];
            $data["currentChapter"] = $currentChapter;
            $data["currentChunk"] = $currentChunk;

            if ($getChunk) {
                $chapData = $chunks;
                $chunk = $chapData[$currentChunk];
                $fv = $chunk[0];
                $lv = $chunk[sizeof($chunk) - 1];

                $data["no_chunk_source"] = true;

                foreach ($data["text"] as $verse => $text) {
                    $v = explode("-", $verse);
                    $map = array_map(function ($value) use ($fv, $lv) {
                        return $value >= $fv && $value <= $lv;
                    }, $v);
                    $map = array_unique($map);

                    if ($map[0]) {
                        $currentChunkText[$verse] = $text;
                        $data["no_chunk_source"] = false;
                    }
                }

                $data["chunks"] = $chapData;
                $data["chunk"] = $chunk;
                $data["totalVerses"] = sizeof($chunk);

                $data["text"] = $currentChunkText;
            }

            return $data;
        } else {
            return array("error" => __("no_source_error"));
        }
    }

    public function getNotesSourceText($data, $getChunk = false)
    {
        $currentChapter = $data["event"][0]->currentChapter;
        $currentChunk = $data["event"][0]->currentChunk;

        if ($currentChapter == -1) {
            $nextChapter = $this->_model->getNextChapter($data["event"][0]->eventID, $data["event"][0]->myMemberID);
            if (!empty($nextChapter))
                $currentChapter = $nextChapter[0]->chapter;
        }

        if ($currentChapter <= -1) return false;

        $notes = $this->resourcesRepo->getMdResource(
            $data["event"][0]->resLangID,
            $data["event"][0]->bookProject,
            $data["event"][0]->bookCode,
            $currentChapter,
            true
        );

        if (!empty($notes)) {
            $data["notes"] = $notes;
            $data["currentChapter"] = $currentChapter;
            $data["currentChunk"] = $currentChunk;

            $chunks = json_decode($data["event"][0]->chunks, true);
            $data["chunks"] = $chunks;

            if ($currentChapter > 0) {
                if (isset($data["text"]) && $data["text"] != "") {
                    $data["nosource"] = false;
                } else {
                    $data["no_chunk_source"] = true;
                    $data["nosource"] = true;
                }
            } else {
                $data["nosource"] = true;
            }

            if ($getChunk) {
                $data["notes"] = [];

                if (isset($data["chunk"])) {
                    foreach ($data["chunk"] as $verse) {
                        foreach ($notes[$verse] as $note) {
                            $data["notes"][] = $note;
                        }
                        break;
                    }
                } else {
                    $data["notes"] = $notes[$currentChunk];
                    $data["chunk"][0] = $currentChunk;
                }
            }

            return $data;
        } else {
            return array("error" => __("no_source_error"));
        }
    }

    private function getQuestionsSourceText($data)
    {
        $currentChapter = $data["event"][0]->currentChapter;
        $currentChunk = $data["event"][0]->currentChunk;

        if ($currentChapter == 0) {
            $nextChapter = $this->_model->getNextChapter($data["event"][0]->eventID, $data["event"][0]->myMemberID);
            if (!empty($nextChapter))
                $currentChapter = $nextChapter[0]->chapter;
        }

        if ($currentChapter <= 0) return false;

        $questions = $this->resourcesRepo->getMdResource(
            $data["event"][0]->resLangID,
            $data["event"][0]->bookProject,
            $data["event"][0]->bookCode,
            $currentChapter,
            true
        );

        if (!empty($questions)) {
            $data["questions"] = $questions;
            $data["currentChapter"] = $currentChapter;
            $data["currentChunk"] = $currentChunk;

            end($data["questions"]);
            $data["totalVerses"] = key($data["questions"]);

            $chunks = json_decode($data["event"][0]->chunks, true);
            $data["chunks"] = $chunks;

            $data["nosource"] = false;

            return $data;
        } else {
            return array("error" => __("no_source_error"));
        }
    }

    public function getOtherSourceText($data, $getChunk = false)
    {
        $currentChapter = $data["event"][0]->currentChapter;
        $currentChunk = $data["event"][0]->state == EventStates::TRANSLATING
            ? $data["event"][0]->currentChunk : 0;

        $source = $this->resourcesRepo->getOtherResource(
            $data["event"][0]->sourceLangID,
            $data["event"][0]->sourceBible,
            $data["event"][0]->bookCode
        );

        if (!empty($source)) {
            $initChapter = 0;
            $currentChunkText = [];
            $chunks = json_decode($data["event"][0]->chunks, true);
            $data["chunks"] = $chunks;

            if ($currentChapter == $initChapter) {
                $memberID = $data["event"][0]->myMemberID;

                $nextChapter = $this->_model->getNextChapter(
                    $data["event"][0]->eventID,
                    $memberID);
                if (!empty($nextChapter))
                    $currentChapter = $nextChapter[0]->chapter;
            }

            if ($currentChapter <= $initChapter) return false;

            if (!isset($source["chapters"][$currentChapter])) {
                return array("error" => __("no_source_error"));
            }

            $data["text"] = $source["chapters"][$currentChapter];

            $lastVerse = sizeof($data["text"]);
            $data["totalVerses"] = $lastVerse;
            $data["currentChapter"] = $currentChapter;
            $data["currentChunk"] = $currentChunk;

            $data["chapters"] = [];
            for ($i = 1; $i <= sizeof($source["chapters"]); $i++) {
                $data["chapters"][$i] = [];
            }

            $chapters = $this->_model->getChapters($data["event"][0]->eventID);

            foreach ($chapters as $chapter) {
                $tmp["trID"] = $chapter["trID"];
                $tmp["memberID"] = $chapter["memberID"];
                $tmp["chunks"] = json_decode($chapter["chunks"], true);
                $tmp["done"] = $chapter["done"];

                $data["chapters"][$chapter["chapter"]] = $tmp;
            }

            if ($getChunk) {
                $chapData = $chunks;
                $chunk = $chapData[$currentChunk];
                $fv = $chunk[0];
                $lv = $chunk[sizeof($chunk) - 1];

                $data["no_chunk_source"] = true;

                if (isset($data["text"][$fv])) {
                    $currentChunkText[$fv] = $data["text"][$fv];
                    $data["no_chunk_source"] = false;
                }

                $data["chunks"] = $chapData;
                $data["chunk"] = $chunk;
                $data["totalVerses"] = sizeof($chunk);

                $data["text"] = $currentChunkText;
            }

            return $data;
        } else {
            return array("error" => __("no_source_error"));
        }
    }

    private function getWordsSourceText($data)
    {
        $currentChapter = $data["event"][0]->currentChapter;
        $currentChunk = $data["event"][0]->currentChunk;

        if ($currentChapter == 0) {
            $nextChapter = $this->_model->getNextChapter($data["event"][0]->eventID, $data["event"][0]->myMemberID);
            if (!empty($nextChapter))
                $currentChapter = $nextChapter[0]->chapter;
        }

        if ($currentChapter <= 0) return false;

        $words = $this->resourcesRepo->getTw(
            $data["event"][0]->resLangID,
            $data["event"][0]->name,
            $data["event"][0]->eventID,
            $currentChapter,
            true
        );

        if (!empty($words)) {
            $data["words"] = $words["words"];
            $data["group"] = $words["group"];
            $data["currentChapter"] = $currentChapter;
            $data["currentChunk"] = $currentChunk;

            $chunks = json_decode($data["event"][0]->chunks, true);
            $data["chunks"] = $chunks;

            $data["nosource"] = false;

            return $data;
        } else {
            return array("error" => __("no_source_error"));
        }
    }

    private function getComments($eventID, $chapter = null, $chunk = null)
    {
        $comments = $this->_translationModel->getCommentsByEvent($eventID, $chapter, $chunk);
        $commentsFinal = array();

        foreach ($comments as $comment) {
            $commentsFinal[$comment->chapter][$comment->chunk][] = $comment;
        }

        unset($comments);

        return $commentsFinal;
    }

    public function checkBookFinished($chapters, $chaptersNum, $other = false, $level = 1)
    {
        if (isset($chapters) && is_array($chapters) && !empty($chapters)) {
            $chaptersDone = 0;
            foreach ($chapters as $chapter) {
                $chk = $level == 3 ? "l3checked" : ($level == 2 ? "l2checked" : ($other ? "checked" : "done"));
                if (!empty($chapter) && $chapter[$chk])
                    $chaptersDone++;
            }

            if ($chaptersNum == $chaptersDone)
                return true;
        }

        return false;
    }

    public function getTq($bookCode, $chapter, $lang)
    {
        $data = [];
        $data["questions"] = $this->resourcesRepo->getMdResource(
            $lang,
            "tq",
            $bookCode,
            $chapter,
            true
        );

        $this->layout = "dummy";
        echo View::make("Events/Tools/Tq")
            ->shares("data", $data)
            ->renderContents();
    }

    public function getTn($bookCode, $chapter, $lang, $totalVerses)
    {
        $data = [];

        $data["notes"] = $this->resourcesRepo->getMdResource(
            $lang,
            "tn",
            $bookCode,
            $chapter,
            true
        );

        $data["totalVerses"] = $totalVerses;
        $data["notesVerses"] = $this->_apiModel->getNotesVerses($data);

        $this->layout = "dummy";
        echo View::make("Events/Tools/Tn")
            ->shares("data", $data)
            ->renderContents();
    }

    public function getTw($bookCode, $chapter, $lang)
    {
        $data = [];

        $data["keywords"] = $this->resourcesRepo->parseTwByBook(
            $lang,
            $bookCode,
            $chapter,
            true
        );

        $this->layout = "dummy";
        echo View::make("Events/Tools/Tw")
            ->shares("data", $data)
            ->renderContents();
    }

    public function getBc($bookCode, $chapter, $lang)
    {
        $data = [];

        $data["commentaries"] = $this->resourcesRepo->getBc(
            $lang,
            $bookCode,
            $chapter,
            true
        );

        $this->layout = "dummy";
        echo View::make("Events/Tools/Bc")
            ->shares("data", $data)
            ->renderContents();
    }

    public function getRubric($lang)
    {
        $data = [];
        $data["rubric"] = $this->resourcesRepo->getQaGuide($lang);

        $this->layout = "dummy";
        echo View::make("Events/Tools/Rubric")
            ->shares("data", $data)
            ->renderContents();
    }

    public function getSailDict()
    {
        $data = [];
        $data["saildict"] = $this->_saildictModel->getSunDictionary();

        $this->layout = "dummy";
        echo View::make("Events/Tools/SailDict")
            ->shares("data", $data)
            ->renderContents();
    }

    public function checkInternet()
    {
        return time();
    }

    private function makeTurnCredentials(): array
    {
        $turnSecret = $this->_membersModel->getTurnSecret();
        $turnUsername = (time() + 3600) . ":vmast";
        $turnPassword = "";

        if (!empty($turnSecret)) {
            if (($turnSecret[0]->expire - time()) < 0) {
                $pass = $this->_membersModel->generateStrongPassword(22);
                if ($this->_membersModel->updateTurnSecret(["value" => $pass, "expire" => time() + (30 * 24 * 3600)])) // Update turn secret each month
                {
                    $turnSecret[0]->value = $pass;
                }
            }

            $turnPassword = hash_hmac("sha1", $turnUsername, $turnSecret[0]->value, true);
        }

        $turn = [];
        $turn[] = $turnUsername;
        $turn[] = base64_encode($turnPassword);

        return $turn;
    }
}
