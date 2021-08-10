<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\CloudModel;
use App\Models\NewsModel;
use App\Repositories\Event\IEventRepository;
use App\Repositories\Language\ILanguageRepository;
use App\Repositories\Member\IMemberRepository;
use View;
use Helpers\Csrf;
use Helpers\Gump;
use Mailer;
use Config\Config;
use Helpers\Password;
use Helpers\ReCaptcha;
use Helpers\Session;
use Helpers\Url;
use App\Models\EventsModel;
use App\Models\MembersModel;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class MembersController extends Controller
{
    private $_model;
    private $_eventModel;
    private $_newsModel;
    private $_notifications;
    private $_news;
    private $_newNewsCount;

    protected $memberRepo = null;
    protected $languageRepo = null;
    protected $eventsRepo = null;

    private $_member = null;

    public function __construct(
        IMemberRepository $memberRepo,
        ILanguageRepository $languageRepo,
        IEventRepository $eventsRepo
    )
    {
        parent::__construct();

        $this->memberRepo = $memberRepo;
        $this->languageRepo = $languageRepo;
        $this->eventsRepo = $eventsRepo;

        if(Config::get("app.isMaintenance")
            && !in_array($_SERVER['REMOTE_ADDR'], Config::get("app.ips")))
        {
            Url::redirect("maintenance");
        }

        $this->_model = new MembersModel();

        $this->_eventModel = new EventsModel($this->eventsRepo);
        $this->_newsModel = new NewsModel();

        if(Session::get("memberID"))
        {
            $this->_member = $this->memberRepo->get(Session::get("memberID"));
            $this->_notifications = $this->_eventModel->getNotifications();
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

        if (!$this->_member) Url::redirect("members/login");

        if (!$this->_member->profile->complete) Url::redirect("members/profile");

        Url::redirect("events");
    }

    /**
     * Show profile view with form
     * @return mixed
     */
    public function profile()
    {
        $data["menu"] = 1;
        $data["errors"] = array();

        if (!$this->_member) Url::redirect('members/login');

        $allLanguages = $this->languageRepo->all();

        $profile = $this->_member->profile;

        if(!empty($_POST))
        {
            $_POST = Gump::xss_clean($_POST);

            $userName = isset($_POST["userName"]) ? $_POST['userName'] : "";
            $firstName = isset($_POST["firstName"]) ? $_POST['firstName'] : "";
            $lastName = isset($_POST["lastName"]) ? $_POST['lastName'] : "";

            $avatar = isset($_POST["avatar"]) && preg_match("/^([f|m][1-9]|[f|m]1[0-9]|[f|m]20)$/", $_POST["avatar"]) ? $_POST["avatar"] : "m1";
            $prefered_roles = isset($_POST["prefered_roles"]) && !empty($_POST["prefered_roles"]) ? (array)$_POST["prefered_roles"] : null;
            $langs = isset($_POST["langs"]) && !empty($_POST["langs"]) ? (array)$_POST["langs"] : null;
            $bbl_trans_yrs = isset($_POST["bbl_trans_yrs"]) && preg_match("/^[1-4]{1}$/", $_POST["bbl_trans_yrs"]) ? $_POST["bbl_trans_yrs"] : null;
            $othr_trans_yrs = isset($_POST["othr_trans_yrs"]) && preg_match("/^[1-4]{1}$/", $_POST["othr_trans_yrs"]) ? $_POST["othr_trans_yrs"] : null;
            $bbl_knwlg_degr = isset($_POST["bbl_knwlg_degr"]) && preg_match("/^[1-4]{1}$/", $_POST["bbl_knwlg_degr"]) ? $_POST["bbl_knwlg_degr"] : null;
            $mast_evnts = isset($_POST["mast_evnts"]) && preg_match("/^[1-4]{1}$/", $_POST["mast_evnts"]) ? $_POST["mast_evnts"] : null;
            $mast_role = isset($_POST["mast_role"]) && !empty($_POST["mast_role"]) ? (array)$_POST["mast_role"] : ($mast_evnts > 1 ? null : array());
            $teamwork = isset($_POST["teamwork"]) && preg_match("/^[1-4]{1}$/", $_POST["teamwork"]) ? $_POST["teamwork"] : null;

            $mast_facilitator = isset($_POST["mast_facilitator"]) && preg_match("/^[0-1]{1}$/", $_POST["mast_facilitator"]) ? $_POST["mast_facilitator"] : 0;
            $org = isset($_POST["org"]) && preg_match("/^(Other|WA EdServices)$/", $_POST["org"]) ? $_POST["org"] : ($mast_facilitator ? null : "");
            $ref_person = isset($_POST["ref_person"]) && trim($_POST["ref_person"]) != "" ? trim($_POST["ref_person"]) : ($mast_facilitator ? null : "");
            $ref_email = isset($_POST["ref_email"]) && trim($_POST["ref_email"]) != "" ? trim($_POST["ref_email"]) : ($mast_facilitator ? null : "");

            $church_role = isset($_POST["church_role"]) && !empty($_POST["church_role"]) ? (array)$_POST["church_role"] : array();
            $hebrew_knwlg = isset($_POST["hebrew_knwlg"]) && preg_match("/^[0-4]{1}$/", $_POST["hebrew_knwlg"]) ? $_POST["hebrew_knwlg"] : 0;
            $greek_knwlg = isset($_POST["greek_knwlg"]) && preg_match("/^[0-4]{1}$/", $_POST["greek_knwlg"]) ? $_POST["greek_knwlg"] : 0;
            $education = isset($_POST["education"]) && !empty($_POST["education"]) ? (array)$_POST["education"] : array();
            $ed_area = isset($_POST["ed_area"]) && !empty($_POST["ed_area"]) ? (array)$_POST["ed_area"] : array();
            $ed_place = isset($_POST["ed_place"]) && trim($_POST["ed_place"]) != "" ? trim($_POST["ed_place"]) : "";

            if(!preg_match("/^[a-z]+[a-z0-9]*$/i", $userName))
            {
                $data["errors"]['userName'] = true;
            }

            if (strlen($userName) < 5 || strlen($userName) > 20)
            {
                $data["errors"]['userName'] = true;
            }

            if (mb_strlen($firstName) < 2 || mb_strlen($firstName) > 20)
            {
                $data["errors"]['firstName'] = true;
            }

            if (mb_strlen($lastName) < 2 || mb_strlen($lastName) > 20)
            {
                $data["errors"]['lastName'] = true;
            }

            if($prefered_roles == null)
            {
                $data["errors"]["prefered_roles"] = true;
            }

            if($langs !== null)
            {
                $languages = array();
                $langArr = array();
                foreach ($langs as $lang) {
                    $arr = explode(":", $lang);
                    $arr[2] = 4; // To support old version, when geo years were saved

                    if(sizeof($arr) != 3) continue;

                    $langID = preg_match("/^[0-9A-Za-z-]{2,40}$/", $arr[0]) ? $arr[0] : null;

                    if($langID === null || (integer)$arr[1] < 0 || (integer)$arr[2] == 0) continue;
                    if((integer)$arr[1] > 5 || (integer)$arr[2] > 4) continue;

                    $languages[$langID] = array((integer)$arr[1], (integer)$arr[2]);

                    $langArr[$langID]["lang_fluency"] = (integer)$arr[1];
                    $langArr[$langID]["geo_lang_yrs"] = (integer)$arr[2];
                }

                if(sizeof($languages) <= 0)
                {
                    $data["errors"]["langs"] = true;
                }
            }
            else
            {
                $data["errors"]["langs"] = true;
            }

            if($bbl_trans_yrs === null)
                $data["errors"]["bbl_trans_yrs"] = true;

            if($othr_trans_yrs === null)
                $data["errors"]["othr_trans_yrs"] = true;

            if($bbl_knwlg_degr === null)
                $data["errors"]["bbl_knwlg_degr"] = true;

            if($mast_evnts === null)
                $data["errors"]["mast_evnts"] = true;

            if($mast_role === null)
                $data["errors"]["mast_role"] = true;
            else
            {
                foreach ($mast_role as $item) {
                    if(!preg_match("/^(translator|facilitator|l2_checker|l3_checker)$/", $item))
                    {
                        $data["errors"]["mast_role"] = true;
                        break;
                    }
                }
            }

            if($teamwork === null)
            {
                $data["errors"]["teamwork"] = true;
            }

            if(!empty($education))
            {
                foreach ($education as $item) {
                    if(!preg_match("/^(BA|MA|PHD)$/", $item))
                    {
                        $data["errors"]["education"] = true;
                        break;
                    }
                }
            }

            if($org === null)
                $data["errors"]["org"] = true;

            if($ref_person === null)
                $data["errors"]["ref_person"] = true;

            if($ref_email === null)
                $data["errors"]["ref_email"] = true;
            elseif($ref_email != "" && !filter_var($ref_email, FILTER_VALIDATE_EMAIL))
                $data["errors"]["ref_email"] = true;

            if($ed_area === null)
                $data["errors"]["ed_area"] = true;
            else
            {
                foreach ($ed_area as $item) {
                    if(!preg_match("/^(Theology|Pastoral Ministry|Bible Translation|Exegetics)$/", $item))
                    {
                        $data["errors"]["ed_area"] = true;
                        break;
                    }
                }
            }

            if($ed_place === null)
                $data["errors"]["ed_place"] = true;

            if($hebrew_knwlg === null)
                $data["errors"]["hebrew_knwlg"] = true;

            if($greek_knwlg === null)
                $data["errors"]["greek_knwlg"] = true;

            if($church_role === null)
                $data["errors"]["church_role"] = true;
            else
            {
                foreach ($church_role as $item) {
                    if(!preg_match("/^(Elder|Bishop|Pastor|Teacher|Denominational Leader|Seminary Professor)$/", $item))
                    {
                        $data["errors"]["church_role"] = true;
                        break;
                    }
                }
            }

            if(empty($data["errors"]))
            {
                $this->_member->userName = $userName;
                $this->_member->firstName = $firstName;
                $this->_member->lastName = $lastName;
                $this->_member->save();

                $profile->avatar = $avatar;
                $profile->prefered_roles = json_encode($prefered_roles);
                $profile->languages = json_encode($languages);
                $profile->bbl_trans_yrs = $bbl_trans_yrs;
                $profile->othr_trans_yrs = $othr_trans_yrs;
                $profile->bbl_knwlg_degr = $bbl_knwlg_degr;
                $profile->mast_evnts = $mast_evnts;
                $profile->mast_role = json_encode($mast_role);
                $profile->teamwork = $teamwork;
                $profile->mast_facilitator = $mast_facilitator;
                $profile->org = $org;
                $profile->ref_person = $ref_person;
                $profile->ref_email = $ref_email;
                $profile->education = json_encode($education);
                $profile->ed_area = json_encode($ed_area);
                $profile->ed_place = $ed_place;
                $profile->hebrew_knwlg = $hebrew_knwlg;
                $profile->greek_knwlg = $greek_knwlg;
                $profile->church_role = json_encode($church_role);
                $profile->complete = true;
                $profile->save();

                Session::set("userName", $userName);
                Session::set("firstName", $firstName);
                Session::set("lastName", $lastName);

                Session::set("success", __("update_profile_success"));

                Url::redirect("members/profile");
            }
            else
            {
                $error[] = __('required_fields_empty_error');
            }
        }

        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;
        $data['csrfToken'] = Csrf::makeToken();

        return View::make('Members/Profile')
            ->shares("title", __("profile_message"))
            ->shares("data", $data)
            ->shares("member", $this->_member)
            ->shares("profile", $profile)
            ->shares("languages", $allLanguages)
            ->shares("error", @$error);
    }

    /**
     * Show public profile view
     * @return mixed
     */
    public function publicProfile($memberID)
    {
        if (!$this->_member) Url::redirect('members/login');

        if (!$this->_member->isSuperAdmin()
            && !$this->_member->isGlAdmin()
            && !$this->_member->isProjectAdmin()
            && !$this->_member->isBookAdmin()) {
            Url::redirect('events');
        }

        $data["menu"] = 1;
        $data["errors"] = array();
        $publicMember = $this->memberRepo->get($memberID);
        $profile = $publicMember->profile;

        $profile->proj_lang = $this->languageRepo->get($profile->proj_lang);
        $profile->projects = (array)json_decode($profile->projects, true);
        $profileLangs = (array)json_decode($profile->languages, true);

        $profile->languages = $this->languageRepo->all()
            ->filter(function ($lang) use ($profileLangs) {
                return in_array($lang->langID, array_keys($profileLangs));
            })
            ->map(function($lang) use ($profileLangs) {
                $lang->fluency = $profileLangs[$lang->langID][0];
                return $lang;
            });
        $profile->prefered_roles = (array)json_decode($profile->prefered_roles, true);
        $profile->mast_role = (array)json_decode($profile->mast_role, true);
        $profile->church_role = (array)json_decode($profile->church_role, true);
        $profile->education = (array)json_decode($profile->education, true);
        $profile->ed_area = (array)json_decode($profile->ed_area, true);

        $data["facilitation_activities"] = $publicMember->adminEvents;
        $data["translation_activities"] = $publicMember->translators;

        $l2_check_activities = $publicMember->checkL2Events;
        $l3_check_activities = $publicMember->checkL3Events;

        $data["checking_activities"] = [];

        // Translation and level 1 check (ulb, udb, notes, tq, tw, sun)
        foreach ($data["translation_activities"] as $translation_activity) {
            $chapters = $translation_activity->chapters;

            $chaps = [];
            if ($chapters) {
                foreach ($chapters as $chapter) {
                    $chaps[] = $chapter->chapter;
                }
            }
            $chaps = array_map(function ($elm) {
                return $elm > 0 ? $elm : __("intro");
            }, $chaps);

            $translation_activity->chapters = join(", ", array_values($chaps));

            $checking = $this->_eventModel->getMemberEvents(null, $translation_activity->eventID, true, true);
            $chaps = [];

            foreach ($checking as $check) {
                if (in_array($check->bookProject, ["ulb", "udb"])) {
                    // Level 1 (ulb, udb) checking
                    $verbCheck = (array)json_decode($check->verbCheck, true);
                    $peerCheck = (array)json_decode($check->peerCheck, true);
                    $kwCheck = (array)json_decode($check->kwCheck, true);
                    $crCheck = (array)json_decode($check->crCheck, true);

                    foreach ($verbCheck as $chapter => $memID)
                        if ($memberID == $memID)
                            $chaps[] = $chapter;

                    foreach ($peerCheck as $chapter => $memID)
                        if ($memberID == $memID)
                            $chaps[] = $chapter;

                    foreach ($kwCheck as $chapter => $memID)
                        if ($memberID == $memID)
                            $chaps[] = $chapter;

                    foreach ($crCheck as $chapter => $memID)
                        if ($memberID == $memID)
                            $chaps[] = $chapter;
                } else {
                    $peerCheck = (array)json_decode($check->peerCheck, true);
                    $kwCheck = (array)json_decode($check->kwCheck, true);
                    $crCheck = (array)json_decode($check->crCheck, true);
                    $otherCheck = (array)json_decode($check->otherCheck, true);

                    foreach ($peerCheck as $chapter => $member_data)
                        if ($memberID == $member_data["memberID"])
                            $chaps[] = $chapter;

                    foreach ($kwCheck as $chapter => $member_data)
                        if ($memberID == $member_data["memberID"])
                            $chaps[] = $chapter;

                    foreach ($crCheck as $chapter => $member_data)
                        if ($memberID == $member_data["memberID"])
                            $chaps[] = $chapter;

                    foreach ($otherCheck as $chapter => $member_data)
                        if ($memberID == $member_data["memberID"])
                            $chaps[] = $chapter;
                }
            }

            $chaps = array_unique($chaps);
            sort($chaps);
            $chaps = array_map(function ($elm) {
                return $elm > 0 ? $elm : __("intro");
            }, $chaps);

            if (!empty($chaps)) {
                $checking[0]->chapters = join(", ", array_values($chaps));
                $data["checking_activities"][] = $checking[0];
            }
        }

        // Level 2 checking (ulb, udb)
        foreach ($l2_check_activities as $checking_activity) {
            $chapters = $checking_activity->chapters;

            // First checker
            $chaps = [];
            if ($chapters) {
                foreach ($chapters as $chapter) {
                    $chaps[] = $chapter->chapter;
                }
            }

            // Second checker
            $checking = $this->_eventModel->getMemberEventsForCheckerL2(null, $checking_activity->eventID);
            foreach ($checking as $check) {
                $sndCheck = (array)json_decode($check->sndCheck, true);
                $peer1Check = (array)json_decode($check->peer1Check, true);
                $peer2Check = (array)json_decode($check->peer2Check, true);

                foreach ($sndCheck as $chapter => $member_data)
                    if ($memberID == $member_data["memberID"])
                        $chaps[] = $chapter;

                foreach ($peer1Check as $chapter => $member_data)
                    if ($memberID == $member_data["memberID"])
                        $chaps[] = $chapter;

                foreach ($peer2Check as $chapter => $member_data)
                    if ($memberID == $member_data["memberID"])
                        $chaps[] = $chapter;
            }

            $chaps = array_unique($chaps);
            sort($chaps);

            $checking_activity->chapters = join(", ", array_values($chaps));
            $data["checking_activities"][] = $checking_activity;
        }

        // Checking level 3 check (ulb, udb, notes, tq, tw)
        foreach ($l3_check_activities as $checking_activity) {
            $chapters = $checking_activity->chapters;

            // First checker
            $chaps = [];
            if ($chapters) {
                foreach ($chapters as $chapter) {
                    $chaps[] = $chapter->chapter;
                }
            }

            // Second checker
            $checking = $this->_eventModel->getMemberEventsForCheckerL3(null, $checking_activity->eventID);
            foreach ($checking as $check) {
                $peerCheck = (array)json_decode($check->peerCheck, true);

                foreach ($peerCheck as $chapter => $member_data)
                    if($memberID == $member_data["memberID"])
                        $chaps[] = $chapter;
            }

            $chaps = array_unique($chaps);
            sort($chaps);
            $chaps = array_map(function ($elm) {
                return $elm > 0 ? $elm : __("intro");
            }, $chaps);

            $checking_activity->chapters = join(", ", array_values($chaps));
            $data["checking_activities"][] = $checking_activity;
        }

        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;

        return View::make('Members/PublicProfile')
            ->shares("title", __("member_profile_message"))
            ->shares("member", $publicMember)
            ->shares("profile", $profile)
            ->shares("data", $data)
            ->shares("error", @$error);
    }


    public function search()
    {
        $member = $this->memberRepo->get(Session::get('memberID'));

        if (!$member) Url::redirect('members/login');

        if(Session::get("isSuperAdmin") && !isset($_POST["ext"]))
        {
            Url::redirect('admin/members');
        }

        if(!Session::get("isBookAdmin") && !isset($_POST["ext"]))
        {
            Url::redirect('events');
        }

        $data["menu"] = 4;

        if(!empty($_POST))
        {
            $response = ["success" => false];

            $_POST = Gump::xss_clean($_POST);

            $name = isset($_POST["name"]) && $_POST["name"] != "" ? $_POST["name"] : false;
            $role = isset($_POST["role"]) && preg_match("/^(translators|facilitators|all)$/", $_POST["role"]) ? $_POST["role"] : "all";
            $language = isset($_POST["language"]) && $_POST["language"] != "" ? $_POST["language"] : false;
            $page = isset($_POST["page"]) ? (integer)$_POST["page"] : 1;
            $searchExt = $_POST["ext"] ?? false;
            $verified = $_POST["verified"] ?? false;

            if($name || $role || $language)
            {
                if($language)
                {
                    $count = $this->_model->searchMembers($name, $role, [$language], true, false, $verified);
                    $members = $this->_model->searchMembers($name, $role, [$language], false, true, $verified, $page);
                }
                else
                {
                    $count = $this->_model->searchMembers($name, $role, [], true, false, $verified);
                    $members = $this->_model->searchMembers($name, $role, [], false, true, $verified, $page);
                }

                $response["success"] = true;
                $response["count"] = $count;
                $response["members"] = $members;
            }
            else
            {
                $response["error"] = __("choose_filter_option");
            }

            echo json_encode($response);
            exit;
        }
        else
        {
            if(empty($admLangs))
            {
                Url::redirect('events');
            }
        }

        $data["languages"] = $this->languageRepo->all();

        $data["count"] = $this->_model->searchMembers(null, "all", [], true);
        $data["members"] = $this->_model->searchMembers(null, "all", [], false, true);

        $data["notifications"] = $this->_notifications;
        $data["newNewsCount"] = $this->_newNewsCount;

        return View::make('Members/Search')
            ->shares("title", __("admin_members_title"))
            ->shares("data", $data);
    }

    /**
     * Show activation view
     * @param $memberID
     * @param $activationToken
     * @return mixed
     */
    public function activate($memberID, $activationToken)
    {
        if (Session::get('memberID'))
        {
            Url::redirect('members');
        }

        if (($memberID > 0) && (strlen($activationToken) == 32))
        {
            $user = $this->_model->getMember(["memberID", "active"], [
                ["memberID", $memberID],
                ["active", false],
                ["activationToken", $activationToken]
            ]);

            if (!$user || $user[0]->memberID == 0)
            {
                $error[] = __('no_account_error');
            }
            elseif ($user[0]->active == true)
            {
                $error[] = __('account_activated_error');
            }
            else
            {
                $postdata = [
                    "active" => true,
                    "verified" => true,
                    "activationToken" => null
                ];
                $where = array('memberID' => $memberID);
                $this->_model->updateMember($postdata, $where);

                $msg = __('account_activated_success');
                Session::set('success', $msg);

                Session::destroy("activation_email");

                Url::redirect('members/success');
            }
        }
        else
        {
            $error[] = __('invalid_link_error');
        }

        return View::make('Members/Activate')
            ->shares("title", __("activate_account_title"))
            ->shares("error", @$error);
    }

    /**
     * Show resend activation instructions view with form
     * @param $email
     * @return mixed
     */
    public function resendActivation($email)
    {
        $data = $this->_model->getMember(array("memberID", "email", "activationToken", "userName"),
            array(
                array("email", $email),
                array("active", "!=", true)
        ));

        if(!empty($data))
        {
            Mailer::send('Emails/Auth/Activate', ["memberID" => $data[0]->memberID, "token" => $data[0]->activationToken], function($message) use($data)
            {
                $message->to($data[0]->email, $data[0]->userName)
                    ->subject(__('activate_account_title'));
            });

            $activationToken = md5(uniqid(rand(), true));
            $this->_model->updateMember(["activationToken" => $activationToken], ["email" => $email]);

            $msg = __('resend_activation_success_message');
            Session::set('success', $msg);
        }
        else
        {
            $error[] = __("wrong_activation_email");
        }

        $data['menu'] = 5;

        return View::make('Members/EmailActivation')
            ->shares("title", __("resend_activation_title"))
            ->shares("data", $data)
            ->shares("error", @$error);
    }

    /**
     * Show login view with form
     * @return mixed
     */
    public function login()
    {
        if (Session::get('memberID'))
        {
            Url::redirect('members');
        }

        if (isset($_POST['submit']))
        {
            if (!Csrf::isTokenValid())
            {
                Url::redirect('members/login');
            }

            if(Config::get("app.type") == "remote")
            {
                $loginTry = Session::get('loginTry');
                if($loginTry == null) $loginTry = 0;
                $loginTry++;

                Session::set('loginTry', $loginTry);

                if($loginTry >= 3)
                {
                    if($loginTry > 3)
                    {
                        if (!ReCaptcha::check())
                        {
                            $error[] = __('captcha_wrong');
                        }
                    }
                }
            }

            if(!isset($error))
            {
                $member = $this->memberRepo->getByEmailOrUserName($_POST['email']);

                if ($member) {
                    if ($member->blocked) Url::redirect('members/login');

                    if (Password::verify($_POST['password'], $member->password) ||
                        (Config::get("app.type") == "local" && !$member->isSuperAdmin())) {

                        if ($member->active) {
                            $authToken = md5(uniqid(rand(), true));

                            $member->authToken = $authToken;
                            $member->logins = $member->logins + 1;
                            $updated = $this->memberRepo->save($member);

                            if ($updated) {
                                Session::set('memberID', $member->memberID);
                                Session::set('userName', $member->userName);
                                Session::set('firstName', $member->firstName);
                                Session::set('lastName', $member->lastName);
                                Session::set('email', $member->email);
                                Session::set('authToken', $authToken);
                                Session::set('verified', $member->verified);
                                Session::set('isSuperAdmin', $member->isSuperAdmin());
                                Session::set('isGlAdmin', $member->isGlAdmin());
                                Session::set('isProjectAdmin', $member->isProjectAdmin());
                                Session::set('isBookAdmin', $member->isBookAdmin());

                                Session::destroy('loginTry');

                                if(Session::get('redirect') != null)
                                {
                                    Url::redirect(Session::get('redirect'));
                                }
                                else
                                {
                                    Url::redirect('members');
                                }
                            } else {
                                $error[] = __('user_login_error');
                            }
                        } else {
                            $error[] = __('not_activated_email', ["email" => $member->email]);
                        }
                    } else {
                        $error[] = __('wrong_credentials_error');
                    }
                } else {
                    $error[] = __('wrong_credentials_error');
                }
            }
        }

        $data["menu"] = 5;
        $data['csrfToken'] = Csrf::makeToken();

        return View::make('Members/Login')
            ->shares("title", __("login_title"))
            ->shares("data", $data)
            ->shares("error", @$error);
    }

    /**
     * Show signup view with form
     * @return mixed
     */
    public function signup()
    {
        // Registration
        $data["menu"] = 5;
        $languages = $this->languageRepo->all();

        if (Session::get('memberID')) Url::redirect("events");

        if (isset($_POST['submit']))
        {
            $_POST = Gump::xss_clean($_POST);

            $_POST = Gump::filter_input($_POST, [
                'userName' => 'trim',
                'firstName' => 'trim',
                'lastName' => 'trim',
                'email' => 'trim',
                'password' => 'trim'
            ]);

            $userName = $_POST['userName'];
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $passwordConfirm = $_POST['passwordConfirm'];
            $tou = isset($_POST['tou']) && (int)$_POST['tou'] == 1;
            $sof = isset($_POST['sof']) && (int)$_POST['sof'] == 1;
            $projects = $_POST['projects'];
            $projLang = $_POST['proj_lang'];

            if(!preg_match("/^[a-z]+[a-z0-9]*$/i", $userName))
            {
                $error['userName'] = __('userName_characters_error');
            }

            if (strlen($userName) < 5 || strlen($userName) > 20)
            {
                $error['userName'] = __('userName_length_error');
            }

            if (mb_strlen($firstName) < 2 || mb_strlen($firstName) > 20)
            {
                $error['firstName'] = __('firstName_length_error');
            }

            if (mb_strlen($lastName) < 2 || mb_strlen($lastName) > 20)
            {
                $error['lastName'] = __('lastName_length_error');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $error['email'] = __('enter_valid_email_error');
            }
            else
            {
                $byEmail = $this->memberRepo->getByEmail($email);
                if ($byEmail) $error['email'] = __('email_taken_error');

                $byUserName = $this->memberRepo->getByUsername($userName);
                if ($byUserName) $error['userName'] = __('username_taken_error');
            }

            if (strlen($password) < 6)
            {
                $error['password'] = __('password_short_error');
            }
            elseif ($password != $passwordConfirm)
            {
                $error['confirm'] = __('passwords_notmatch_error');
            }

            if(Config::get("app.type") == "remote")
            {
                if (!ReCaptcha::check())
                {
                    $error['recaptcha'] = __('captcha_wrong');
                }
            }

            if(!$tou)
            {
                $error['tou'] = __('tou_accept_error');
            }

            if(!$sof)
            {
                $error['sof'] = __('sof_accept_error');
            }

            if(!in_array($projects, ["vmast","vsail","odb","l2","l3","tn","tq","tw"])) {
                $error["projects"] = __("projects_empty_error");
            }

            if(!preg_match("/^[0-9A-Za-z-]{2,40}$/", $projLang)) {
                $error["proj_lang"] = __("proj_lang_empty_error");
            }

            if (!isset($error))
            {
                $activationToken = md5(uniqid(rand(), true));
                $hash = Password::make($password);

                //insert
                $postdata = [
                    "userName" => $userName,
                    "firstName" => $firstName,
                    "lastName" => $lastName,
                    "email" => $email,
                    "password" => $hash,
                    "activationToken" => $activationToken,
                ];

                if(Config::get("app.type") == "local")
                {
                    $postdata["active"] = true;
                    $postdata["verified"] = true;
                }

                $data = [
                    "userName" => $userName,
                    "email" => $email
                ];

                $profiledata = [
                    "projects" => json_encode([$projects]),
                    "proj_lang" => $projLang
                ];
                if(Config::get("app.type") != "remote") {
                    $profiledata["prefered_roles"] = json_encode(["translator"]);
                    $profiledata["languages"] = '{"en":[3,3]}';
                    $profiledata["complete"] = true;
                }

                $member = $this->memberRepo->create($postdata, $profiledata);

                if(Config::get("app.type") == "remote")
                {
                    Mailer::send('Emails/Auth/Activate', ["memberID" => $member->memberID, "token" => $activationToken], function($message) use($data)
                    {
                        $message->to($data["email"], $data["userName"])
                            ->subject(__('activate_account_title'));
                    });

                    // Project language for email message
                    $proj_language = $this->languageRepo->get($projLang);
                    $proj_lang = "";
                    if ($proj_language)
                    {
                        $pl = $proj_language;
                        $proj_lang = "[".$pl->langID."] " . $pl->langName .
                            ($pl->angName != "" && $pl->angName != $pl->langName ? " (".$pl->angName.")" : "");
                    }

                    // Projects list for email message
                    switch ($projects) {
                        case "vmast":
                            $projects = __("8steps_vmast");
                            break;
                        case "l2":
                            $projects = __("l2_3_events", ["level" => 2]);
                            break;
                        case "l3":
                            $projects = __("l2_3_events", ["level" => 3]);
                            break;
                        default:
                            $projects = __($projects);
                    }

                    Mailer::send(
                        'Emails/Common/NotifyRegistration',
                        [
                            "userName" => $userName,
                            "name" => $firstName . " " . $lastName,
                            "id" => $member->memberID,
                            "projectLanguage" => $proj_lang,
                            "projects" => $projects
                        ],
                        function($message) {
                            $message->to("vmastteam@gmail.com")->subject(__("new_account_title"));
                        });

                    Session::set("success", __('registration_success_message'));
                    Session::set("activation_email", $email);

                    Url::redirect('members/success');
                }
                else
                {
                    Session::set("success", __('registration_local_success_message'));
                    Url::redirect('members/login');
                }
            }
        }

        $data['csrfToken'] = Csrf::makeToken();

        return View::make('Members/Signup')
            ->shares("title", __("signup"))
            ->shares("data", $data)
            ->shares("languages", $languages)
            ->shares("error", @$error);
    }


    /**
     * Show password reset view with email form
     * @return mixed
     */
    public function passwordReset()
    {
        if (Session::get('memberID'))
        {
            Url::redirect('members');
        }

        if (isset($_POST['submit']))
        {
            if (!Csrf::isTokenValid())
            {
                Url::redirect('members/passwordreset');
            }

            $email = $_POST['email'];

            if(Config::get("app.type") == "remote")
            {
                if (!ReCaptcha::check())
                {
                    $error[] = __('captcha_wrong');
                }
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $error[] = __('enter_valid_email_error');
            }

            if(!isset($error))
            {
                $data = $this->_model->getMember(array("memberID", "userName", "email"), array("email", $email));
                if(!empty($data))
                {
                    $resetToken = md5(uniqid(rand(), true));
                    $postdata = array('resetToken' => $resetToken, 'resetDate' => date('Y-m-d H:i:s',time()));
                    $this->_model->updateMember($postdata, array('email' => $email));

                    if(Config::get("app.type") == "remote")
                    {
                        Mailer::send('Emails/Auth/PasswordReset', ["memberID" => $data[0]->memberID, "token" => $resetToken], function($message) use($data)
                        {
                            $message->to($data[0]->email, $data[0]->userName)
                                ->subject(__('passwordreset_title'));
                        });
                        $msg = __('pwresettoken_send_success');
                    }
                    else
                    {
                        $msg = __("passwordreset_link_message", ["link" => '<a href="'.site_url('members/resetpassword/' .$data[0]->memberID."/".$resetToken).'">'.site_url('members/resetpassword/' .$data[0]->memberID."/".$resetToken).'</a>']);
                    }

                    Session::set('success', $msg);

                    Url::redirect('members/success');
                }
                else
                {
                    $error[] = __('enter_valid_email_error');
                }
            }
        }

        $data['menu'] = 5;
        $data['csrfToken'] = Csrf::makeToken();

        return View::make('Members/PasswordReset')
            ->shares("title", __("passwordreset_title"))
            ->shares("data", $data)
            ->shares("error", @$error);
    }

    /**
     * Show password reset view with new password form
     * @param $memberID
     * @param $resetToken
     * @return mixed
     */
    public function resetPassword($memberID, $resetToken)
    {
        if (Session::get('memberID'))
        {
            Url::redirect('members');
        }

        $data['step'] = 1;
        $data['menu'] = 5;

        if (($memberID > 0) && (strlen($resetToken) == 32))
        {
            $user = $this->_model->getMember(array("memberID", "resetDate"),
                array(
                    array("memberID", $memberID),
                    array("resetToken", $resetToken)
            ));

            if (!$user || $user[0]->memberID == 0)
            {
                $error[] = __('no_account_error');
            }
            elseif((time() - strtotime($user[0]->resetDate) > (60*60*24*3)))
            {
                $error[] = __('token_expired_error');
                $postdata = array('resetToken' => null);
                $where = array('memberID' => $memberID);
                $this->_model->updateMember($postdata, $where);
            }
            else
            {
                $data['step'] = 2;

                if(isset($_POST['submit']))
                {
                    if (!Csrf::isTokenValid())
                    {
                        Url::redirect(SITEURL."members/resetpassword/$memberID/$resetToken");
                    }

                    $_POST = Gump::filter_input($_POST, array(
                        'password' => 'trim'
                    ));

                    $password = $_POST['password'];
                    $passwordConfirm = $_POST['passwordConfirm'];

                    if (strlen($password) < 5)
                    {
                        $error[] = __('password_short_error');
                    }
                    elseif ($password != $passwordConfirm)
                    {
                        $error[] = __('passwords_notmatch_error');
                    }

                    if (empty($error))
                    {
                        $postdata = array('password' => Password::make($password), 'resetToken' => null);
                        $this->_model->updateMember($postdata, array('memberID' => $memberID));

                        $msg = __('password_reset_success');
                        Session::set('success', $msg);

                        Url::redirect('members/success');
                    }
                }
            }
        }
        else
        {
            $error[] = __('invalid_link_error');
        }

        $data['csrfToken'] = Csrf::makeToken();

        return View::make('Members/ResetPassword')
            ->shares("title", __("passwordreset_title"))
            ->shares("data", $data)
            ->shares("error", @$error);
    }

    /**
     * Make rpc call from nodejs and send json string back
     * @param $memberID
     * @param $eventID
     * @param $authToken
     */
    public function rpcAuth($memberID, $eventID, $authToken) {

        $member = $this->memberRepo->get($memberID);
        $event = $this->eventsRepo->get($eventID);

        if($member && $event)
        {
            if ($member->authToken == $authToken) {
                $isAdmin = $event->admins->contains($member)
                    || $event->project->admins->contains($member)
                    || $event->project->gatewayLanguage->admins->contains($member);

                $isParticipant = $event->translators->contains($member)
                    || $event->checkersL2->contains($member)
                    || $event->checkersL3->contains($member);

                if ($isAdmin || $isParticipant) {
                    $member->isAdmin = $isAdmin;
                    $member->lastName = mb_substr($member->lastName, 0, 1).".";
                    echo json_encode($member);
                } else {
                    echo json_encode(array());
                }
            } else {
                echo json_encode(array());
            }
        }
        else
        {
            echo json_encode(array());
        }
    }


    public function sendMessageToAdmin()
    {
        $response = ["success" => false];

        if(Config::get("app.type") != "remote") {
            $response["errorType"] = "local";
            $response["error"] = __("local_use_restriction");
            echo json_encode($response);
            exit;
        }

        if(!empty($_POST)) {
            $_POST =  Gump::xss_clean($_POST);

            $adminID = isset($_POST["adminID"]) && $_POST["adminID"] != "" ? (integer)$_POST["adminID"] : null;
            $subject = isset($_POST["subject"]) && $_POST["subject"] != "" ? $_POST["subject"] : null;
            $message = isset($_POST["message"]) && $_POST["message"] != "" ? $_POST["message"] : null;

            if($adminID != null && $subject != null && $message != null) {
                $admin = $this->memberRepo->get($adminID);

                if($admin && $admin->isBookAdmin()) {
                    if($admin->memberID != $this->_member->memberID) {
                        $data["fName"] = $admin->firstName . " " . $admin->lastName;
                        $data["fEmail"] = $admin->email;
                        $data["tMemberID"] = $this->_member->memberID;
                        $data["tUserName"] = $this->_member->userName;
                        $data["tName"] = $this->_member->firstName . " " . $this->_member->lastName;
                        $data["tEmail"] = $this->_member->email;
                        $data["subject"] = $subject;
                        $data["message"] = $message;

                        $firstLang = "en";
                        $languages = json_decode($admin->profile->languages, true);
                        if(is_array($languages) && !empty($languages))
                        {
                            $keys = array_keys($languages);
                            $firstLang = $keys[0];
                        }

                        $lang = ucfirst($firstLang);
                        $data["member_profile_message"] = $this->_model->translate("member_profile_message", $lang);
                        $data["facilitator_message_tip"] = $this->_model->translate("facilitator_message_tip", $lang);
                        $data["subject"] = "VMAST ".$this->_model->translate("message_content", $lang).": " . $data["subject"];

                        Mailer::send('Emails/Common/Message', ["data" => $data], function($message) use($data)
                        {
                            $message->setReplyTo([$data["tEmail"] => $data["tName"]])
                                ->setTo($data["fEmail"], $data["fName"])
                                ->setSubject($data["subject"]);
                        });

                        $response["success"] = true;
                    }
                    else
                    {
                        $response["errorType"] = "data";
                        $response["error"] = __("facilitator_yourself_error");
                    }
                }
                else
                {
                    $response["errorType"] = "data";
                    $response["error"] = __("not_facilitator_error");
                }
            }
            else
            {
                $response["errorType"] = "data";
                $response["error"] = __("required_fields_empty_error");
            }
        }

        echo json_encode($response);
    }


    public function cloudLogin() {
        $response = ["success" => false];

        if(!empty($_POST)) {

            $_POST = Gump::xss_clean($_POST);

            $server = isset($_POST["server"]) && $_POST["server"] != "" ? $_POST["server"] : null;
            $username = isset($_POST["username"]) && $_POST["username"] != "" ? $_POST["username"] : null;
            $password = isset($_POST["password"]) && $_POST["password"] != "" ? $_POST["password"] : null;
            $otp = isset($_POST["otp"]) && $_POST["otp"] != "" ? $_POST["otp"] : "";

            if ($server != null && $username != null && $password != null)
            {
                $cloudModel = new CloudModel($server, $username, $password, $otp);
                $data = $cloudModel->getAccessTokens();
                $token = $cloudModel->getVmastAccessToken($data);

                if(empty($token) || $token["sha1"] == "")
                {
                    if (isset($token["id"]))
                        $cloudModel->deleteAccessToken($token["id"]);
                    $data = $cloudModel->createAccessToken();
                    $token = $cloudModel->getVmastAccessToken($data);
                }

                if(!empty($token))
                {
                    Session::set($server, [
                        "username" => $username,
                        "token" => $token["sha1"]
                    ]);

                    $response["success"] = true;
                }
                else
                {
                    $response["error"] = "could_not_get_token";
                }
            }
        }

        echo json_encode($response);
    }

    /**
     * Show success veiw
     * @return mixed
     */
    public function success()
    {
        return View::make('Members/Success')
            ->shares("title", __("success"));
    }

    /**
     * Show verification error view
     * @return mixed
     */
    public function verificationError()
    {
        return View::make('Members/Verify')
            ->shares("title", __("verification_error_title"))
            ->shares("error", __("verification_error"));
    }

    /**
     * Logout of site
     */
    public function logout()
    {
        Session::destroy();
        Url::redirect('/', true);
    }

    /**
     * Calculates average member rating based on one's profile
     * @param $profile
     * @return array
     */
    private function calculateMemberRating($profile)
    {
        $rating = 0;
        if($profile != null && !empty($profile))
        {
            $rating += $profile["bbl_trans_yrs"];
            $rating += $profile["othr_trans_yrs"];
            $rating += $profile["bbl_knwlg_degr"];
            $rating += $profile["mast_evnts"];
            $rating += $profile["teamwork"];
            $rating += in_array("translator", $profile["mast_role"]) ? 4 : 1;

            $langRate = 0;
            foreach ($profile["languages"] as $language => $data) {
                $lang = $data["lang_fluency"];
                $lang += $data["geo_lang_yrs"];
                $langRate += $lang/2;
            }

            if(sizeof($profile["languages"]) > 0)
                $rating += $langRate / sizeof($profile["languages"]);

            $rating = min($rating / 7, 4);

            // Average value
            $rating = sprintf("%1.2f", $rating);
        }

        return $rating;
    }
}