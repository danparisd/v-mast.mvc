<?php
/**
 * Routes - all standard Routes are defined here.
 */


/** Define static routes. */

// The default Routing
Router::get("", "App\Controllers\MainController@index");
Router::get("about", "App\Controllers\MainController@about");
Router::any("contact", "App\Controllers\MainController@contactUs");
Router::get("maintenance", "App\Controllers\MainController@maintenance");



// TRANSLATIONS
Route::group(["prefix" => "translations", "namespace" => "App\Controllers"], function() {
    Router::any("{lang}/{bookProject}/{sourceBible?}/{bookCode}/usfm", "TranslationsController@downloadUsfm")
        ->where([
            "lang" => "[a-zA-Z0-9-]+",
            "bookProject" => "[a-z0-9]+",
            "bookCode" => "[a-z0-9]+"
        ]);
    Router::any("{lang}/{bookProject}/{sourceBible?}/{bookCode}/json", "TranslationsController@downloadJson")
        ->where([
            "lang" => "[a-zA-Z0-9-]+",
            "bookProject" => "[a-z0-9]+",
            "bookCode" => "[a-z0-9]+"
        ]);
    Router::any("{lang}/tw/{sourceBible?}/{bookCode}/md", "TranslationsController@downloadMdTw")
        ->where([
            "lang" => "[a-zA-Z0-9-]+",
            "sourceBible" => "[a-z0-9]+",
            "bookCode" => "[a-z0-9]+"
        ]);
    Router::any("{lang}/obs/{sourceBible?}/obs/md", "TranslationsController@downloadMdObs")
        ->where([
            "lang" => "[a-zA-Z0-9-]+",
            "sourceBible" => "[a-z0-9]+"
        ]);
    Router::any("{lang}/{bookProject}/{sourceBible?}/{bookCode}/md", "TranslationsController@downloadMd")
        ->where([
            "lang" => "[a-zA-Z0-9-]+",
            "bookProject" => "[a-z0-9]+",
            "bookCode" => "[a-z0-9]+"
        ]);
    Router::any("{lang}/{bookProject}/{sourceBible?}/{bookCode}/ts", "TranslationsController@downloadTs")
        ->where([
            "lang" => "[a-zA-Z0-9-]+",
            "bookProject" => "[a-z0-9]+",
            "bookCode" => "[a-z0-9]+"
        ]);
    Router::any("{lang}/{bookProject}/{sourceBible?}/{bookCode}/{server}/export", "TranslationsController@export")
        ->where([
            "lang" => "[a-zA-Z0-9-]+",
            "bookProject" => "[a-z0-9]+",
            "bookCode" => "[a-z0-9]+"
        ]);
    Router::any("", "TranslationsController@languages");
    Router::any("{lang}", "TranslationsController@resources")
        ->where([
            "lang" => "[a-zA-Z0-9-]+"
        ]);
    Router::any("{lang}/{bookProject}/{sourceBible}", "TranslationsController@books")
        ->where([
            "lang" => "[a-zA-Z0-9-]+",
            "bookProject" => "[a-z0-9]+",
            "sourceBible" => "[a-z0-9]+",
        ]);
    Router::any("{lang}/{bookProject}/{sourceBible}/{bookCode}", "TranslationsController@book")
        ->where([
            "lang" => "[a-zA-Z0-9-]+",
            "bookProject" => "[a-z0-9]+",
            "sourceBible" => "[a-z0-9]+",
            "bookCode" => "[a-z0-9]+"
        ]);
});


