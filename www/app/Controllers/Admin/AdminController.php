<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Domain\EventContributors;
use App\Domain\ProjectContributors;
use App\Models\ApiModel;
use App\Models\EventsModel;
use App\Models\MembersModel;
use App\Models\NewsModel;
use App\Models\SailDictionaryModel;
use App\Models\TranslationsModel;
use App\Repositories\BookInfo\IBookInfoRepository;
use App\Repositories\Event\IEventRepository;
use App\Repositories\GatewayLanguage\IGatewayLanguageRepository;
use App\Repositories\Language\ILanguageRepository;
use App\Repositories\Member\IMemberRepository;
use App\Repositories\Project\IProjectRepository;
use App\Repositories\Source\ISourceRepository;
use Config\Config;
use Database\ORM\Collection;
use Database\QueryException;
use File;
use Helpers\Constants\EventMembers;
use Helpers\Constants\EventStates;
use Helpers\Constants\EventSteps;
use Helpers\Gump;
use Helpers\Password;
use Helpers\Session;
use Helpers\Url;
use Helpers\UsfmParser;
use Shared\Legacy\Error;
use Support\Facades\Cache;
use Support\Facades\Input;
use Support\Facades\View;
use \stdClass;

class AdminController extends Controller {

    private $_membersModel;
    private $_eventsModel;
    private $_apiModel;
    private $_translationModel;
    private $_saildictModel;
    private $_newsModel;
    protected $layout = "admin";

    protected $memberRepo = null;
    protected $glRepo = null;
    protected $projectRepo = null;
    protected $eventRepo = null;
    protected $languageRepo = null;
    protected $sourceRepo = null;
    protected $bookInfoRepo = null;

    private $_member;

    public function __construct(
        IMemberRepository $memberRepo,
        IGatewayLanguageRepository $glProjectRepo,
        IProjectRepository $projectRepo,
        IEventRepository $eventRepo,
        ILanguageRepository $languageRepo,
        ISourceRepository $sourceRepo,
        IBookInfoRepository $bookInfoRepo
    ) {
        parent::__construct();

        $this->memberRepo = $memberRepo;
        $this->glRepo = $glProjectRepo;
        $this->projectRepo = $projectRepo;
        $this->eventRepo = $eventRepo;
        $this->languageRepo = $languageRepo;
        $this->sourceRepo = $sourceRepo;
        $this->bookInfoRepo = $bookInfoRepo;

        if(Config::get("app.isMaintenance")
            && !in_array($_SERVER['REMOTE_ADDR'], Config::get("app.ips")))
        {
            Url::redirect("maintenance");
        }

        if (!Session::get('memberID')) {
            Session::set('redirect', 'admin');
            Url::redirect('members/login');
        }

        $this->_member = $this->memberRepo->get(Session::get('memberID'));

        if (!$this->_member) Url::redirect("members/login");

        $this->_membersModel = new MembersModel();
        $this->_eventsModel = new EventsModel($this->eventRepo);
        $this->_apiModel = new ApiModel();
        $this->_translationModel = new TranslationsModel();
        $this->_saildictModel = new SailDictionaryModel();
        $this->_newsModel = new NewsModel();
    }

    public function index() {

        if(!$this->_member->isSuperAdmin()
            && !$this->_member->isGlAdmin()
            && !$this->_member->isProjectAdmin()) {
            Url::redirect('');
        }

        $data['menu'] = 1;

        if ($this->_member->isSuperAdmin()) {
            $gatewayLanguages = $this->glRepo->all();
        } else {
            $gatewayLanguages = $this->_member->adminGatewayLanguages;
            foreach ($this->_member->adminProjects as $project) {
                if (!$gatewayLanguages->contains($project->gatewayLanguage))
                    $gatewayLanguages->add($project->gatewayLanguage);
            }
        }
        $gwLangs = $this->languageRepo->where("isGW", 1)->get();

        return View::make('Admin/Main/Index')
            ->shares("title", __("gateway_languages"))
            ->shares("gatewayLanguages", $gatewayLanguages)
            ->shares("gwLangs", $gwLangs)
            ->shares("data", $data);
    }


    public function gatewayLanguage($glID)
    {
        if(!$this->_member->isGlAdmin() && !$this->_member->isProjectAdmin()) {
            Url::redirect('');
        }

        $data['menu'] = 1;
        $gatewayLanguage = $this->glRepo->get($glID);

        $isGlAdmin = $gatewayLanguage->admins->contains($this->_member);
        $hasGlProjects = !$gatewayLanguage->projects->intersect($this->_member->adminProjects)->isEmpty();

        if (!$gatewayLanguage || (!$hasGlProjects && !$isGlAdmin)) {
            Url::redirect("/admin");
        }

        $gwLangs = $this->languageRepo->where("isGW", 1)->get();

        if ($isGlAdmin) {
            $projects = $gatewayLanguage->projects;
        } else {
            $projects = $this->_member->adminProjects->where("glID", $glID)->sort(function($a, $b) {
                return $a->targetLang >= $b->targetLang;
            });
        }

        $sources = $this->sourceRepo->all();

        return View::make('Admin/Main/GatewayLanguage')
            ->shares("title", __("admin_events_title"))
            ->shares("gatewayLanguage", $gatewayLanguage)
            ->shares("projects", $projects)
            ->shares("sources", $sources)
            ->shares("gwLangs", $gwLangs)
            ->shares("isGlAdmin", $isGlAdmin)
            ->shares("data", $data);
    }

    public function project($projectID)
    {
        if(!$this->_member->isGlAdmin() && !$this->_member->isProjectAdmin()) {
            Url::redirect('');
        }

        $data['menu'] = 1;
        $project = $this->projectRepo->get($projectID);

        $page = 'Admin/Main/Project';
        $category = 'bible';

        if($project)
        {
            if($project->bookProject == "tw")
            {
                $page = 'Admin/Main/ProjectTW';
                $category = 'tw';
            }
            elseif($project->bookProject == "rad")
            {
                $page = 'Admin/Main/ProjectRadio';
                $category = 'rad';
            }
            elseif ($project->sourceBible == "odb")
            {
                $page = 'Admin/Main/ProjectODB';
                $category = 'odb';
            }

            $otDone = 0;
            $ntDone = 0;
            $data["OTprogress"] = 0;
            $data["NTprogress"] = 0;

            $odbDone = 0;
            $data["ODBprogress"] = 0;

            $radDone = 0;
            $data["RADprogress"] = 0;

            $twDone = 0;
            $data["TWprogress"] = 0;

            $events = $project->events;

            $bookInfos = $this->bookInfoRepo->where("category", $category)->get();

            foreach ($bookInfos as $bookInfo) {
                foreach ($events as $event) {
                    if ($bookInfo->sort == $event->bookInfo->sort) {
                        $bookInfo->event = $event;
                    }
                }

                if ($bookInfo->event) {
                    if($bookInfo->category == "bible" && $bookInfo->sort < 41) // Old testament
                    {
                        if(EventStates::enum($bookInfo->event->state) >= EventStates::enum(EventStates::TRANSLATED))
                        {
                            $otDone++;
                        }
                    }
                    else if($bookInfo->category == "bible" && $bookInfo->sort >= 41) // New testament
                    {
                        if(EventStates::enum($bookInfo->event->state) >= EventStates::enum(EventStates::TRANSLATED))
                        {
                            $ntDone++;
                        }
                    }
                    else if($bookInfo->category == "tw") // tWords categories (kt, names, other)
                    {
                        if(EventStates::enum($bookInfo->event->state) >= EventStates::enum(EventStates::TRANSLATED))
                        {
                            $twDone++;
                        }
                    }
                    else if($bookInfo->category == "odb") // ODB books
                    {
                        if(EventStates::enum($bookInfo->event->state) >= EventStates::enum(EventStates::TRANSLATED))
                        {
                            $odbDone++;
                        }
                    }
                    else if($bookInfo->category == "rad") // RADIO books
                    {
                        if(EventStates::enum($bookInfo->event->state) >= EventStates::enum(EventStates::TRANSLATED))
                        {
                            $radDone++;
                        }
                    }
                }
            }

            $data["OTprogress"] = 100*$otDone/39;
            $data["NTprogress"] = 100*$ntDone/27;
            $data["TWprogress"] = 100*$twDone/3;

            if($project->sourceBible == "odb")
            {
                $count = $this->bookInfoRepo->all()->where("category", "odb", false)->count();
                if($count > 0)
                    $data["ODBprogress"] = 100*$odbDone/$count;
            }
            elseif ($project->bookProject == "rad")
            {
                $count = $this->bookInfoRepo->all()->where("category", "rad", false)->count();
                if($count > 0)
                    $data["RADprogress"] = 100*$radDone/$count;
            }
        }

        return View::make($page)
            ->shares("title", __("admin_events_title"))
            ->shares("project", $project)
            ->shares("bookInfos", $bookInfos)
            ->shares("data", $data);
    }