// EVENTS
Route::group(["prefix" => "events", "namespace" => "App\Controllers"], function() {
    Router::any("", "EventsController@index");
    Router::any("translator/{eventID}", "EventsController@translator")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("translator/{eventID}/{chapter}", "EventsController@translatorContinue")
        ->where(["eventID" => "[0-9]+", "chapter" => "[0-9]+"]);
    Router::any("translator-tn/{eventID}", "EventsController@translatorNotes")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("translator-tq/{eventID}", "EventsController@translatorQuestions")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("translator-tw/{eventID}", "EventsController@translatorWords")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("translator-sun/{eventID}", "EventsController@translatorSun")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("translator-odb-sun/{eventID}", "EventsController@translatorOdbSun")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("translator-rad/{eventID}", "EventsController@translatorRadio")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("translator-obs/{eventID}", "EventsController@translatorObs")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("checker-revision/{eventID}", "EventsController@checkerRevision")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("checker-revision/{eventID}/{chapter}", "EventsController@checkerRevisionContinue")
        ->where(["eventID" => "[0-9]+", "chapter" => "[0-9]+"]);
    Router::any("checker-revision/{eventID}/{memberID}/{chapter}", "EventsController@checkerRevisionApprover")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+"
        ]);
    Router::any("checker-l3/{eventID}", "EventsController@checkerL3")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("checker-l3/{eventID}/{memberID}/{chapter}", "EventsController@checkerL3Peer")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+"
        ]);
    Router::any("checker-tn-l3/{eventID}", "EventsController@checkerNotesL3")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("checker-tn-l3/{eventID}/{memberID}/{chapter}", "EventsController@checkerNotesL3Peer")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+"
        ]);
    Router::any("checker-sun-l3/{eventID}", "EventsController@checkerSunL3")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("checker-sun-l3/{eventID}/{memberID}/{chapter}", "EventsController@checkerSunL3Peer")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+"
        ]);
    Router::any("checker/{eventID}/{memberID}/{chapter}", "EventsController@checker")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+"
            ]);
    Router::any("checker-tn/{eventID}/{memberID}/{chapter}", "EventsController@checkerNotes")
        ->where([
            "eventID" => "[0-9]+"
            ]);
    Router::any("checker-tn/{eventID}/{memberID}", "EventsController@checkerNotesPeer")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+"
            ]);
    Router::any("checker-sun/{eventID}/{memberID}/{chapter}", "EventsController@checkerSun")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+"
        ]);
    Router::any("checker-odb-sun/{eventID}/{memberID}/{chapter}", "EventsController@checkerOdbSun")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+"
        ]);
    Router::any("checker-tq/{eventID}/{memberID}/{chapter}", "EventsController@checkerQuestions")
        ->where([
            "eventID" => "[0-9]+"
        ]);
    Router::any("checker-tw/{eventID}/{memberID}/{chapter}", "EventsController@checkerWords")
        ->where([
            "eventID" => "[0-9]+"
        ]);
    Router::any("checker-rad/{eventID}/{memberID}/{chapter}", "EventsController@checkerRadio")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+"
        ]);
    Router::any("checker-obs/{eventID}/{memberID}/{chapter}", "EventsController@checkerObs")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+"
        ]);
    Router::any("checker/{eventID}/{memberID}/{chapter}/{step}/apply", "EventsController@applyChecker")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+",
            "step" => "[a-z\-]+"
        ]);
    Router::any("checker-{bookProject}/{eventID}/{memberID}/other/{chapter}/apply", "EventsController@applyCheckerOther")
        ->where([
            "bookProject" => "tn|tq|tw|obs",
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+"
        ]);
    Router::any("checker-{bookProject}/{eventID}/{memberID}/peer-review/{chapter}/apply", "EventsController@applyCheckerOther")
        ->where([
            "bookProject" => "tn|tq|tw|obs",
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+"
        ]);
    Router::any("checker/{eventID}/{memberID}/{step}/{chapter}/apply", "EventsController@applyCheckerL2L3")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+",
            "step" => "[23a-z\-]+"
        ]);
    Router::any("checker-sun/{eventID}/{memberID}/{step}/{chapter}/apply", "EventsController@applyCheckerSun")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+",
            "step" => "[2a-z\-]+"
        ]);
    Router::any("checker-rad/{eventID}/{memberID}/{step}/{chapter}/apply", "EventsController@applyCheckerRadio")
        ->where([
            "eventID" => "[0-9]+",
            "memberID" => "[0-9]+",
            "chapter" => "[0-9]+",
            "step" => "[2a-z\-]+"
        ]);

    Router::any("news", "EventsController@news");
    Router::any("faq", "EventsController@faqs");
    Router::any("rpc/get_notifications", "EventsController@getNotifications");
    Router::any("rpc/autosave_chunk", "EventsController@autosaveChunk");
    Router::any("rpc/autosave_li_verse", "EventsController@autosaveVerseLangInput");
    Router::any("rpc/delete_li_verse", "EventsController@deleteVerseLangInput");
    Router::any("rpc/save_comment", "EventsController@saveComment");
    Router::any("rpc/save_keyword", "EventsController@saveKeyword");
    Router::any("rpc/get_keywords", "EventsController@getKeywords");
    Router::any("rpc/check_event", "EventsController@checkEvent");
    Router::any("rpc/check_internet", "EventsController@checkInternet");
    Router::any("rpc/apply_verb_checker", "EventsController@applyVerbChecker");
    Router::any("rpc/get_tq/{bookCode}/{chapter}/{lang}", "EventsController@getTq");
    Router::any("rpc/get_tw/{bookCode}/{chapter}/{lang}", "EventsController@getTw");
    Router::any("rpc/get_tn/{bookCode}/{chapter}/{lang}/{totalVerses}", "EventsController@getTn");
    Router::any("rpc/get_bc/{bookCode}/{chapter}/{lang}", "EventsController@getBc");
    Router::any("rpc/get_bc_article/{lang}/{article}", "EventsController@getBcArticle");
    Router::any("rpc/get_rubric/{lang}", "EventsController@getRubric");
    Router::any("rpc/get_saildict/", "EventsController@getSailDict");
});

// INFORMATION
Route::group(["prefix" => "events", "namespace" => "App\Controllers"], function() {
    Router::any("information/{eventID}", "InformationController@information")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("information-tn/{eventID}", "InformationController@informationNotes")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("information-tq/{eventID}", "InformationController@informationQuestions")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("information-tw/{eventID}", "InformationController@informationWords")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("information-revision/{eventID}", "InformationController@informationRevision")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("information-tn-l3/{eventID}", "InformationController@informationL3")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("information-sun-l3/{eventID}", "InformationController@informationL3")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("information-l3/{eventID}", "InformationController@informationL3")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("information-sun/{eventID}", "InformationController@informationSun")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("information-odb-sun/{eventID}", "InformationController@informationOdbSun")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("information-rad/{eventID}", "InformationController@informationRadio")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("information-obs/{eventID}", "InformationController@informationObs")
        ->where(["eventID" => "[0-9]+"]);
});

//MANAGE
Route::group(["prefix" => "events", "namespace" => "App\Controllers"], function() {
    Router::any("manage/{eventID}", "ManageController@manage")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("manage-tw/{eventID}", "ManageController@manageTw")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("manage-revision/{eventID}", "ManageController@manageRevision")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("manage-l3/{eventID}", "ManageController@manageL3")
        ->where(["eventID" => "[0-9]+"]);
    Router::any("rpc/move_step_back", "ManageController@moveStepBack");
    Router::any("rpc/move_step_back_alt", "ManageController@moveStepBackAlt");
    Router::any("rpc/set_tn_checker", "ManageController@setOtherChecker");
    Router::any("rpc/assign_chapter", "ManageController@assignChapter");
    Router::any("rpc/add_event_member", "ManageController@addEventMember");
    Router::any("rpc/delete_event_member", "ManageController@deleteEventMember");
    Router::any("rpc/create_words_group", "ManageController@createWordsGroup");
    Router::any("rpc/delete_words_group", "ManageController@deleteWordsGroup");
    Router::any("rpc/get_event_members", "ManageController@getEventMembers");
    Router::any("rpc/send_user_email", "ManageController@sendUserEmail");
});

//DEMO
Route::group(["prefix" => "events", "namespace" => "App\Controllers"], function() {
    Router::any("demo/{page?}", "DemoController@demo");
    Router::any("demo-scripture-input/{page?}", "DemoController@demoLangInput");
    Router::any("demo-revision/{page?}/{mode?}", "DemoController@demoRevision");
    Router::any("demo-l3/{page?}", "DemoController@demoL3");
    Router::any("demo-tn-l3/{page?}", "DemoController@demoL3Notes");
    Router::any("demo-tn/{page?}", "DemoController@demoTn");
    Router::any("demo-tq/{page?}", "DemoController@demoTq");
    Router::any("demo-tw/{page?}", "DemoController@demoTw");
    Router::any("demo-sun/{page?}", "DemoController@demoSun");
    Router::any("demo-sun-l3/{page?}", "DemoController@demoSunL3");
    Router::any("demo-sun-odb/{page?}", "DemoController@demoSunOdb");
    Router::any("demo-rad/{page?}", "DemoController@demoRadio");
    Router::any("demo-obs/{page?}", "DemoController@demoObs");
});