    public function import()
    {
        $response = [
            "success" => false,
            "error" => __("unknown_import_type_error")
        ];

        if(!$this->_member->isGlAdmin() && $this->_member->isProjectAdmin()) {
            $response["error"] = __("not_enough_rights_error");
            echo json_encode($response);
            return;
        }

        $_POST = Gump::xss_clean($_POST);
        $_FILES = Gump::xss_clean($_FILES);

        $import = isset($_FILES['import']) && $_FILES['import'] != "" ? $_FILES['import']
            : (isset($_POST['import']) && $_POST['import'] != "" ? $_POST['import'] : null);
        $type = isset($_POST['type']) && $_POST['type'] != "" ? $_POST['type'] : "dcs";
        $projectID = isset($_POST['projectID']) && $_POST['projectID'] != "" ? (integer)$_POST['projectID'] : 0;
        $eventID = isset($_POST['eventID']) && $_POST['eventID'] != "" ? (integer)$_POST['eventID'] : 0;
        $bookCode = isset($_POST['bookCode']) && $_POST['bookCode'] != "" ? $_POST['bookCode'] : null;
        $bookProject = isset($_POST['bookProject']) && $_POST['bookProject'] != "" ? $_POST['bookProject'] : null;
        $importLevel = isset($_POST['importLevel']) && $_POST['importLevel'] != "" ? (integer)$_POST['importLevel'] : 1;
        $importProject = isset($_POST['importProject']) && $_POST['importProject'] != "" ? $_POST['importProject'] : null;

        if($import !== null && $bookCode != null && $bookProject != null && $importProject != null) {
            switch ($type) {
                case "dcs":
                    $path = $this->_apiModel->processDCSUrl($import);

                    switch ($importProject) {
                        case "ulb":
                        case "udb":
                            $usfm = $this->_apiModel->compileUSFMProject($path);

                            if($usfm != null)
                            {
                                $response = $this->importScriptureToEvent($usfm, $projectID, $eventID, $bookCode, $importLevel);
                            }
                            break;
                        case "tn":
                            $tn = $this->_apiModel->getTranslationNotes($bookCode, null, false, $path);
                            if(!empty($tn))
                            {
                                $response = $this->importResourceToEvent($tn, $projectID, $eventID, $bookCode, $importLevel);
                            }
                            else
                            {
                                $response["error"] = __("usfm_not_valid_error");
                            }
                            break;
                        case "tq":
                            $tq = $this->_apiModel->getTranslationQuestions($bookCode, null, false, $path);
                            if(!empty($tq))
                            {
                                $response = $this->importResourceToEvent($tq, $projectID, $eventID, $bookCode, $importLevel);
                            }
                            else
                            {
                                $response["error"] = __("usfm_not_valid_error");
                            }
                            break;
                        case "tw":
                            $cat = $bookCode == "wkt" ? "kt" : ($bookCode == "wns" ? "names" : "other");
                            $tw = $this->_apiModel->getTranslationWordsByCategory($cat, null, false, false, $path);
                            $tw = array_chunk($tw, 5); // make groups of 5 words each

                            if(!empty($tw))
                            {
                                $response = $this->importResourceToEvent($tw, $projectID, $eventID, $bookCode, $importLevel);
                            }
                            else
                            {
                                $response["error"] = __("usfm_not_valid_error");
                            }
                            break;
                        default:
                            $response["error"] = __("not_implemented");
                    }
                    break;

                case "usfm":
                    if(File::extension($import["name"]) == "usfm" || File::extension($import["name"]) == "txt") {
                        $usfm = File::get($import["tmp_name"]);
                        $response = $this->importScriptureToEvent($usfm, $projectID, $eventID, $bookCode, $importLevel);
                    }
                    else
                    {
                        $response["error"] = __("usfm_not_valid_error");
                    }
                    break;

                case "ts":
                    if(File::extension($import["name"]) == "tstudio")
                    {
                        $path = $this->_apiModel->processZipFile($import);
                        if(in_array($bookProject, ["ulb","udb"]))
                        {
                            $usfm = $this->_apiModel->compileUSFMProject($path);

                            if($usfm != null)
                            {
                                $response = $this->importScriptureToEvent($usfm, $projectID, $eventID, $bookCode, $importLevel);
                            }
                            else
                            {
                                $response["error"] = __("usfm_not_valid_error");
                            }
                        }
                        elseif ($bookProject == "tn")
                        {
                            $tn = $this->_apiModel->getTranslationNotes($bookCode, null, false, $path);
                            if(!empty($tn))
                            {
                                $response = $this->importResourceToEvent($tn, $projectID, $eventID, $bookCode, $importLevel);
                            }
                            else
                            {
                                $response["error"] = __("usfm_not_valid_error");
                            }
                        }
                        elseif ($bookProject == "tq")
                        {
                            $tq = $this->_apiModel->getTranslationQuestions($bookCode, null, false, $path);
                            if(!empty($tq))
                            {
                                $response = $this->importResourceToEvent($tq, $projectID, $eventID, $bookCode, $importLevel);
                            }
                            else
                            {
                                $response["error"] = __("usfm_not_valid_error");
                            }
                        }
                        elseif ($bookProject == "tw")
                        {
                            $cat = $bookCode == "wkt" ? "kt" : ($bookCode == "wns" ? "names" : "other");
                            $tw = $this->_apiModel->getTranslationWordsByCategory($cat, null, false, false, $path);
                            $tw = array_chunk($tw, 5); // make groups of 5 words each

                            if(!empty($tw))
                            {
                                $response = $this->importResourceToEvent($tw, $projectID, $eventID, $bookCode, $importLevel);
                            }
                            else
                            {
                                $response["error"] = __("usfm_not_valid_error");
                            }
                        }
                        else
                        {
                            $response["error"] = __("not_implemented");
                        }
                    }
                    else
                    {
                        $response["error"] = __("usfm_not_valid_error");
                    }
                    break;

                case "zip":
                    if(File::extension($import["name"]) == "zip")
                    {
                        $path = $this->_apiModel->processZipFile($import);

                        switch ($importProject)
                        {
                            case "ulb":
                            case "udb":
                                $response["error"] = __("not_implemented");
                                break;
                            case "tn":
                                $tn = $this->_apiModel->getTranslationNotes($bookCode, null, false, $path);

                                if(!empty($tn))
                                {
                                    $response = $this->importResourceToEvent($tn, $projectID, $eventID, $bookCode, $importLevel);
                                }
                                else
                                {
                                    $response["error"] = __("usfm_not_valid_error");
                                }
                                break;
                            case "tq":
                                $tq = $this->_apiModel->getTranslationQuestions($bookCode, null, false, $path);

                                if(!empty($tq))
                                {
                                    $response = $this->importResourceToEvent($tq, $projectID, $eventID, $bookCode, $importLevel);
                                }
                                else
                                {
                                    $response["error"] = __("usfm_not_valid_error");
                                }
                                break;
                            case "tw":
                                $cat = $bookCode == "wkt" ? "kt" : ($bookCode == "wns" ? "names" : "other");
                                $tw = $this->_apiModel->getTranslationWordsByCategory($cat, null, false, false, $path);
                                $tw = array_chunk($tw, 5); // make groups of 5 words each

                                if(!empty($tw))
                                {
                                    $response = $this->importResourceToEvent($tw, $projectID, $eventID, $bookCode, $importLevel);
                                }
                                else
                                {
                                    $response["error"] = __("usfm_not_valid_error");
                                }
                                break;
                            default:
                                $response["error"] = __("not_implemented");
                        }
                    }
                    else
                    {
                        $response["error"] = __("usfm_not_valid_error");
                    }
                    break;

                default:
                    $response["error"] = __("unknown_import_type_error");
                    break;
            }
        }
        else
        {
            $response["error"] = __('unknown_import_type_error');
        }

        echo json_encode($response);
    }

    public function repos_search($q)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://git.door43.org/api/v1/repos/search?limit=50&q=" . $q);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);

        if(curl_errno($ch))
        {
            return false;
        }

        curl_close($ch);