// MEMBERS
Route::group(["prefix" => "members", "namespace" => "App\Controllers"], function() {
    Router::any("", "MembersController@index");
    Router::any("profile", "MembersController@profile");
    Router::any("profile/{memberID}", "MembersController@publicProfile")
        ->where(["memberID" => "[0-9]+"]);
    Router::any("search", "MembersController@search");
    Router::any("signup", "MembersController@signup");
    Router::any("signup_desktop", "MembersController@signupDesktop");
    Router::any("login", "MembersController@login");
    Router::any("login_desktop", "MembersController@loginDesktop");
    Router::any("logout", "MembersController@logout");
    Router::any("error/verification", "MembersController@verificationError");
    Router::any("passwordreset", "MembersController@passwordReset");
    Router::any("resetpassword/{memberID}/{token}", "MembersController@resetPassword")
        ->where([
            "memberID" => "[0-9]+",
            "token" => "[a-z0-9]+"
        ]);
    Router::any("activate/{memberID}/{token}", "MembersController@activate")
        ->where([
            "memberID" => "[0-9]+",
            "token" => "[a-z0-9]+"
        ]);
    Router::any("activate/resend/{email}", "MembersController@resendActivation");
    Router::any("success", "MembersController@success");
    Router::any("rpc/auth/{memberID}/{eventID}/{authToken}", "MembersController@rpcAuth")
        ->where([
            "memberID" => "[0-9]+",
            "eventID" => "[a-z0-9]+",
            "authToken" => "[a-z0-9]+"
        ]);
    Router::any("rpc/search_members", "MembersController@searchMembers");
    Router::any("rpc/send_message", "MembersController@sendMessageToAdmin");
    Router::any("rpc/cloud_login", "MembersController@cloudLogin");
});


// ADMIN
Route::group(["prefix" => "admin", "namespace" => "App\Controllers\Admin"], function() {
    Router::any("", "AdminController@index");
    Router::any("gateway_language/{glID}", "AdminController@gatewayLanguage")
        ->where([
            "glID" => "[0-9]+"
        ]);
    Router::any("project/{projectID}", "AdminController@project")
        ->where([
            "projectID" => "[0-9]+"
    ]);
    Router::any("members", "AdminController@members");
    Router::any("tools", "AdminController@toolsSource");
    Router::any("tools/vsun", "AdminController@toolsVsun");
    Router::any("tools/faq", "AdminController@toolsFaq");
    Router::any("tools/news", "AdminController@toolsNews");
    Router::any("tools/common", "AdminController@toolsCommon");
    Router::any("rpc/create_gateway_language", "AdminController@createGatewayLanguage");
    Router::any("rpc/get_gl_admins", "AdminController@getGlAdmins");
    Router::any("rpc/edit_gl_admins", "AdminController@editGlAdmins");
    Router::any("rpc/import", "AdminController@import");
    Router::any("rpc/repos_search/{q}", "AdminController@repos_search");
    Router::any("rpc/get_project", "AdminController@getProject");
    Router::any("rpc/get_event", "AdminController@getEvent");
    Router::any("rpc/get_event_contributors", "AdminController@getEventContributors");
    Router::any("rpc/get_project_contributors", "AdminController@getProjectContributors");
    Router::any("rpc/create_project", "AdminController@createProject");
    Router::any("rpc/get_members", "AdminController@getMembers");
    Router::any("rpc/search_members", "AdminController@searchMembers");
    Router::any("rpc/get_target_languages", "AdminController@getTargetLanguagesByGwLanguage");
    Router::any("rpc/create_event", "AdminController@createEvent");
    Router::any("rpc/create_tw_event", "AdminController@createEventTw");
    Router::any("rpc/verify_member", "AdminController@verifyMember");
    Router::any("rpc/block_member", "AdminController@blockMember");
    Router::any("rpc/clear_cache", "AdminController@clearCache");
    Router::any("rpc/update_languages", "AdminController@updateLanguages");
    Router::any("rpc/update_catalog", "AdminController@updateCatalog");
    Router::any("rpc/clear_all_cache", "AdminController@clearAllCache");
    Router::any("rpc/create_multiple_users", "AdminController@createMultipleUsers");
    Router::any("rpc/delete_sail_word", "AdminController@deleteSailWord");
    Router::any("rpc/create_sail_word", "AdminController@createSailWord");
    Router::any("rpc/delete_faq", "AdminController@deleteFaq");
    Router::any("rpc/create_faq", "AdminController@createFaq");
    Router::any("rpc/create_news", "AdminController@createNews");
    Router::any("rpc/upload_sun_font", "AdminController@uploadSunFont");
    Router::any("rpc/upload_sun_dict", "AdminController@uploadSunDict");
    Router::any("rpc/upload_image", "AdminController@uploadImage");
    Router::any("rpc/upload_source", "AdminController@uploadSource");
    Router::any("rpc/update_source", "AdminController@updateSource");
    Router::any("rpc/create_custom_src", "AdminController@createCustomSource");
    Router::any("rpc/get_event_progress/{eventID}", "AdminController@getEventProgress")
        ->where([
            "eventID" => "[0-9]+"
        ]);
    Router::any("migrate/chapters", "AdminController@migrateChapters");
    Router::any("migrate/questions_words", "AdminController@migrateQuestionsWords");
    Router::any("migrate/8steps", "AdminController@migrate8steps");
    Router::any("migrate/admins", "AdminController@migrateAdmins");
});

/** End default Routes */