        echo $data;
    }

    public function members()
    {
        if(!$this->_member->isSuperAdmin()) Url::redirect('');

        $data['menu'] = 2;
        $data["languages"] = $this->_eventsModel->getAllLanguages();

        // New members
        $data["newMembers"] = $this->_membersModel->getMember(["*"], [
            ["active", false],
            ["verified", false, "=", "OR"]
        ], true);

        // All members
        $data["count"] = $this->_membersModel->searchMembers(null, "all", null, true);
        $data["members"] = $this->_membersModel->searchMembers(null, "all", null, false, true);

        // All members with their translation events
        $data["all_members"] = [];
        $list = $this->_eventsModel->getBooksOfTranslators();
        foreach($list as $item)
        {
            if(!isset($data["all_members"][$item->userName]))
            {
                $tmp = [];
                $tmp["firstName"] = $item->firstName;
                $tmp["lastName"] = $item->lastName;
                $data["all_members"][$item->userName] = $tmp;
            }

            if(!isset($data["all_members"][$item->userName]["books"]))
            {
                $tmp = [];
                $tmp["name"] = $item->name;
                $tmp["project"] = "(".$item->bookProject.") ".__($item->bookProject);
                $tmp["lang"] = "[".$item->targetLang."] ".$item->angName
                    .($item->angName != $item->langName ? " (".$item->langName.")" : "");
                $tmp["chapters"] = [];
                $data["all_members"][$item->userName]["books"][$item->code] = $tmp;
            }

            if(!isset($data["all_members"][$item->userName]["books"][$item->code]))
            {
                $tmp = [];
                $tmp["name"] = $item->name;
                $tmp["project"] = "(".$item->bookProject.") ".__($item->bookProject);
                $tmp["lang"] = "[".$item->targetLang."] ".$item->angName
                    .($item->angName != $item->langName ? " (".$item->langName.")" : "");
                $tmp["chapters"] = [];
                $data["all_members"][$item->userName]["books"][$item->code] = $tmp;
            }

            if(!isset($data["all_members"][$item->userName]["books"][$item->code]["chapters"]))
                $data["all_members"][$item->userName]["books"][$item->code]["chapters"] = [];

            $data["all_members"][$item->userName]["books"][$item->code]["chapters"][$item->chapter]["done"] = $item->done;
            $data["all_members"][$item->userName]["books"][$item->code]["chapters"][$item->chapter]["words"] = $item->words;
        }

        return View::make('Admin/Members/Index')
            ->shares("title", __("admin_members_title"))
            ->shares("data", $data);
    }

    public function toolsSource()
    {
        if(!$this->_member->isGlAdmin()) Url::redirect('/admin/tools/common');

        $data["menu"] = 3;

        $gwLangs = $this->_member->adminGatewayLanguages;

        $allowedLanguages = new Collection();
        foreach ($gwLangs as $gl) {
            $allowedLanguages = $allowedLanguages->merge($gl->language->targetLanguages);
        }

        $sources = $this->sourceRepo->all()->filter(function($item) use ($allowedLanguages) {
            return $allowedLanguages->contains($item->language);
        });
        $sourceTypes = $this->sourceRepo->all()->groupBy("slug")
            ->map(function($group) {
                return $group[0];
            });

        return View::make('Admin/Main/ToolsSource')
            ->shares("title", __("admin_tools_title"))
            ->shares("gwLangs", $gwLangs)
            ->shares("sources", $sources)
            ->shares("sourceTypes", $sourceTypes)
            ->shares("data", $data);
    }

    public function toolsCommon()
    {
        if(!$this->_member->isSuperAdmin()) Url::redirect('/admin');

        $data["menu"] = 3;

        $data["faqs"] = $this->_newsModel->getFaqs();

        return View::make('Admin/Main/ToolsCommon')
            ->shares("title", __("admin_tools_title"))
            ->shares("data", $data);
    }

    public function toolsVsun()
    {
        if(!$this->_member->isSuperAdmin()) Url::redirect('/admin');

        $data["menu"] = 3;

        $data["saildict"] = $this->_saildictModel->getSunDictionary();

        return View::make('Admin/Main/ToolsVsun')
            ->shares("title", __("admin_tools_title"))
            ->shares("data", $data);
    }

    public function toolsFaq()
    {
        if(!$this->_member->isSuperAdmin()) Url::redirect('/admin');

        $data["menu"] = 3;

        $data["faqs"] = $this->_newsModel->getFaqs();

        return View::make('Admin/Main/ToolsFaq')
            ->shares("title", __("admin_tools_title"))
            ->shares("data", $data);
    }

    public function toolsNews()
    {
        if(!$this->_member->isSuperAdmin()) Url::redirect('/admin');

        $data["menu"] = 3;

        return View::make('Admin/Main/ToolsNews')
            ->shares("title", __("admin_tools_title"))
            ->shares("data", $data);
    }

    /**
     * Get event information with facilitators list
     */
    public function getEvent()
    {
        $response = ["success" => false];

        if(!$this->_member->isGlAdmin() && $this->_member->isProjectAdmin()) {
            $response["error"] = "admin";
            echo json_encode($response);
            return;
        }

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST['eventID']) && $_POST['eventID'] != "" ? (integer)$_POST['eventID'] : null;

        if($eventID == null)
        {
            $response["error"] = __('wrong_parameters_error');
        }

        if(!isset($response["error"]))
        {
            $event = $this->eventRepo->get($eventID);

            if($event)
            {
                if (!$event->project->admins->contains($this->_member)
                    && !$event->project->gatewayLanguage->admins->contains($this->_member)) {
                    $response["error"] = "admin";
                    echo json_encode($response);
                    return;
                }

                $members = [];

                foreach ($event->admins as $member) {
                    $members[$member->memberID] = "{$member->firstName} "
                        .mb_substr($member->lastName, 0, 1)
                        .". ({$member->userName})";
                }

                if(in_array($event->project->bookProject, ["tn","tq","tw"]))
                {
                    $ulbEvent = $this->eventRepo->all()->filter(function($ev) use ($event) {
                        return $ev->bookCode == $event->bookCode
                            && $ev->project->targetLang == $event->project->targetLang
                            && $ev->project->bookProject == "ulb";
                    })->first();

                    if($ulbEvent)
                        $response["ulb"] = $ulbEvent;
                }

                $event->load('bookInfo');

                $response["success"] = true;
                $response["admins"] = $members;
                $response["event"] = $event;
            }
            else
            {
                $response["error"] = __('wrong_parameters_error');
            }
        }

        echo json_encode($response);

    }

    public function getProject()
    {
        $response = ["success" => false];

        if(!$this->_member->isGlAdmin()) {
            echo json_encode(array("login" => true));
            exit;
        }

        $_POST = Gump::xss_clean($_POST);

        $projectID = isset($_POST['projectID']) && $_POST['projectID'] != "" ? (integer)$_POST['projectID'] : null;

        if($projectID == null)
        {
            $response["error"] = __('wrong_parameters_error');
        }

        if(!isset($response["error"]))
        {
            $project = $this->projectRepo->get($projectID);
            $project->load("admins");

            if($project && $project->gatewayLanguage->admins->contains($this->_member))
            {
                $response["success"] = true;
                $response["project"] = $project;
            }
            else
            {
                $response["error"] = __('wrong_parameters_error');
            }
        }

        echo json_encode($response);
    }


    /**
     * Get event contributors (translators, facilitators, checkers) list
     */
    public function getEventContributors()
    {
        $response = ["success" => false];

        if(!$this->_member->isGlAdmin() && !$this->_member->isProjectAdmin()) {
            $response["error"] = "admin";
            echo json_encode($response);
            exit;
        }

        $_POST = Gump::xss_clean($_POST);

        $eventID = isset($_POST['eventID']) && $_POST['eventID'] != "" ? (integer)$_POST['eventID'] : null;
        $level = isset($_POST['level']) && $_POST['level'] != "" ? (integer)$_POST['level'] : 1;
        $mode = isset($_POST['mode']) && $_POST['mode'] != "" ? $_POST['mode'] : "ulb";

        $event = $this->eventRepo->get($eventID);

        if($event)
        {
            $eventContributors = new EventContributors($event, $level, $mode);
            $contributors = $eventContributors->get();

            if(!empty($contributors))
            {
                $response["success"] = true;
                $response = array_merge($response, $contributors);
            }
        } else {
            $response["error"] = __('wrong_parameters_error');
        }

        echo json_encode($response);
    }

    public function getProjectContributors() {
        $response = ["success" => false];

        if (!Session::get('memberID'))
        {
            $response["error"] = "login";
        }

        if(!Session::get('isSuperAdmin'))
        {
            $response["error"] = "admin";
        }

        $_POST = Gump::xss_clean($_POST);

        $projectID = isset($_POST['projectID']) && $_POST['projectID'] != "" ? (integer)$_POST['projectID'] : null;

        if($projectID == null)
        {
            $response["error"] = __('wrong_parameters_error');
        }

        if(!isset($response["error"]))
        {
            $project = $this->projectRepo->get($projectID);
            if ($project) {
                $projectContributors = new ProjectContributors($project);
                $response["contributors"] = $projectContributors->get();
            }
            $response["success"] = true;
        }

        echo json_encode($response);
    }

    public function createGatewayLanguage()
    {
        if(!$this->_member->isSuperAdmin()) return;

        $_POST = Gump::xss_clean($_POST);

        $gwLang = isset($_POST['gwLang']) && $_POST['gwLang'] != "" ? $_POST['gwLang'] : null;

        if($gwLang == null)
        {
            $error[] = __('choose_gateway_language_error');
        }

        if(!isset($error))
        {
            $gatewayLanguage = $this->glRepo->where("gwLang", $gwLang)->first();
            if($gatewayLanguage)
            {
                $error[] = __("gateway_language_exists_error");
                echo json_encode(array("error" => Error::display($error)));
                return;
            }

            $this->glRepo->create(["gwLang" => $gwLang]);
            echo json_encode(array("success" => __("successfully_created")));
        }
        else
        {
            echo json_encode(array("error" => Error::display($error)));
        }
    }

    public function getGlAdmins()
    {
        $response = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            echo json_encode(array());
            exit;
        }

        $_POST = Gump::xss_clean($_POST);

        $glID = isset($_POST['glID']) && $_POST['glID'] != "" ? (integer)$_POST['glID'] : 0;

        $gatewayLanguage = $this->glRepo->where("glID", $glID)->first();

        if($gatewayLanguage)
        {
            $members = [];
            foreach ($gatewayLanguage->admins as $member) {
                $members[$member->memberID] = "{$member->firstName} "
                    .mb_substr($member->lastName, 0, 1)
                    .". ({$member->userName})";
            }

            $response["success"] = true;
            $response["admins"] = $members;
        }
        else
        {
            $response["error"] = __("gateway_language_not_exist");
        }

        echo json_encode($response);
    }


    public function editGlAdmins()
    {
        $response = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            echo json_encode(array());
            exit;
        }

        $_POST = Gump::xss_clean($_POST);

        $glID = isset($_POST['glID']) && $_POST['glID'] != "" ? (integer)$_POST['glID'] : 0;
        $glAdmins = isset($_POST['gl_admins']) && !empty($_POST['gl_admins']) ? array_unique($_POST['gl_admins']) : [];

        $gatewayLanguage = $this->glRepo->where("glID", $glID)->first();

        $glAdmins = array_filter($glAdmins, function($elm) {
            return is_numeric($elm);
        });
        $glAdmins = array_values($glAdmins);

        $gatewayLanguage->admins()->sync($glAdmins);

        $response["success"] = true;

        echo json_encode($response);
    }

    public function createProject()
    {
        if(!$this->_member->isGlAdmin()) {
            echo json_encode(array("login" => true));
            exit;
        }

        $_POST = Gump::xss_clean($_POST);

        $projectMode = isset($_POST['projectMode']) && preg_match("/(bible|tn|tq|tw|odb|rad)/", $_POST['projectMode']) ? $_POST['projectMode'] : "bible";
        $subGwLangs = isset($_POST['subGwLangs']) && $_POST['subGwLangs'] != "" ? $_POST['subGwLangs'] : null;
        $targetLang = isset($_POST['targetLangs']) && $_POST['targetLangs'] != "" ? $_POST['targetLangs'] : null;
        $sourceTranslation = isset($_POST['sourceTranslation']) && $_POST['sourceTranslation'] != "" ? $_POST['sourceTranslation'] : null;
        $sourceTools = isset($_POST['sourceTools']) && $_POST['sourceTools'] != "" ? $_POST['sourceTools'] : null;
        $toolsTn = isset($_POST['toolsTn']) && $_POST['toolsTn'] != "" ? $_POST['toolsTn'] : null;
        $toolsTq = isset($_POST['toolsTq']) && $_POST['toolsTq'] != "" ? $_POST['toolsTq'] : null;
        $toolsTw = isset($_POST['toolsTw']) && $_POST['toolsTw'] != "" ? $_POST['toolsTw'] : null;
        $projectType = isset($_POST['projectType']) && $_POST['projectType'] != "" ? $_POST['projectType'] : null;
        $act = isset($_POST['act']) && $_POST['act'] != "" ? $_POST['act'] : "create";
        $projectID = isset($_POST['projectID']) && $_POST['projectID'] != "" ? $_POST['projectID'] : null;
        $admins = isset($_POST['project_admins']) && !empty($_POST['project_admins']) ? array_filter($_POST['project_admins']) : [];

        $admins = array_filter($admins, function($elm) {
            return is_numeric($elm);
        });
        $admins = array_values($admins);

        if($act == "create")
        {
            if(empty($admins))
            {
                $error[] = __('enter_project_admins');
            }

            if($subGwLangs == null)
            {
                $error[] = __('choose_gw_lang');
            }

            if($targetLang == null)
            {
                $error[] = __("choose_target_lang");
            }

            if($sourceTranslation == null)
            {
                if(!in_array($projectMode, ["tq","tw","odb","rad"]))
                    $error[] = __("choose_source_trans");
            }

            if($projectType == null)
            {
                if(!in_array($projectMode, ["tn","tq","tw","rad"]))
                    $error[] = __("choose_project_type");

                if($projectMode == "rad")
                {
                    $projectType = "rad";
                }
            }

            if(in_array($projectMode, ["tn","tq","tw"]) && $sourceTools == null)
            {
                $error[] = __("choose_source_".$projectMode);
            }

            if(in_array($projectMode, ["tq","tw"]))
            {
                $sourceTranslation = "ulb|en";
            }
            elseif($projectMode == "odb")
            {
                $sourceTranslation = "odb|en";
            }
            elseif($projectMode == "rad")
            {
                $sourceTranslation = "rad|en";
            }

            if(!isset($error))
            {
                $sourceTrPair = explode("|", $sourceTranslation);
                $gwLangsPair = explode("|", $subGwLangs);

                $gatewayLanguage = $this->_member->adminGatewayLanguages->where("glID", $gwLangsPair[1])->first();

                if(!$gatewayLanguage)
                {
                    $error[] = __("not_enough_rights_error");
                    echo json_encode(array("error" => Error::display($error)));
                    return;
                }

                $projType = in_array($projectMode, ['tn','tq','tw']) ? $projectMode : $projectType;

                $search = [
                    "gwLang" => $gwLangsPair[0],
                    "targetLang" => $targetLang,
                    "bookProject" => $projType
                ];

                if($projectMode == "odb")
                {
                    $search["sourceBible"] = "odb";
                }
                elseif ($projectMode == "rad")
                {
                    $search["sourceBible"] = "rad";
                }

                $project = $this->projectRepo->where($search)->first();

                if($project)
                {
                    $error[] = __("project_exists");
                    echo json_encode(array("error" => Error::display($error)));
                    return;
                }

                $postdata = array(
                    "gwLang" => $gwLangsPair[0],
                    "targetLang" => $targetLang,
                    "bookProject" => $projType,
                    "sourceBible" => $sourceTrPair[0],
                    "sourceLangID" => $sourceTrPair[1],
                    "resLangID" => $sourceTools
                );

                if($toolsTn)
                    $postdata["tnLangID"] = $toolsTn;
                if($toolsTq)
                    $postdata["tqLangID"] = $toolsTq;
                if($toolsTw)
                    $postdata["twLangID"] = $toolsTw;

                $project = $this->projectRepo->create($postdata, $gatewayLanguage);
                $project->admins()->sync($admins);

                echo json_encode(array("success" => __("successfully_created")));
            }
            else
            {
                echo json_encode(array("error" => Error::display($error)));
            }
        }
        elseif($act == "edit")
        {
            $project = $this->projectRepo->get($projectID);

            if(!$project)
            {
                $error[] = __("error_ocured");
                echo json_encode(array("error" => Error::display($error)));
                exit;
            }

            $projectMode = $project->bookProject;

            if($sourceTranslation == null)
            {
                if(!in_array($projectMode, ["tq","tw","odb","rad"]))
                    $error[] = __("choose_source_trans");
            }

            if(in_array($projectMode, ["tn","tq","tw"]) && $sourceTools == null)
            {
                $error[] = __("choose_source_".$projectMode);
            }

            if(in_array($projectMode, ["tq","tw"]))
            {
                $sourceTranslation = "ulb|en";
            }
            elseif($projectMode == "odb")
            {
                $sourceTranslation = "odb|en";
            }
            elseif($projectMode == "rad")
            {
                $sourceTranslation = "rad|en";
            }

            if(!isset($error))
            {
                $gatewayLanguage = $project->gatewayLanguage;

                if(!$gatewayLanguage->admins->contains($this->_member))
                {
                    $error[] = __("not_enough_rights_error");
                    echo json_encode(array("error" => Error::display($error)));
                    return;
                }

                $sourceTrPair = explode("|", $sourceTranslation);

                $project->sourceBible = $sourceTrPair[0];
                $project->sourceLangID = $sourceTrPair[1];
                $project->resLangID = $sourceTools;

                if($toolsTn)
                    $project->tnLangID = $toolsTn;
                if($toolsTq)
                    $project->tqLangID = $toolsTq;
                if($toolsTw)
                    $project->twLangID = $toolsTw;

                $project->save();
                $project->admins()->sync($admins);
                echo json_encode(array("success" => __("successfully_updated")));
            }
            else
            {
                echo json_encode(array("error" => Error::display($error)));
            }
        }
    }

    public function getMembers()
    {
        if(!$this->_member->isSuperAdmin()
            && !$this->_member->isGlAdmin()
            && !$this->_member->isProjectAdmin()
            && !$this->_member->isBookAdmin()) {
            echo json_encode(array());
            exit;
        }

        $_POST = Gump::xss_clean($_POST);

        if(isset($_POST['search']) && $_POST['search'] != "")
        {
            $admins = $this->_membersModel->getMembersByTerm($_POST['search']);
            $arr = array();

            foreach ($admins as $admin) {
                $tmp = array();
                $tmp["value"] = $admin->memberID;
                $tmp["text"] = "{$admin->firstName} "
                    .mb_substr($admin->lastName, 0, 1)
                    .". ({$admin->userName})";

                $arr[] = $tmp;
            }
            echo json_encode($arr);
        }
    }

    public function searchMembers()
    {
        $response = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            echo json_encode(array());
            exit;
        }

        $_POST = Gump::xss_clean($_POST);

        $name = isset($_POST["name"]) && $_POST["name"] != "" ? $_POST["name"] : false;
        $role = isset($_POST["role"]) && preg_match("/^(translators|facilitators|all)$/", $_POST["role"]) ? $_POST["role"] : "all";
        $language = isset($_POST["language"]) && $_POST["language"] != "" ? [$_POST["language"]] : null;
        $page = isset($_POST["page"]) ? (integer)$_POST["page"] : 1;

        if($name || $role || $language)
        {
            $response["success"] = true;
            $response["count"] = $this->_membersModel->searchMembers($name, $role, $language, true, false, true);
            $response["members"] = $this->_membersModel->searchMembers($name, $role, $language, false, true, true, $page);
        }
        else
        {
            $response["error"] = __("choose_filter_option");
        }

        echo json_encode($response);
    }

    public function verifyMember()
    {
        $response = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $response["error"] = "not_allowed";
            echo json_encode($response);
            exit;
        }

        $_POST = Gump::xss_clean($_POST);

        $memberID = isset($_POST["memberID"]) ? (integer)$_POST["memberID"] : null;

        if($memberID) {
            $this->_membersModel->updateMember(
                ["active" => true, "verified" => true, "activationToken" => null],
                ["memberID" => $memberID]);
            $response["success"] = true;
        }
        else
        {
            $response["error"] = "wrong_parameters";
        }
        echo json_encode($response);
    }

    public function clearCache()
    {
        $response = ["success" => false];

        if(!$this->_member->isGlAdmin() && $this->_member->isProjectAdmin()) {
            $error[] = __("not_enough_rights_error");
            echo json_encode(array("error" => Error::display($error)));
            return;
        }

        $_POST = Gump::xss_clean($_POST);

        $sort = $_POST["sort"] ?? null;
        $bookCode = $_POST["bookCode"] ?? null;
        $sourceLangID = $_POST["sourceLangID"] ?? null;
        $sourceBible = $_POST["sourceBible"] ?? null;

        // Book source
        $cache_keyword = $bookCode."_".$sourceLangID."_".$sourceBible."_usfm";

        if(Cache::has($cache_keyword))
            Cache::forget($cache_keyword);

        $source = $this->_apiModel->getCachedSourceBookFromApi(
            $sourceBible,
            $bookCode,
            $sourceLangID,
            $sort);

        if($source)
            $response["success"] = true;

        echo json_encode($response);
    }

    public function updateAllBooksCache()
    {
        $response = ["success" => false];

        if(!$this->_member->isGlAdmin() && $this->_member->isProjectAdmin()) {
            $error[] = __("not_enough_rights_error");
            echo json_encode(array("error" => Error::display($error)));
            return;
        }

        $_POST = Gump::xss_clean($_POST);

        $sourceLangID = $_POST["sourceLangID"] ?? null;
        $sourceBible = $_POST["sourceBible"] ?? null;

        $booksUpdated = 0;

        if($sourceLangID && $sourceBible)
        {
            $books = $this->bookInfoRepo->all();

            $renDir = "../app/Templates/Default/Assets/source/".$sourceLangID."_".$sourceBible."_tmp";
            $origDir = "../app/Templates/Default/Assets/source/".$sourceLangID."_".$sourceBible;

            //File::deleteDirectory($renDir);
            if(File::exists($origDir))
                File::move($origDir, $renDir);

            foreach ($books as $book)
            {
                $bookCode = $book->code;
                $sort = $book->sort;

                // Book source
                $cache_keyword = $bookCode."_".$sourceLangID."_".$sourceBible."_usfm";

                if(Cache::has($cache_keyword))
                    Cache::forget($cache_keyword);

                $source = $this->_apiModel->getCachedSourceBookFromApi(
                    $sourceBible,
                    $bookCode,
                    $sourceLangID,
                    $sort);

                if($source)
                {
                    $response["success"] = true;
                    $booksUpdated++;
                }
            }

            if($booksUpdated > 0)
            {
                File::deleteDirectory($renDir);

            }
            else
            {
                File::move($renDir, $origDir);
            }
        }

        $response["booksUpdated"] = $booksUpdated;

        echo json_encode($response);
    }

    public function blockMember()
    {
        $response = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $response["error"] = "not_allowed";
            echo json_encode($response);
            exit;
        }

        $_POST = Gump::xss_clean($_POST);

        $memberID = isset($_POST["memberID"]) ? (integer)$_POST["memberID"] : null;

        if($memberID)
        {
            $member = $this->_membersModel->getMember(
                ["blocked"],
                ["memberID", $memberID]
            );

            if(!empty($member))
            {
                $this->_membersModel->updateMember(
                    ["blocked" => !$member[0]->blocked],
                    ["memberID" => $memberID]);

                $response["success"] = true;
                $response["blocked"] = !$member[0]->blocked;
                echo json_encode($response);
            }
            else
            {
                $response["error"] = "no_member";
                echo json_encode($response);
                exit;
            }
        }
        else
        {
            $response["error"] = "wrong_parameters";
            echo json_encode($response);
        }
    }

    public function getTargetLanguagesByGwLanguage()
    {
        if(!$this->_member->isGlAdmin() && !$this->_member->isProjectAdmin()) {
            echo json_encode([]);
        }

        $gwLang = isset($_POST["gwLang"]) && $_POST["gwLang"] != "" ? $_POST["gwLang"] : null;

        if($gwLang)
        {
            $gwLang = explode("|", $gwLang)[0];
            $gatewayLanguage = $this->languageRepo->where("langID", $gwLang)->first();
            echo $this->languageRepo
                ->where("gwLang", $gatewayLanguage->langName)
                ->get()
                ->toJson();
        }
    }

    public function createEvent()
    {
        if(!$this->_member->isGlAdmin() && $this->_member->isProjectAdmin()) {
            $error[] = __("not_enough_rights_error");
            echo json_encode(array("error" => Error::display($error)));
            return;
        }

        $_POST = Gump::xss_clean($_POST);

        $bookCode = isset($_POST['book_code']) && $_POST['book_code'] != "" ? $_POST['book_code'] : null;
        $projectID = isset($_POST['projectID']) && $_POST['projectID'] != "" ? (integer)$_POST['projectID'] : null;
        $eventLevel = isset($_POST['eventLevel']) && $_POST['eventLevel'] != "" ? (integer)$_POST['eventLevel'] : 1;
        $langInput = isset($_POST['langInput']) && $_POST['langInput'] != "" ? true : false;
        $admins = isset($_POST['admins']) && !empty($_POST['admins']) ? array_unique($_POST['admins']) : [];
        $act = isset($_POST['act']) && preg_match("/^(create|edit|delete)$/", $_POST['act']) ? $_POST['act'] : "create";

        if($bookCode == null)
            $error[] = __('wrong_book_code');

        if($projectID == null)
            $error[] = __('wrong_project_id');

        if(!isset($error))
        {
            $event = $this->eventRepo->where(["projectID" => $projectID, "bookCode" => $bookCode])->first();
            if ($event) {
                $project = $event->project;
                $bookInfo = $event->bookInfo;
            } else {
                $project = $this->projectRepo->get($projectID);
                $bookInfo = $this->bookInfoRepo->where("code", $bookCode)->first();
            }

            $postdata = [];

            switch($act)
            {
                case "create":
                    if(empty($admins))
                    {
                        $error[] = __('enter_admins');
                        echo json_encode(array("error" => Error::display($error)));
                        return;
                    }

                    if($project->bookProject != "ulb" || $eventLevel != 1)
                    {
                        if($langInput)
                        {
                            $error[] = __('lang_input_not_allowed');
                            echo json_encode(array("error" => Error::display($error)));
                            return;
                        }
                    }

                    // Check if event is ready for Level 2, Level 3 check
                    switch ($eventLevel)
                    {
                        case 1:
                            if($event)
                            {
                                $error[] = __("event_already_exists");
                                echo json_encode(array("error" => Error::display($error)));
                                return;
                            }
                            break;
                        case 2:
                            if (in_array($project->bookProject, ["ulb","udb"]))
                            {
                                if(!$event || $event->state != EventStates::TRANSLATED)
                                {
                                    $error[] = __("l2_l3_create_event_error");
                                    echo json_encode(array("error" => Error::display($error)));
                                    return;
                                }
                            }
                            elseif (in_array($project->bookProject, ["tn","tq"]))
                            {
                                if($event)
                                {
                                    $error[] = __("event_already_exists");
                                    echo json_encode(array("error" => Error::display($error)));
                                    return;
                                }
                            }
                            break;
                        case 3:
                            if (in_array($project->bookProject, ["ulb","udb"])) {
                                if(!$event || $event->state != EventStates::L2_CHECKED) {
                                    $error[] = __("l2_l3_create_event_error");
                                    echo json_encode(array("error" => Error::display($error)));
                                    return;
                                }
                            } elseif (in_array($project->bookProject, ["tn","tq"])) {
                                // Check if related ulb event is complete (level 3 checked)
                                $ulbProject = $this->projectRepo->where([
                                    "glID" => $project->glID,
                                    "gwLang" => $project->gwLang,
                                    "targetLang" => $project->targetLang,
                                    "bookProject" => "ulb",
                                ])->first();

                                $ulbEvent = null;
                                if($ulbProject)
                                    $ulbEvent = $this->eventRepo->where([
                                        "projectID" => $ulbProject->projectID,
                                        "bookCode" => $bookCode
                                    ])->first();

                                if(!$event || $event->state != EventStates::TRANSLATED
                                    || !$ulbEvent || $ulbEvent->state != EventStates::COMPLETE) {
                                    $error[] = __("l2_l3_create_event_error");
                                    echo json_encode(array("error" => Error::display($error)));
                                    return;
                                }
                            } elseif ($project->bookProject == "sun" && $event) {
                                $error[] = __("event_already_exists");
                                echo json_encode(array("error" => Error::display($error)));
                                return;
                            }
                            break;
                    }

                    $postdata["projectID"] = $projectID;
                    $postdata["bookCode"] = $bookCode;

                    if($bookInfo->category == "odb") {
                        $odb = $this->_apiModel->getOtherSource("odb", $bookInfo->code, $project->sourceLangID);
                        if(empty($odb)) {
                            $error[] = __("no_source_error");
                            echo json_encode(array("error" => Error::display($error)));
                            return;
                        }
                    } elseif ($bookInfo->category == "rad") {
                        $radio = $this->_apiModel->getOtherSource("rad", $bookInfo->code, $project->sourceLangID);
                        if(empty($radio)) {
                            $error[] = __("no_source_error");
                            echo json_encode(array("error" => Error::display($error)));
                            return;
                        }
                    } else {
                        // Book source
                        $cache_keyword = $bookCode."_".$project->sourceLangID."_".$project->sourceBible."_usfm";

                        if(!Cache::has($cache_keyword)) {
                            $usfm = $this->_apiModel->getCachedSourceBookFromApi(
                                $project->sourceBible,
                                $bookInfo->code,
                                $project->sourceLangID,
                                $bookInfo->sort);

                            if(!$usfm || empty($usfm)) {
                                $error[] = __("no_source_error");
                                echo json_encode(array("error" => Error::display($error)));
                                return;
                            }
                        }
                    }

                    if(!$event) {
                        $postdata["dateFrom"] = date("Y-m-d H:i:s", strtotime("0000-00-00"));
                        $postdata["dateTo"] = date("Y-m-d H:i:s", strtotime("0000-00-00"));
                        $postdata["langInput"] = $langInput;

                        $event = $this->eventRepo->create($postdata, $project);
                    } else {
                        // Create(change state) L2 event
                        if($event->state == EventStates::TRANSLATED) {
                            if(in_array($project->bookProject, ["tn","tq"])) {
                                $event->state = EventStates::L3_RECRUIT;
                            } else {
                                $event->state = EventStates::L2_RECRUIT;
                            }
                        } else {
                            $event->state = EventStates::L3_RECRUIT;
                        }

                        $event->save();
                    }
                    $event->admins()->sync($admins);

                    echo json_encode(array("success" => __("successfully_created")));
                    break;

                case "edit":
                    if(!$event)
                    {
                        $error[] = __("event_not_exists_error");
                        echo json_encode(array("error" => Error::display($error)));
                        return;
                    }

                    if(empty($admins))
                    {
                        $error[] = __('enter_admins');
                    }

                    $event->admins()->sync($admins);
                    echo json_encode(array("success" => __("successfully_updated")));
                    break;

                case "delete":
                    if(!$event)
                    {
                        $error[] = __("event_not_exists_error");
                        echo json_encode(array("error" => Error::display($error)));
                        return;
                    }

                    $event->admins()->detach();
                    $event->delete();
                    echo json_encode(array("success" => __("successfully_deleted")));

                    break;
            }
        }
        else
        {
            echo json_encode(array("error" => Error::display($error)));
        }
    }


    public function createEventTw()
    {
        if(!$this->_member->isGlAdmin() && $this->_member->isProjectAdmin()) {
            $error[] = __("not_enough_rights_error");
            echo json_encode(array("error" => Error::display($error)));
            return;
        }

        $_POST = Gump::xss_clean($_POST);

        $bookCode = isset($_POST['book_code']) && $_POST['book_code'] != "" ? $_POST['book_code'] : null;
        $projectID = isset($_POST['projectID']) && $_POST['projectID'] != "" ? (integer)$_POST['projectID'] : null;
        $eventLevel = isset($_POST['eventLevel']) && $_POST['eventLevel'] != "" ? (integer)$_POST['eventLevel'] : 1;
        $admins = isset($_POST['admins']) && !empty($_POST['admins']) ? array_unique($_POST['admins']) : [];
        $act = isset($_POST['act']) && preg_match("/^(create|edit|delete)$/", $_POST['act']) ? $_POST['act'] : "create";

        if($bookCode == null)
            $error[] = __('wrong_book_code');

        if($projectID == null)
            $error[] = __('wrong_project_id');

        if(!isset($error))
        {
            $event = $this->eventRepo->where(["projectID" => $projectID, "bookCode" => $bookCode])->first();
            if ($event) {
                $project = $event->project;
            } else {
                $project = $this->projectRepo->get($projectID);
            }

            $postdata = [];

            switch($act) {
                case "create":
                    // Check if the event is ready for Level 2, Level 3 check
                    if(empty($admins))
                    {
                        $error[] = __('enter_admins');
                        echo json_encode(array("error" => Error::display($error)));
                        return;
                    }

                    switch ($eventLevel) {
                        case 2:
                            if($event)
                            {
                                $error[] = __("event_already_exists");
                                echo json_encode(array("error" => Error::display($error)));
                                return;
                            }
                            break;
                        case 3:
                            if(!$event || $event->state != EventStates::TRANSLATED)
                            {
                                $error[] = __("l2_l3_create_event_error");
                                echo json_encode(array("error" => Error::display($error)));
                                return;
                            }
                            break;
                    }

                    $postdata["bookCode"] = $bookCode;

                    if(!$event)
                    {
                        $postdata["dateFrom"] = date("Y-m-d H:i:s", strtotime("0000-00-00"));
                        $postdata["dateTo"] = date("Y-m-d H:i:s", strtotime("0000-00-00"));
                        $event = $this->eventRepo->create($postdata, $project);
                    }
                    else
                    {
                        // Create(change state) L3 event
                        $event->state = EventStates::L3_RECRUIT;
                        $event->save();
                    }
                    $event->admins()->sync($admins);

                    echo json_encode(array("success" => __("successfully_created")));
                    break;

                case "edit":
                    if(!$event)
                    {
                        $error[] = __("event_not_exists_error");
                        echo json_encode(array("error" => Error::display($error)));
                        return;
                    }

                    if(empty($admins))
                    {
                        $error[] = __('enter_admins');
                        echo json_encode(array("error" => Error::display($error)));
                        return;
                    }

                    $event->admins()->sync($admins);
                    echo json_encode(array("success" => __("successfully_updated")));
                    break;

                case "delete":
                    if(!$event)
                    {
                        $error[] = __("event_not_exists_error");
                        echo json_encode(array("error" => Error::display($error)));
                        return;
                    }

                    $event->admins()->detach();
                    $event->delete();
                    echo json_encode(array("success" => __("successfully_deleted")));

                    break;
            }
        }
        else
        {
            echo json_encode(array("error" => Error::display($error)));
        }
    }


    public function getSource()
    {
        $response = array();
        $_POST = Gump::xss_clean($_POST);

        $bookCode = isset($_POST["bookCode"]) && $_POST["bookCode"] != "" ? $_POST["bookCode"] : null;
        $sourceLangID = isset($_POST["sourceLangID"]) && $_POST["sourceLangID"] != "" ? $_POST["sourceLangID"] : null;
        $bookProject = isset($_POST["bookProject"]) && $_POST["bookProject"] != "" ? $_POST["bookProject"] : null;

        if($bookCode && $sourceLangID && $bookProject)
        {
            $cache_keyword = $bookCode."_".$sourceLangID."_".$bookProject;

            if(Cache::has($cache_keyword))
            {
                $source = Cache::get($cache_keyword);
                $json = json_decode($source, true);
            }
            else
            {
                $source = $this->_apiModel->getSourceBookFromApi($bookCode, $sourceLangID, $bookProject);
                $json = json_decode($source, true);

                if(!empty($json))
                    Cache::add($cache_keyword, $source, 60*24*7);
            }

            if(!empty($json))
            {
                $response["chaptersNum"] = sizeof($json["chapters"]);

                $text = "";

                foreach ($json["chapters"] as $chapter) {
                    foreach ($chapter["frames"] as $frame) {
                        $text .= $frame["text"];
                    }
                }

                $text = preg_split("/<verse\D+(\d+)\D+>/", $text, -1, PREG_SPLIT_DELIM_CAPTURE);
                $response["versesNum"] = !empty($text) ? (sizeof($text)-1)/2 : 0;
            }
        }

        echo json_encode($response);
    }


    private function importScriptureToEvent($usfm, $projectID, $eventID, $bookCode, $level)
    {
        $response = ["success" => false];
        $usfmData = UsfmParser::parse($usfm);

        // Check if a "fake" user exists
        $member = $this->memberRepo->getByUsername("spec");
        if (!$member) {
            $memberData = [
                "userName" => "spec",
                "firstName" => "Special",
                "lastName" => "User",
                "password" => "none",
                "email" => "none",
                "active" => true,
                "verified" => true
            ];
            $member = $this->memberRepo->create($memberData, ["complete", true]);
        }

        $project = $this->projectRepo->get($projectID);

        if($project->bookProject == "sun")
        {
            $response["error"] = __("not_allowed_action");
            return $response;
        }

        if(in_array($project->bookProject, ["tn","tq","tw"]))
        {
            $ulbProject = $this->projectRepo->where([
                "glID" => $project->glID,
                "gwLang" => $project->gwLang,
                "targetLang" => $project->targetLang,
                "bookProject" => "ulb"
            ])->first();

            // Create ulb project if it doesn't exist
            if(!$ulbProject)
            {
                $postData = [
                    "gwLang" => $project->gwLang,
                    "targetLang" => $project->targetLang,
                    "bookProject" => "ulb",
                    "sourceLangID" => $project->sourceLangID,
                    "sourceBible" => $project->sourceBible
                ];
                $ulbProject = $this->projectRepo->create($postData, $project->gatewayLanguage);
            }

            $project = $ulbProject;

            $event = $this->eventRepo->where([
                "projectID" => $project->projectID,
                "bookCode" => $bookCode
            ])->first();

            if($event)
                $eventID = $event->eventID;
        }
        else
        {
            $event = $this->eventRepo->get($eventID);
        }

        // Create event if it doesn't exist
        if(!$event)
        {
            $event = $this->eventRepo->create([
                "bookCode" => $bookCode,
                "state" => EventStates::STARTED
            ], $project);
            $eventID = $event->eventID;
        }

        if($event) {
            if(isset($usfmData["chapters"]) && sizeof($usfmData["chapters"]) == $event->bookInfo->chaptersNum)
            {
                // Check if there are translations of this event in database
                $translations = $event->chunks;
                if($translations->isEmpty()) {
                    if($level == 3) {
                        $response["error"] = __("not_allowed_action");
                        return $response;
                    }

                    // Create new translator
                    $chkData = [];
                    for($i=1; $i<=$event->bookInfo->chaptersNum; $i++)
                    {
                        $chkData[$i] = ["memberID" => $member->memberID, "done" => 1];
                    }

                    $trData = array(
                        "step" => EventSteps::NONE,
                        "currentChapter" => 0,
                        "verbCheck" => json_encode($chkData),
                        "peerCheck" => json_encode($chkData),
                        "kwCheck" => json_encode($chkData),
                        "crCheck" => json_encode($chkData)
                    );
                    $event->translators()->attach($member, $trData);
                    $translator = $member->translators->where("eventID", $eventID, false)->first();
                    $trID = $translator->trID;

                    $l2chID = 0;
                    if($level == 2) {
                        // Create new checker
                        $sndCheckData = [];
                        $peerCheckData = [];
                        for($i=1; $i<=$event->bookInfo->chaptersNum; $i++)
                        {
                            $sndCheckData[$i] = ["memberID" => $member->memberID, "done" => 2];
                            $peerCheckData[$i] = ["memberID" => $member->memberID, "done" => 1];
                        }

                        $l2chData = array(
                            "step" => EventSteps::NONE,
                            "currentChapter" => 0,
                            "sndCheck" => json_encode($sndCheckData),
                            "peer1Check" => json_encode($peerCheckData),
                            "peer2Check" => json_encode($peerCheckData)
                        );
                        $event->checkersL2()->attach($member, $l2chData);
                        $checkerL2 = $member->checkersL2->where("eventID", $eventID, false)->first();
                        $l2chID = $checkerL2->l2chID;
                    }

                    foreach ($usfmData["chapters"] as $key => $chapter) {
                        $chunks = [];
                        foreach ($chapter as $chunkkey => $chunk) {
                            $chunks[] = array_keys($chunk);

                            $translationVerses = [
                                EventMembers::TRANSLATOR => [
                                    "blind" => "",
                                    "verses" => $chunk
                                ],
                                EventMembers::L2_CHECKER => [
                                    "verses" => $level == 2 ? $chunk : array()
                                ],
                                EventMembers::L3_CHECKER => [
                                    "verses" => array()
                                ],
                            ];

                            // Create new translations
                            $this->_translationModel->createTranslation([
                                "projectID" => $event->projectID,
                                "eventID" => $eventID,
                                "trID" => $trID,
                                "l2chID" => $l2chID,
                                "targetLang" => $project->targetLang,
                                "bookProject" => $project->bookProject,
                                "sort" => $event->bookInfo->sort,
                                "bookCode" => $event->bookCode,
                                "chapter" => $key,
                                "chunk" => $chunkkey,
                                "firstvs" => key($chunk),
                                "translatedVerses" => json_encode($translationVerses),
                                "translateDone" => true
                            ]);
                        }

                        // Assign chapters to new translator
                        $postdata = [
                            "trID" => $trID,
                            "l2memberID" => $level == 2 ? $member->memberID : 0,
                            "l2chID" => $l2chID,
                            "chapter" => $key,
                            "chunks" => json_encode($chunks),
                            "done" => true,
                            "l2checked" => $level == 2
                        ];
                        $event->translatorsWithChapters()->attach($member, $postdata);

                        $event->state = $level == 2 ? EventStates::L2_CHECKED : EventStates::TRANSLATED;
                        $event->save();
                    }

                    $response["success"] = true;
                    $response["message"] = __("import_successful_message");
                }
                else
                {
                    if(in_array($event->state, [EventStates::TRANSLATED, EventStates::L2_CHECKED]))
                    {
                        if(($event->state == EventStates::TRANSLATED && $level == 3) ||
                            $event->state == EventStates::L2_CHECKED && $level == 1)
                        {
                            $response["error"] = __("not_allowed_action");
                            return $response;
                        }

                        foreach ($usfmData["chapters"] as $chapter => $chunks) {
                            $new_chapter = [];
                            foreach ($chunks as $verses) {
                                foreach ($verses as $verse => $text) {
                                    $new_chapter[$verse] = $text;
                                }
                            }
                            $usfmData["chapters"][$chapter] = $new_chapter;
                        }

                        foreach ($translations as $tran) {
                            $verses = (array)json_decode($tran->translatedVerses, true);

                            foreach ($verses[EventMembers::TRANSLATOR]["verses"] as $verse => $text) {
                                if(isset($usfmData["chapters"][$tran->chapter]) &&
                                    isset($usfmData["chapters"][$tran->chapter][$verse]) &&
                                    trim($usfmData["chapters"][$tran->chapter][$verse]) != "")
                                {
                                    switch ($level)
                                    {
                                        case 1:
                                            $verses[EventMembers::TRANSLATOR]["verses"][$verse] = $usfmData["chapters"][$tran->chapter][$verse];
                                            break;
                                        case 2:
                                            $verses[EventMembers::L2_CHECKER]["verses"][$verse] = $usfmData["chapters"][$tran->chapter][$verse];
                                            break;
                                        case 3:
                                            $verses[EventMembers::L3_CHECKER]["verses"][$verse] = $usfmData["chapters"][$tran->chapter][$verse];
                                            break;
                                    }
                                }
                            }

                            $this->_translationModel->updateTranslation([
                                "translatedVerses" => json_encode($verses),
                                "l2chID" => ($tran->l2chID == 0 && $level == 2 ? $member->memberID : $tran->l2chID),
                                "l3chID" => $tran->l3chID == 0 && $level == 3 ? $member->memberID : $tran->l3chID
                            ], ["tID" => $tran->tID]);
                        }

                        $chapters = $event->chapters;

                        if($event->state == EventStates::TRANSLATED && $level == 2)
                        {
                            // Create new level 2 checker
                            $sndCheckData = [];
                            $peerCheckData = [];
                            for($i=1; $i<=$event->bookInfo->chaptersNum; $i++)
                            {
                                $sndCheckData[$i] = ["memberID" => $member->memberID, "done" => 2];
                                $peerCheckData[$i] = ["memberID" => $member->memberID, "done" => 1];
                            }

                            $l2chData = array(
                                "step" => EventSteps::NONE,
                                "currentChapter" => 0,
                                "sndCheck" => json_encode($sndCheckData),
                                "peer1Check" => json_encode($peerCheckData),
                                "peer2Check" => json_encode($peerCheckData)
                            );

                            $event->checkersL2()->attach($member, $l2chData);
                            $checkerL2 = $member->checkersL2->where("eventID", $eventID, false)->first();
                            $l2chID = $checkerL2->l2chID;

                            // Assign chapters to new level 2 checker
                            foreach ($chapters as $chapter) {
                                $this->_eventsModel->updateChapter([
                                    "l2memberID" => $member->memberID,
                                    "l2chID" => $l2chID,
                                    "l2checked" => true
                                ],["chapterID" => $chapter["chapterID"]]);
                            }
                        }

                        if($event->state == EventStates::L2_CHECKED && $level == 3)
                        {
                            // Create new level 3 checker
                            $peerCheckData = [];
                            for($i=1; $i<=$event->bookInfo->chaptersNum; $i++)
                            {
                                $peerCheckData[$i] = ["memberID" => $member->memberID, "done" => 2];
                            }

                            $l3chData = array(
                                "step" => EventSteps::NONE,
                                "currentChapter" => 0,
                                "peerCheck" => json_encode($peerCheckData)
                            );

                            $event->checkersL3()->attach($member, $l3chData);
                            $checkerL3 = $member->checkersL3->where("eventID", $eventID, false)->first();
                            $l3chID = $checkerL3->l3chID;

                            // Assign chapters to new level 3 checker
                            foreach ($chapters as $chapter) {
                                $this->_eventsModel->updateChapter([
                                    "l3memberID" => $member->memberID,
                                    "l3chID" => $l3chID,
                                    "l3checked" => true
                                ],["chapterID" => $chapter["chapterID"]]);
                            }
                        }

                        $event->state = $level == 2 ? EventStates::L2_CHECKED : ($level == 3 ? EventStates::COMPLETE : EventStates::TRANSLATED);
                        $event ->save();

                        $response["success"] = true;
                        $response["message"] = __("import_successful_message");
                    }
                    else
                    {
                        $response["error"] = __("event_has_translations_error");
                    }
                }
            }
            else
            {
                $response["error"] = __("usfm_not_valid_error");
            }
        }
        else
        {
            $response["error"] = __("event_notexist_error");
        }

        return $response;
    }


    private function importResourceToEvent($resource, $projectID, $eventID, $bookCode, $importLevel)
    {
        $response = ["success" => false];

        // Check if a "fake" user exists
        $member = $this->memberRepo->getByUsername("spec");
        if (!$member) {
            $memberData = [
                "userName" => "spec",
                "firstName" => "Special",
                "lastName" => "User",
                "password" => "none",
                "email" => "none",
                "active" => true,
                "verified" => true
            ];
            $member = $this->memberRepo->create($memberData, ["complete", true]);
        }

        $project = $this->projectRepo->get($projectID);
        $event = $this->eventRepo->get($eventID);

        $isNewEvent = false;

        // Create event if it doesn't exist
        if(!$event)
        {
            $event = $this->eventRepo->create([
                "bookCode" => $bookCode,
                "state" => $importLevel == 1 ? EventStates::TRANSLATING : EventStates::TRANSLATED,
            ], $project);
            $eventID = $event->eventID;
            $isNewEvent = true;
        }

        if($event)
        {
            if($event->state == EventStates::TRANSLATED)
            {
                // Check if there are translations of this event in database
                $translations = $event->chunks;
                if($translations->isEmpty())
                {
                    // Create new translator
                    $peerCheckData = [];
                    $otherCheckData = [];

                    $trData = array(
                        "step" => EventSteps::NONE,
                        "currentChapter" => 0
                    );

                    $event->translators()->attach($member, $trData);
                    $translator = $member->translators->where("eventID", $eventID, false)->first();
                    $trID = $translator->trID;

                    foreach ($resource as $chapter => $chunks)
                    {
                        ksort($chunks, SORT_NUMERIC);
                        if($event->project->bookProject == "tw")
                        {
                            $words_list = array_map(function ($elm) {
                                return $elm["word"];
                            }, $chunks);

                            $chapter = $this->_eventsModel->createTwGroup([
                                "eventID" => $eventID,
                                "words" => json_encode($words_list)
                            ]);
                        }

                        $peerCheckData[$chapter] = ["memberID" => $member->memberID, "done" => 1];
                        $done = $event->project->bookProject == "tn" ? 6 : 3;
                        $otherCheckData[$chapter] = ["memberID" => $member->memberID, "done" => $done];

                        $chunkKey = 0;
                        foreach ($chunks as $firstvs => $chunk) {
                            $translationVerses = [
                                EventMembers::TRANSLATOR => [
                                    "verses" => $event->project->bookProject == "tw" ? $chunk["text"] : $chunk[0]
                                ],
                                EventMembers::CHECKER => [
                                    "verses" => $event->project->bookProject == "tw" ? $chunk["text"] : $chunk[0]
                                ],
                                EventMembers::L2_CHECKER => [
                                    "verses" => array()
                                ],
                                EventMembers::L3_CHECKER => [
                                    "verses" => array()
                                ],
                            ];

                            // Create new translations
                            $this->_translationModel->createTranslation([
                                "projectID" => $project->projectID,
                                "eventID" => $eventID,
                                "trID" => $trID,
                                "targetLang" => $project->targetLang,
                                "bookProject" => $project->bookProject,
                                "sort" => $event->bookInfo->sort,
                                "bookCode" => $event->bookCode,
                                "chapter" => $chapter,
                                "chunk" => $chunkKey,
                                "firstvs" => $firstvs,
                                "translatedVerses" => json_encode($translationVerses),
                                "translateDone" => true
                            ]);

                            $chunkKey++;
                        }

                        if($event->project->bookProject == "tw")
                        {
                            $resource_chunks = array_keys($words_list);
                            usort($resource_chunks, function($a, $b) {
                                if(!isset($a[0])) return -1;
                                if(!isset($b[0])) return 1;
                                return $a[0] <= $b[0] ? -1 : 1;
                            });
                        }
                        else
                        {
                            if($chapter > 0)
                            {
                                // Get related Scripture to define total verses of the chapter
                                $relatedScripture = $this->_apiModel->getBookText([
                                    "sourceBible" => $project->sourceBible,
                                    "bookCode" => $event->bookCode,
                                    "sourceLangID" => $project->sourceLangID,
                                    "sort" => $event->bookInfo->sort
                                ], $chapter);

                                if(empty($relatedScripture))
                                    $relatedScripture = $this->_apiModel->getBookText([
                                        "sourceBible" => "ulb",
                                        "bookCode" => $event->bookCode,
                                        "sourceLangID" => "en",
                                        "sort" => $event->bookInfo->sort
                                    ], $chapter);

                                if(empty($relatedScripture))
                                    $response["warning"] = true;

                                if($project->bookProject == "tn") {
                                    $notes = [
                                        "notes" => $chunks,
                                        "totalVerses" => isset($relatedScripture) ? $relatedScripture["totalVerses"] : 0];
                                    $resource_chunks = $this->_apiModel->getNotesChunks($notes);
                                } else {
                                    $questions = [
                                        "questions" => $chunks,
                                        "totalVerses" => isset($relatedScripture) ? $relatedScripture["totalVerses"] : 0];
                                    $resource_chunks = $this->_apiModel->getQuestionsChunks($questions);
                                }

                                usort($resource_chunks, function($a, $b) {
                                    if(!isset($a[0])) return -1;
                                    if(!isset($b[0])) return 1;
                                    return $a[0] <= $b[0] ? -1 : 1;
                                });
                            }
                            else
                            {
                                $resource_chunks = [[0]];
                            }
                        }

                        // Assign chapters to new translator
                        $postdata = [
                            "trID" => $trID,
                            "chapter" => $chapter,
                            "chunks" => json_encode($resource_chunks),
                            "done" => true,
                            "checked" => true
                        ];
                        $event->translatorsWithChapters()->attach($member, $postdata);

                        $event->state = EventStates::TRANSLATED;
                        $event->save();
                    }

                    $translator->peerCheck = json_encode($peerCheckData);
                    $translator->otherCheck = json_encode($otherCheckData);
                    $translator->save();

                    $response["success"] = true;
                    $response["message"] = __("import_successful_message");
                }
                else
                {
                    $contentChunks = array_reduce($resource, function ($arr, $elm) {
                        return array_merge((array)$arr, array_keys($elm));
                    });

                    if(sizeof($contentChunks) == sizeof($translations))
                    {
                        foreach ($translations as $tran) {
                            $verses = (array)json_decode($tran->translatedVerses, true);

                            if(isset($resource[$tran->chapter]) &&
                                isset($resource[$tran->chapter][$tran->firstvs]) &&
                                trim($resource[$tran->chapter][$tran->firstvs][0]) != "")
                            {
                                $verses[EventMembers::CHECKER]["verses"] = $resource[$tran->chapter][$tran->firstvs][0];
                            }

                            $this->_translationModel->updateTranslation([
                                "translatedVerses" => json_encode($verses),
                            ], ["tID" => $tran->tID]);
                        }

                        $response["success"] = true;
                        $response["message"] = __("import_successful_message");
                    }
                    else
                    {
                        $response["message"] = __("content_chunks_not_equal_error");
                    }
                }
            } elseif ($isNewEvent) {
                // Add level 1 translation for a just created event

                // Create new translator
                $otherCheckData = [];

                $trData = array(
                    "step" => EventSteps::NONE,
                    "currentChapter" => 0
                );

                $event->translators()->attach($member, $trData);
                $translator = $member->translators->where("eventID", $eventID, false)->first();
                $trID = $translator->trID;

                foreach ($resource as $chapter => $chunks)
                {
                    ksort($chunks, SORT_NUMERIC);
                    if($project->bookProject == "tw")
                    {
                        $words_list = array_map(function ($elm) {
                            return $elm["word"];
                        }, $chunks);

                        $chapter = $this->_eventsModel->createTwGroup([
                            "eventID" => $eventID,
                            "words" => json_encode($words_list)
                        ]);
                    }

                    $otherCheckData[$chapter] = ["memberID" => 0, "done" => 0];

                    $chunkKey = 0;
                    foreach ($chunks as $firstvs => $chunk) {
                        $translationVerses = [
                            EventMembers::TRANSLATOR => [
                                "verses" => $project->bookProject == "tw" ? $chunk["text"] : $chunk[0]
                            ],
                            EventMembers::CHECKER => [
                                "verses" => []
                            ],
                            EventMembers::L2_CHECKER => [
                                "verses" => []
                            ],
                            EventMembers::L3_CHECKER => [
                                "verses" => []
                            ],
                        ];

                        // Create new translations
                        $this->_translationModel->createTranslation([
                            "projectID" => $project->projectID,
                            "eventID" => $eventID,
                            "trID" => $trID,
                            "targetLang" => $project->targetLang,
                            "bookProject" => $project->bookProject,
                            "sort" => $event->bookInfo->sort,
                            "bookCode" => $event->bookCode,
                            "chapter" => $chapter,
                            "chunk" => $chunkKey,
                            "firstvs" => $firstvs,
                            "translatedVerses" => json_encode($translationVerses),
                            "translateDone" => true
                        ]);

                        $chunkKey++;
                    }

                    if($project->bookProject == "tw")
                    {
                        $resource_chunks = array_keys($words_list);
                        usort($resource_chunks, function($a, $b) {
                            if(!isset($a[0])) return -1;
                            if(!isset($b[0])) return 1;
                            return $a[0] <= $b[0] ? -1 : 1;
                        });
                    }
                    else
                    {
                        if($chapter > 0)
                        {
                            // Get related Scripture to define total verses of the chapter
                            $relatedScripture = $this->_apiModel->getBookText([
                                "sourceBible" => $project->sourceBible,
                                "bookCode" => $event->bookCode,
                                "sourceLangID" => $project->sourceLangID,
                                "sort" => $event->bookInfo->sort
                            ], $chapter);

                            if(empty($relatedScripture))
                                $relatedScripture = $this->_apiModel->getBookText([
                                    "sourceBible" => "ulb",
                                    "bookCode" => $event->bookCode,
                                    "sourceLangID" => "en",
                                    "sort" => $event->bookInfo->sort
                                ], $chapter);

                            if(empty($relatedScripture))
                                $response["warning"] = true;

                            if($project->bookProject == "tn")
                            {
                                $notes = [
                                    "notes" => $chunks,
                                    "totalVerses" => isset($relatedScripture) ? $relatedScripture["totalVerses"] : 0];
                                $resource_chunks = $this->_apiModel->getNotesChunks($notes);
                            }
                            else
                            {
                                $questions = [
                                    "questions" => $chunks,
                                    "totalVerses" => isset($relatedScripture) ? $relatedScripture["totalVerses"] : 0];
                                $resource_chunks = $this->_apiModel->getQuestionsChunks($questions);
                            }

                            usort($resource_chunks, function($a, $b) {
                                if(!isset($a[0])) return -1;
                                if(!isset($b[0])) return 1;
                                return $a[0] <= $b[0] ? -1 : 1;
                            });
                        }
                        else
                        {
                            $resource_chunks = [[0]];
                        }
                    }

                    // Assign chapters to new translator
                    $postdata = [
                        "trID" => $trID,
                        "chapter" => $chapter,
                        "chunks" => json_encode($resource_chunks),
                        "done" => true,
                        "checked" => false
                    ];
                    $event->translatorsWithChapters()->attach($member, $postdata);

                    $event->state = EventStates::TRANSLATING;
                    $event->save();
                }

                $translator->otherCheck = json_encode($otherCheckData);
                $translator->save();

                $response["success"] = true;
                $response["message"] = __("import_successful_message");
            }
            else
            {
                $response["error"] = __("event_has_translations_error");
            }
        }
        else
        {
            $response["error"] = __("event_notexist_error");
        }

        return $response;
    }

    public function updateLanguages()
    {
        $result = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $reDownload = Input::get("download", false);

        $result = $this->_apiModel->insertLangsFromTD($reDownload);

        echo json_encode($result);
    }

    public function updateCatalog()
    {
        $result = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $reDownload = Input::get("download", false);

        $this->_apiModel->insertSourcesFromCatalog($reDownload);

        $result["success"] = true;
        echo json_encode($result);
    }

    public function clearAllCache()
    {
        $result = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $this->_apiModel->clearAllCache();
        $result["success"] = true;

        echo json_encode($result);
    }

    public function createMultipleUsers()
    {
        $result = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $amount = (integer)Input::get("amount", "50");
        $langs = (string)Input::get("langs", "en");
        $password = (string)Input::get("password", "");

        if(mb_strlen(trim($password)) < 6)
        {
            $result["error"] = __("password_short_error");
            echo json_encode($result);
            exit;
        }

        if($amount <= 0)
            $amount = 50;

        if($langs == "" || !preg_match("/[a-z\-]+,?\s?/", $langs))
            $langs = "en";

        $ilangs = explode(",", $langs);

        $langs = [];

        foreach ($ilangs as $lang) {
            $lang = trim($lang);

            $langs[$lang] = [3,3];
        }

        $password = Password::make($password);

        $res = $this->_membersModel->createMultipleMembers(
            $amount,
            $langs,
            $password);

        $result["success"] = true;
        $result["msg"] = $res;

        echo json_encode($result);
    }


    public function deleteSailWord()
    {
        $result = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $word = Input::get("word", "");

        if(trim($word) != "")
        {
            if($this->_saildictModel->deleteSunWord(["word" => $word]))
            {
                $result["success"] = true;
            }
        }

        echo json_encode($result);
    }


    public function createSailWord()
    {
        $result = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $word = Input::get("word", "");
        $symbol = Input::get("symbol", "");

        if(trim($word) != "" && trim($symbol) != "")
        {
            $data = [
                "word" => $word,
                "symbol" => $symbol
            ];

            $exist = $this->_saildictModel->getSunWord(["word" => $word]);

            if(empty($exist))
            {
                if($this->_saildictModel->createSunWord($data))
                {
                    $li = '<li class="sun_content" id="'.$word.'">
                            <div class="tools_delete_word glyphicon glyphicon-remove" title="'.__("delete").'">
                                <img src="'.template_url("img/loader.gif").'">
                            </div>

                            <div class="sail_word">'.$word.'</div>
                            <div class="sail_symbol">'.$symbol.'</div>
                            <input type="text" value="'.$symbol.'" />
                            <div class="clear"></div>
                        </li>';

                    $result["success"] = true;
                    $result["li"] = $li;
                }
            }
            else
            {
                $result["error"] = __("sail_word_exists");
            }
        }

        echo json_encode($result);
    }


    public function uploadSunFont()
    {
        $result = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $font_file = Input::file("file");

        if($font_file != null && $font_file->isValid())
        {
            $ext = $font_file->getClientOriginalExtension();
            if($ext == "woff")
            {
                $name = $font_file->getClientOriginalName();
                $destinationPath = "../app/Templates/Default/Assets/fonts/";

                if(preg_match("/backsun/i", $name))
                {
                    $fileName = "BackSUN.woff";
                    $font_file->move($destinationPath, $fileName);

                    if(File::exists(join("/", [$destinationPath, $fileName])))
                    {
                        $result["success"] = true;
                        $result["message"] = __("font_uploaded", $fileName);
                        echo json_encode($result);
                        exit;
                    }
                }
                elseif(preg_match("/sun/i", $name))
                {
                    $fileName = "SUN.woff";
                    $font_file->move($destinationPath, $fileName);

                    if(File::exists(join("/", [$destinationPath, $fileName])))
                    {
                        $result["success"] = true;
                        $result["message"] = __("font_uploaded", $fileName);
                        echo json_encode($result);
                        exit;
                    }
                }
                else
                {
                    $result["error"] = __("font_name_error");
                    echo json_encode($result);
                    exit;
                }
            }
            else
            {
                $result["error"] = __("font_format_error");
                echo json_encode($result);
                exit;
            }
        }
        else
        {
            $result["error"] = __("error_ocured", "Empty input!");
            echo json_encode($result);
            exit;
        }
    }

    public function uploadSunDict() {
        $result = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $dict_file = Input::file("file");

        if($dict_file != null && $dict_file->isValid())
        {
            $ext = $dict_file->getClientOriginalExtension();
            if($ext == "csv")
            {
                $new_dict = [];

                $file = $dict_file->openFile();
                while (!$file->eof()) {
                    $pair = $file->fgetcsv();
                    if(isset($pair[0]) && isset($pair[1])
                        && !empty($pair[0]) && !empty($pair[1]))
                    {
                        $wordObj = new stdClass();
                        $wordObj->symbol = $pair[0];
                        $wordObj->word = $pair[1];
                        $new_dict[] = $wordObj;
                    }
                }

                if(sizeof($new_dict) > 0)
                {
                    $this->_saildictModel->deleteAllWords();

                    foreach ($new_dict as $word)
                    {
                        $this->_saildictModel->createSunWord([
                            "symbol" => $word->symbol,
                            "word" => $word->word
                        ]);
                    }

                    $result["success"] = true;
                    $result["message"] = __("dictionary_updated");
                    echo json_encode($result);
                    exit;
                }
                else
                {
                    $result["error"] = __("empty_dictionary");
                    echo json_encode($result);
                    exit;
                }
            }
            else
            {
                $result["error"] = __("not_csv_format_error");
                echo json_encode($result);
                exit;
            }
        }
        else
        {
            $result["error"] = __("error_ocured", "Empty input!");
            echo json_encode($result);
            exit;
        }
    }

    public function uploadImage()
    {
        $result = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $image_file = Input::file("image");

        if($image_file->isValid())
        {
            $mime = $image_file->getMimeType();
            if(in_array($mime, ["image/jpeg", "image/png", "image/gif", "application/pdf"]))
            {
                $fileExtension = $image_file->getClientOriginalExtension();
                $fileName = uniqid().".".$fileExtension;
                $destinationPath = "../app/Templates/Default/Assets/faq/";
                $image_file->move($destinationPath, $fileName);

                if(File::exists(join("/", [$destinationPath, $fileName])))
                {
                    $result["success"] = true;
                    $result["ext"] = $fileExtension;
                    $result["url"] = template_url("faq/".$fileName);
                    echo json_encode($result);
                    exit;
                }
            }
            else
            {
                $result["error"] = __("wrong_image_format_error");
                echo json_encode($result);
                exit;
            }
        }
        else
        {
            $result["error"] = __("error_ocured");
            echo json_encode($result);
            exit;
        }
    }

    public function uploadSource() {
        $result = ["success" => false];

        if(!$this->_member->isGlAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $src = Input::get("src", "");
        $sourceZip = Input::file("file");

        if(isset($sourceZip) && $sourceZip->isValid() && trim($src) != "")
        {
            $srcArr = explode("|", $src);
            if(sizeof($srcArr) == 2)
            {
                $lang = $srcArr[0];
                $slug = $srcArr[1];

                $allowedLanguages = new Collection();
                foreach ($this->_member->adminGatewayLanguages as $gl) {
                    $allowedLanguages = $allowedLanguages->merge($gl->language->targetLanguages);
                }
                $allowedLanguage = $allowedLanguages->filter(function($language) use ($lang) {
                    return $language->langID == $lang;
                })->first();

                if($allowedLanguage)
                {
                    $mime = $sourceZip->getMimeType();
                    if($mime == "application/zip")
                    {
                        $format = in_array($slug, ["tn","tq","tw"]) ? "md" : "usfm";

                        $path = $this->_apiModel->processSourceZipFile($sourceZip, $format);

                        if($format == "usfm")
                        {
                            $result["success"] = $this->_apiModel->processUsfmSource($path, $lang, $slug);
                        }
                        else
                        {
                            $result["success"] = $this->_apiModel->processMdSource($path, $lang, $slug);
                        }

                        $result["message"] = "Uploaded!";
                    }
                    else
                    {
                        $result["error"] = __("error_zip_file_required");
                    }
                }
                else
                {
                    $result["error"] = __("not_enough_lang_rights_error");
                }
            }
            else
            {
                $result["error"] = __("wrong_parameters");
            }
        }
        else
        {
            $result["error"] = __("wrong_parameters");
        }

        echo json_encode($result);
        exit;
    }

    public function createFaq()
    {
        $result = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $question = Input::get("question", "");
        $category = Input::get("category", "common");
        $answer = Input::get("answer", "");

        if(trim($question) != "" && trim($answer) != ""
            && preg_match("/^common|vmast|vsail|level2|level3|notes|questions|words|lang_input$/", $category))
        {
            $data = [
                "title" => $question,
                "text" => $answer,
                "category" => $category
            ];

            $id = $this->_newsModel->createFaqs($data);

            if($id)
            {
                $li = '<li class="faq_content" id="'.$id.'">
                            <div class="tools_delete_faq">
                                <span>'.__("delete").'</span>
                                <img src="'.template_url("img/loader.gif").'">
                            </div>

                            <div class="faq_question">'.$question.'</div>
                            <div class="faq_answer">'.$answer.'</div>
                            <div class="faq_cat">'.__($category).'</div>
                        </li>';

                $result["success"] = true;
                $result["li"] = $li;
            }
        }

        echo json_encode($result);
    }


    public function deleteFaq()
    {
        $result = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $questionID = Input::get("id", 0);

        if($questionID)
        {
            if($this->_newsModel->deleteFaqs(["id" => $questionID]))
            {
                $result["success"] = true;
            }
        }

        echo json_encode($result);
    }


    public function createNews()
    {
        $result = ["success" => false];

        if(!$this->_member->isSuperAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $title = Input::get("title", "");
        $category = Input::get("category", "common");
        $text = Input::get("text", "");

        if(trim($title) != "" && trim($text) != ""
            && preg_match("/^common|vmast|vsail|level2|level3|notes|questions|words|lang_input$/", $category))
        {
            $data = [
                "title" => $title,
                "category" => $category,
                "text" => $text,
                "created_at" => date("Y-m-d H:i:s", time())
            ];

            if($this->_newsModel->createNews($data))
            {
                $result["success"] = true;
                $result["msg"] = __("successfully_created");
            }
            else
            {
                $result["error"] = __("error_ocured");
            }
        }
        else
        {
            $result["error"] = __("wrong_parameters");
        }

        echo json_encode($result);
    }


    public function getEventProgress($eventID) {
        $result = ["success" => false, "progress" => 0];

        if(!$this->_member->isGlAdmin() && $this->_member->isProjectAdmin()) {
            $result["error"] = __("not_loggedin_error");
            echo json_encode($result);
            exit;
        }

        $event = $this->eventRepo->get($eventID);
        if ($event) {
            switch ($event->state) {
                case EventStates::L2_RECRUIT:
                case EventStates::L2_CHECK:
                case EventStates::L2_CHECKED:
                    $level = "l2";
                    break;

                case EventStates::L3_RECRUIT:
                case EventStates::L3_CHECK:
                case EventStates::COMPLETE:
                    $level = "l3";
                    break;

                default:
                    $level = "l1";
                    break;
            }
            $result["progress"] = $this->eventRepo->calculateEventProgress($event, $level);
        }

        $result["success"] = true;

        echo json_encode($result);

    }

    public function createCustomSource() {
        $result = ["success" => false];

        if(!$this->_member->isGlAdmin())
        {
            $result["error"] = __("not_enough_rights_error");
            echo json_encode($result);
            exit;
        }

        $lang = Input::get("lang", "");
        $type = Input::get("type", "");

        if(trim($lang) != "" && trim($type)) {
            $allowedLanguages = new Collection();
            foreach ($this->_member->adminGatewayLanguages as $gl) {
                $allowedLanguages = $allowedLanguages->merge($gl->language->targetLanguages);
            }
            $allowedLanguage = $allowedLanguages->filter(function($language) use ($lang) {
                return $language->langID == $lang;
            })->first();

            if($allowedLanguage)
            {
                $typeArr = explode("|", $type);
                if(sizeof($typeArr) == 2) {
                    $slug = $typeArr[0];
                    $name = $typeArr[1];

                    if(preg_match("/[a-z-]+/", $slug)) {
                        try {
                            $postData = [
                                "langID" => $lang,
                                "slug" => $slug,
                                "name" => $name
                            ];
                            $this->sourceRepo->create($postData);

                            $result["success"] = true;
                            $result["message"] = __("successfully_created");
                        } catch(QueryException $e) {
                            $result["success"] = true;
                        }
                    } else {
                        $result["error"] = __("Only english letters and hyphens are allowed for the source slug");
                    }
                } else {
                    $result["error"] = __("wrong_parameters");
                }
            } else {
                $result["error"] = __("not_enough_lang_rights_error");
            }
        } else {
            $result["error"] = __("wrong_parameters");
        }

        echo json_encode($result);
    }



    // ----------------- Migration functions -------------------- //

    /**
     * Migrate to version v.6.9.0
     */
    function migrate8steps()
    {
        $data["menu"] = 1;

        if (Session::get("isSuperAdmin")) {
            $translators = $this->_eventsModel->getMembersForProject(["ulb", "udb"]);

            try {
                foreach ($translators as $translator) {
                    $trID = $translator->trID;
                    $step = $translator->step;
                    $currentChapter = $translator->currentChapter;
                    $checkerID = $translator->checkerID;
                    $checkDone = $translator->checkDone;
                    $verbCheck = (array)json_decode($translator->verbCheck, true);
                    $peerCheck = (array)json_decode($translator->peerCheck, true);
                    $kwCheck = (array)json_decode($translator->kwCheck, true);
                    $crCheck = (array)json_decode($translator->crCheck, true);
                    $otherCheck = (array)json_decode($translator->otherCheck, true);

                    // Migrate completed chapters
                    $tmpVerb = [];
                    $tmpPeer = [];
                    $tmpKw = [];
                    $tmpCr = [];
                    foreach ($verbCheck as $chapter => $member) {
                        $tmpVerb[$chapter] = is_array($member) ? $member : ["memberID" => $member, "done" => 1];
                    }
                    $verbCheck = $tmpVerb;
                    foreach ($peerCheck as $chapter => $member) {
                        $tmpPeer[$chapter] = is_array($member) ? $member : ["memberID" => $member, "done" => 2];
                    }
                    $peerCheck = $tmpPeer;
                    unset($tmpPeer);
                    foreach ($kwCheck as $chapter => $member) {
                        $tmpKw[$chapter] = is_array($member) ? $member : ["memberID" => $member, "done" => 2];
                    }
                    $kwCheck = $tmpKw;
                    unset($tmpKw);
                    foreach ($crCheck as $chapter => $member) {
                        $tmpCr[$chapter] = is_array($member) ? $member : ["memberID" => $member, "done" => 2];
                    }
                    $crCheck = $tmpCr;
                    unset($tmpCr);

                    // Migrate in-progress chapters
                    if ($step == EventSteps::PEER_REVIEW && $currentChapter > 0) {
                        if ($checkerID > 0 && $checkDone) {
                            $peerCheck[$currentChapter] = ["memberID" => $checkerID, "done" => 1];
                        } else if ($checkerID > 0 && !$checkDone) {
                            $peerCheck[$currentChapter] = ["memberID" => $checkerID, "done" => 0];
                        } else {
                            $peerCheck[$currentChapter] = ["memberID" => 0, "done" => 0];
                        }
                    }
                    if ($step == EventSteps::KEYWORD_CHECK && $currentChapter > 0) {
                        if ($checkerID > 0 && $checkDone) {
                            $kwCheck[$currentChapter] = ["memberID" => $checkerID, "done" => 1];
                        } else if ($checkerID > 0 && !$checkDone) {
                            $kwCheck[$currentChapter] = ["memberID" => $checkerID, "done" => 0];
                        } else {
                            $kwCheck[$currentChapter] = ["memberID" => 0, "done" => 0];
                        }
                    }
                    if ($step == EventSteps::CONTENT_REVIEW && $currentChapter > 0) {
                        if ($checkerID > 0 && $checkDone) {
                            $crCheck[$currentChapter] = ["memberID" => $checkerID, "done" => 1];
                        } else if ($checkerID > 0 && !$checkDone) {
                            $crCheck[$currentChapter] = ["memberID" => $checkerID, "done" => 0];
                        } else {
                            $crCheck[$currentChapter] = ["memberID" => 0, "done" => 0];
                        }
                    }

                    // Migrate Final Review step
                    foreach ($crCheck as $chapter => $data) {
                        if ($step != EventSteps::FINAL_REVIEW) {
                            if ($data["done"] == 2)
                                $otherCheck[$chapter] = ["memberID" => 0, "done" => 1];
                        } else {
                            $otherCheck[$chapter] = ["memberID" => 0, "done" => 0];
                        }
                    }

                    $updated = $this->_eventsModel->updateTranslator(
                        [
                            "verbCheck" => json_encode($verbCheck),
                            "peerCheck" => json_encode($peerCheck),
                            "kwCheck" => json_encode($kwCheck),
                            "crCheck" => json_encode($crCheck),
                            "otherCheck" => json_encode($otherCheck)
                        ],
                        ["trID" => $trID]
                    );

                    pr("------------");
                    pr("trID: " . $trID . ", updated: " . ($updated ? "TRUE" : "FALSE"));
                }
            } catch (\ErrorException $e) {
                pr("There was an error or already migrated");
                pr("Reason: " . $e->getMessage());
            }
        }
    }

    /**
     * Migrate to version v.7.0.0
     */
    function migrateAdmins()
    {
        $data["menu"] = 1;

        if (Session::get("isSuperAdmin")) {

            $glAdmins = $this->_eventsModel->getSuperadminLanguages();
            $admins = $this->_eventsModel->getAdminLanguages();

            $membersCache = [];
            $glProjectsCache = [];

            try {
                pr("Reassigning GL Project admins and Project admins");
                foreach ($glAdmins as $admin) {
                    $glProjectObj = null;
                    if (!array_key_exists($admin->glID, $glProjectsCache)) {
                        $glProjectObj = $this->glRepo->get($admin->glID);
                        $glProjectsCache[$admin->glID] = $glProjectObj;
                    } else {
                        $glProjectObj = $glProjectsCache[$admin->glID];
                    }

                    $members = (array)json_decode($admin->admins, true);
                    foreach ($members as $member) {
                        $memberObj = null;
                        if (!array_key_exists($member, $membersCache)) {
                            $memberObj = $this->memberRepo->get($member);
                            $membersCache[$member] = $memberObj;
                        } else {
                            $memberObj = $membersCache[$member];
                        }

                        $memberObj->adminGatewayLanguages()->attach($glProjectObj);

                        // Assign GL project admins to project admins
                        foreach ($glProjectObj->projects as $project) {
                            $memberObj->adminProjects()->attach($project);
                        }
                    }
                }

                pr("Reassigning Event admins");
                foreach ($admins as $admin) {
                    $members = (array)json_decode($admin->admins, true);
                    $members = array_merge($members, (array)json_decode($admin->admins_l2, true));
                    $members = array_merge($members, (array)json_decode($admin->admins_l3, true));
                    $members = array_unique($members);

                    foreach ($members as $member) {
                        $memberObj = null;
                        if (!array_key_exists($member, $membersCache)) {
                            $memberObj = $this->memberRepo->get($member);
                            $membersCache[$member] = $memberObj;
                        } else {
                            $memberObj = $membersCache[$member];
                        }

                        $memberObj->adminEvents()->attach($admin->eventID);
                    }
                }

                pr("Migration completed!");
            } catch (\Exception $e) {
                pr("There was an error or already migrated");
                pr("Reason: " . $e->getMessage());
            }
        }
    }
}
