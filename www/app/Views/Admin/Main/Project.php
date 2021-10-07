<?php
use Helpers\Constants\EventStates;

$language = Language::code();

if($project):
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h1 class="panel-title">
            <?php echo "[".$project->targetLang."] "
                . $project->targetLanguage->langName
                . ($project->targetLanguage->langName != $project->targetLanguage->angName
                    && $project->targetLanguage->angName != "" ? " (" . $project->targetLanguage->angName . ")" : "")
                . " - ".__($project->bookProject) ?>
        </h1>
    </div>

    <div class="form-inline dt-bootstrap no-footer">
        <div style="display: flex; margin-bottom: 50px; border-bottom: 1px solid #ccc;">
            <ul class="nav nav-pills book-parts">
                <li role="presentation" class="active"><a href="#old_test"><?php echo __("old_test") ?></a></li>
                <li role="presentation"><a href="#new_test"><?php echo __("new_test") ?></a></li>
            </ul>
            <div style="flex: 2; display: flex; justify-content: flex-end">
                <div class="add-event-btn">
                    <img class="contibLoader" width="24px" src="<?php echo template_url("img/loader.gif") ?>">
                    <button style="margin-top: 12px" class="btn btn-warning showAllContibutors"
                            data-projectid="<?php echo $project->projectID ?>"><?php echo __("contributors") ?></button>
                </div>
                <div class="add-event-btn">
                    <img class="cacheLoader" width="24px" src="<?php echo template_url("img/loader.gif") ?>">
                    <button style="margin-top: 12px" class="btn btn-danger"
                            name="updateAllCache"
                            data-sourcelangid="<?php echo $project->sourceLangID ?>"
                            data-sourcebible="<?php echo $project->sourceBible ?>"><?php echo __("update_cache_all") ?></button>
                </div>
            </div>
        </div>

        <?php foreach($bookInfos as $bookInfo): ?>
            <?php if($bookInfo->sort == 1): ?>
            <div class="row" id="old_test">
                <div class="project_progress progress <?php echo $data["OTprogress"] <= 0 ? "zero" : ""?>">
                    <div class="progress-bar progress-bar-success" role="progressbar"
                         aria-valuenow="<?php echo floor($data["OTprogress"]) ?>"
                         aria-valuemin="0" aria-valuemax="100" style="min-width: 0em; width: <?php echo floor($data["OTprogress"])."%" ?>">
                        <?php echo floor($data["OTprogress"])."%" ?>
                    </div>
                </div>
                <div class="col-sm-12">
            <?php elseif($bookInfo->sort == 41): ?>
            <div class="row" id="new_test">
                <div class="project_progress progress <?php echo $data["NTprogress"] <= 0 ? "zero" : ""?>">
                    <div class="progress-bar progress-bar-success" role="progressbar"
                         aria-valuenow="<?php echo floor($data["NTprogress"]) ?>"
                         aria-valuemin="0" aria-valuemax="100" style="min-width: 0em; width: <?php echo floor($data["NTprogress"])."%" ?>">
                        <?php echo floor($data["NTprogress"])."%" ?>
                    </div>
                </div>
                <div class="col-sm-12">
            <?php endif; ?>
                <?php if($bookInfo->sort == 1 || $bookInfo->sort == 41): ?>

                <table class="table table-bordered table-hover" role="grid">
                    <thead>
                    <tr>
                        <th><?php echo __("book") ?></th>
                        <th><?php echo __("time_start") ?></th>
                        <th><?php echo __("time_end") ?></th>
                        <th><?php echo __("state") ?></th>
                        <th><?php echo __("progress") ?></th>
                        <th><?php echo __("contributors") ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                <?php endif; ?>
                        <tr>
                            <td><?php echo $bookInfo->name ?></td>
                            <td class="datetime" data="<?php echo $bookInfo->event && $bookInfo->event->dateFrom != "" && $bookInfo->event->dateFrom != "0000-00-00 00:00:00" ?
                                date(DATE_RFC2822, strtotime($bookInfo->event->dateFrom)) : "" ?>">
                                <?php echo $bookInfo->event && $bookInfo->event->dateFrom != "" && $bookInfo->event->dateFrom != "0000-00-00 00:00:00" ? $bookInfo->event->dateFrom . " UTC" : "" ?></td>
                            <td class="datetime" data="<?php echo $bookInfo->event && $bookInfo->event->dateTo != "" && $bookInfo->event->dateTo != "0000-00-00 00:00:00" ?
                                date(DATE_RFC2822, strtotime($bookInfo->event->dateTo)) : "" ?>">
                                <?php echo $bookInfo->event && $bookInfo->event->dateTo != "" && $bookInfo->event->dateTo != "0000-00-00 00:00:00" ? $bookInfo->event->dateTo . " UTC" : "" ?></td>
                            <td><?php echo $bookInfo->event && $bookInfo->event->state ? __("state_".$bookInfo->event->state) : "" ?></td>
                            <td style="position:relative;">
                                <div class="event_column progress zero" data-eventid="<?php echo $bookInfo->event ? $bookInfo->event->eventID : ""?>">
                                    <div class="progress-bar progress-bar-success" role="progressbar"
                                         aria-valuenow="0"
                                         aria-valuemin="0" aria-valuemax="100" style="min-width: 0em; width: 0%">
                                        0%
                                    </div>
                                    <img class="progressLoader" width="24px" src="<?php echo template_url("img/loader.gif") ?>">
                                </div>
                            </td>
                            <td style="white-space: nowrap">
                                <?php if($bookInfo->event && $bookInfo->event->state != ""
                                    && EventStates::enum($bookInfo->event->state) >= EventStates::enum(EventStates::TRANSLATED)
                                    && in_array($project->bookProject, ["ulb","udb","sun"])): ?>
                                    <button class="btn btn-warning showContributors" data-eventid="<?php echo $bookInfo->event->eventID?>" data-level="1">
                                        <?php echo __("L1") ?>
                                    </button>
                                <?php endif; ?>
                                <?php if($bookInfo->event && $bookInfo->event->state != ""
                                    && (EventStates::enum($bookInfo->event->state) >= EventStates::enum(EventStates::L2_CHECKED)
                                        || (EventStates::enum($bookInfo->event->state) >= EventStates::enum(EventStates::TRANSLATED)
                                        && in_array($project->bookProject, ["tn","tq","tw"])))): ?>
                                    <button class="btn btn-warning showContributors"
                                            data-eventid="<?php echo $bookInfo->event->eventID?>"
                                            data-level="2"
                                            data-mode="<?php echo $project->bookProject ?>">
                                        <?php echo __("L2") ?>
                                    </button>
                                <?php endif; ?>
                                <?php if($bookInfo->event && $bookInfo->event->state != "" && EventStates::enum($bookInfo->event->state) >= EventStates::enum(EventStates::COMPLETE)): ?>
                                    <button class="btn btn-warning showContributors"
                                            data-eventid="<?php echo $bookInfo->event->eventID?>"
                                            data-level="3"
                                            data-mode="<?php echo $project->bookProject ?>">
                                        <?php echo __("L3") ?>
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                switch($bookInfo->event)
                                {
                                    case null:
                                        echo '<button 
                                            data-bookcode="'.$bookInfo->code.'" 
                                            data-bookname="'.$bookInfo->name.'" 
                                            data-chapternum="'.$bookInfo->chaptersNum.'" 
                                                class="btn btn-primary startEvnt">'.__("create").'</button>';
                                        break;

                                    default:
                                        echo '<button 
                                            data-bookcode="'.$bookInfo->code.'" 
                                            data-eventid="'.$bookInfo->event->eventID.'" 
                                            data-sort="'.$bookInfo->sort.'"
                                                class="btn btn-success editEvnt">'.__("edit").'</button>';
                                }
                                ?>
                            </td>
                        </tr>

            <?php if($bookInfo->sort == 39): ?>
                    </tbody>
                </table>
                </div>
            </div>
            <?php elseif($bookInfo->sort == 67): ?>
                    </tbody>
                </table>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<div class="event-content form-panel">
    <div class="create-event-content panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title"><?php echo __("create_event"); ?></h1>
            <span class="panel-close glyphicon glyphicon-remove"></span>
        </div>

        <div class="page-content row panel-body">
            <div class="bookName"></div>
            <div class="book_info_content"></div>
            <div class="clear"></div>

            <div class="event_menu">
                <div class="glyphicon glyphicon-menu-hamburger"></div>
                <ul>
                    <li class="clearCache">
                        <?php echo __("clear_cache"); ?>
                        <span class="glyphicon glyphicon-question-sign" title="<?php echo __("clear_cache_info") ?>"></span>
                    </li>
                    <li class="deleteEvent"><?php echo __("delete"); ?></li>

                    <?php if(in_array($project->bookProject, ["ulb","udb","sun"])):?>
                    <hr>
                    <div class="event_links_l1">
                        <li class="option_group"><?php echo __("translation_event") ?></li>
                        <li class="event_progress"><a href="#"><?php echo __("progress"); ?></a></li>
                        <li class="event_manage"><a href="#"><?php echo __("manage"); ?></a></li>
                    </div>
                    <?php endif; ?>

                    <hr>
                    <div class="event_links_l2">
                        <li class="option_group"><?php echo __("l2_3_events", ["level" => 2]) ?></li>
                        <li class="event_progress"><a href="#"><?php echo __("progress"); ?></a></li>
                        <li class="event_manage"><a href="#"><?php echo __("manage"); ?></a></li>
                    </div>

                    <hr>
                    <div class="event_links_l3">
                        <li class="option_group"><?php echo __("l2_3_events", ["level" => 3]) ?></li>
                        <li class="event_progress"><a href="#"><?php echo __("progress"); ?></a></li>
                        <li class="event_manage"><a href="#"><?php echo __("manage"); ?></a></li>
                    </div>
                </ul>
            </div>

            <div class="errors"></div>

            <div class="row">
                <div class="col-sm-12">
                    <form action="/admin/rpc/create_event" method="post" id="startEvent">
                        <div class="form-group" style="width: 450px;">
                            <label for="adminsSelect" style="width: 100%; display: block"><?php echo __('facilitators'); ?></label>
                            <select class="form-control" name="admins[]" id="adminsSelect" multiple data-placeholder="<?php echo __("add_admins_by_username") ?>">
                                <option></option>
                            </select>
                        </div>

                        <div class="form-group delinput" style="width: 350px; display: none">
                            <label for="delevnt" style="width: 100%; display: block; color: #f00;"><?php echo __('delete_warning'); ?></label>
                            <div style="display: flex;">
                                <input class="form-control" type="text" id="delevnt" autocomplete="off" style="margin-right: 10px">
                                <button type="submit" name="deleteEvent" class="btn btn-danger"><?php echo __("delete"); ?></button>
                            </div>
                        </div>

                        <div class="event_level_radio">
                            <label style="width: 100%; display: block"><?php echo __('choose_event_level'); ?></label>
                            <?php if(in_array($project->bookProject, ["ulb","udb","sun"])):?>
                            <label>
                                <input type="radio" name="eventLevel" value="1" class="event_l_1" checked>
                                <?php echo __("level2_3_check", ["level" => 1]) ?>
                            </label>&nbsp;&nbsp;
                            <?php endif; ?>
                            <label>
                                <input type="radio" name="eventLevel" value="2" class="event_l_2"
                                    <?php echo !in_array($project->bookProject, ["ulb","udb","sun"]) ? "checked" : "" ?>>
                                <?php echo __("level2_3_check", ["level" => 2]) ?>
                            </label>&nbsp;&nbsp;
                            <label>
                                <input type="radio" name="eventLevel" value="3" class="event_l_3">
                                <?php echo __("level2_3_check", ["level" => 3]) ?>
                            </label>
                        </div>

                        <div class="event_imports">
                            <?php if($project->bookProject == "tn"): ?>
                            <div class="import tn_l1_import">
                                <div class="import_title"><?php echo __("tn") ?> L1</div>
                                <div class="import_link" data-source="tn_l1" title="<?php echo __("import_translation_tip") ?>">
                                    Import
                                </div>
                                <div class="import_done glyphicon glyphicon-ok"></div>
                                <div class="import_progress glyphicon glyphicon-info-sign" title="<?php echo __("step_status_in_progress"); ?>"></div>
                            </div>
                            <div class="import tn_l2_import">
                                <div class="import_title"><?php echo __("tn") ?> L2</div>
                                <div class="import_link" data-source="tn_l2" title="<?php echo __("import_translation_tip") ?>">
                                    Import
                                </div>
                                <div class="import_done glyphicon glyphicon-ok"></div>
                                <div class="import_progress glyphicon glyphicon-info-sign" title="<?php echo __("step_status_in_progress"); ?>"></div>
                            </div>
                            <?php endif; ?>

                            <?php if($project->bookProject == "tq"): ?>
                            <div class="import tq_l1_import">
                                <div class="import_title"><?php echo __("tq") ?> L1</div>
                                <div class="import_link" data-source="tq_l1" title="<?php echo __("import_translation_tip") ?>">
                                    Import
                                </div>
                                <div class="import_done glyphicon glyphicon-ok"></div>
                                <div class="import_progress glyphicon glyphicon-info-sign" title="<?php echo __("step_status_in_progress"); ?>"></div>
                            </div>
                            <div class="import tq_l2_import">
                                <div class="import_title"><?php echo __("tq") ?> L2</div>
                                <div class="import_link" data-source="tq_l2" title="<?php echo __("import_translation_tip") ?>">
                                    Import
                                </div>
                                <div class="import_done glyphicon glyphicon-ok"></div>
                                <div class="import_progress glyphicon glyphicon-info-sign" title="<?php echo __("step_status_in_progress"); ?>"></div>
                            </div>
                            <?php endif; ?>

                            <?php if(!in_array($project->bookProject, ["tn","tq"])): ?>
                            <div class="import l1_import">
                                <div class="import_title"><?php echo __("book") ?> L1</div>
                                <div class="import_link" data-source="l1" title="<?php echo __("import_translation_tip") ?>">
                                    Import
                                </div>
                                <div class="import_done glyphicon glyphicon-ok"></div>
                                <div class="import_progress glyphicon glyphicon-info-sign" title="<?php echo __("step_status_in_progress"); ?>"></div>
                            </div>
                            <?php endif; ?>

                            <div class="import l2_import">
                                <div class="import_title"><?php echo __("book") ?> L2</div>
                                <div class="import_link" data-source="l2" title="<?php echo __("import_translation_tip") ?>">
                                    Import
                                </div>
                                <div class="import_done glyphicon glyphicon-ok"></div>
                                <div class="import_progress glyphicon glyphicon-info-sign" title="<?php echo __("step_status_in_progress"); ?>"></div>
                            </div>

                            <?php if(!in_array($project->bookProject, ["ulb","udb","sun"])): ?>
                            <div class="import l3_import">
                                <div class="import_title"><?php echo __("book") ?> L3</div>
                                <div class="import_link" data-source="l3" title="<?php echo __("import_translation_tip") ?>">
                                    Import
                                </div>
                                <div class="import_done glyphicon glyphicon-ok"></div>
                                <div class="import_progress glyphicon glyphicon-info-sign" title="<?php echo __("step_status_in_progress"); ?>"></div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if($project->bookProject == "ulb"): ?>
                        <div class="form-control language_input_checkbox">
                            <label>
                                <input type="checkbox" name="langInput">
                                <?php echo __("lang_input") ?>
                            </label>
                        </div>
                        <?php endif; ?>

                        <input type="hidden" name="eID" id="eID" value="">
                        <input type="hidden" name="act" id="eventAction" value="create">
                        <input type="hidden" name="sort" id="sort" value="" />
                        <input type="hidden" name="book_code" id="bookCode" value="" />
                        <input type="hidden" name="projectID" id="projectID" value="<?php echo $project->projectID?>" />
                        <input type="hidden" name="sourceBible" id="sourceBible" value="<?php echo $project->sourceBible?>" />
                        <input type="hidden" name="bookProject" id="bookProject" value="<?php echo $project->bookProject?>" />
                        <input type="hidden" name="sourceLangID" id="sourceLangID" value="<?php echo $project->sourceLangID?>" />
                        <input type="hidden" name="targetLangID" id="targetLangID" value="<?php echo $project->targetLang?>" />
                        <input type="hidden" name="initialLevel" id="initialLevel" value="1" />
                        <input type="hidden" name="importLevel" id="importLevel" value="1" />
                        <input type="hidden" name="importProject" id="importProject" value="<?php echo $project->bookProject?>" />

                        <br>
                        <button type="submit" name="startEvent" class="btn btn-primary"><?php echo __("create"); ?></button>

                        <img class="startEventLoader" style="position:absolute; bottom: 5px; right: 5px;" width="24px" src="<?php echo template_url("img/loader.gif") ?>">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="import_menu_content form-panel">
    <div class="import_menu_content_body panel panel-default">
        <div class="import_menu">
            <ul>
                <li><?php echo __("import_options") ?></li>
                <li data-type="dcs">
                    <label role="button"><?php echo __("import_from_dcs") ?></label>
                </li>
                <li data-type="usfm">
                    <form id="usfm_form">
                        <label for="usfm_import" role="button"><?php echo __("import_from_usfm") ?>
                            <input type="file" name="import" id="usfm_import" accept=".usfm" />
                            <input type="hidden" name="type" value="usfm" />
                        </label>
                    </form>
                </li>
                <li data-type="ts">
                    <form id="ts_form">
                        <label for="ts_import" role="button"><?php echo __("import_from_ts") ?>
                            <input type="file" name="import" id="ts_import" accept=".tstudio" />
                            <input type="hidden" name="type" value="ts" />
                        </label>
                    </form>
                </li>
                <li data-type="zip">
                    <form id="zip_form">
                        <label for="zip_import" role="button"><?php echo __("import_from_zip") ?>
                            <input type="file" name="import" id="zip_import" accept=".zip" />
                            <input type="hidden" name="type" value="zip" />
                        </label>
                    </form>
                </li>
                <li>
                    <img class="importLoader" width="24px" src="<?php echo template_url("img/loader.gif") ?>">
                    <?php echo __("cancel") ?>
                </li>
            </ul>
        </div>
    </div>
</div>


<div class="dcs_import_menu_content form-panel">
    <div class="dcs_import_menu_content_body panel panel-default">
        <div class="dcs_import_menu">
            <ul>
                <li><?php echo __("import_from_dcs") ?></li>
                <li data-type="dcs">
                    <form id="dcs_form">
                        <input class="form-control" type="text" name="dcs_repo_name" placeholder="<?php echo __("repository_name") ?>" />
                        <div class="dcs_list">
                            <table class="table table-hover" role="grid">
                                <thead>
                                <tr>
                                    <th><?php echo __("userName") ?></th>
                                    <th><?php echo __("repository") ?></th>
                                    <th><?php echo __("updated_at") ?></th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="import" value="" />
                    </form>
                </li>
                <li>
                    <img class="importLoader" width="24px" src="<?php echo template_url("img/loader.gif") ?>">
                    <?php echo __("cancel") ?>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="contributors_container">
    <div class="contributors_block">
        <div class="contributors-close glyphicon glyphicon-remove"></div>
        <div class="contributors_title"><?php echo __("event_contributors") ?></div>
        <div class="contributors_title proj">
            <?php echo __("contributors") ?>
            <button class="btn btn-link contribs_download_tsv">Download (.tsv)</button>
        </div>
        <div class="contributors_content"></div>
    </div>
</div>
<?php else: ?>
<div>Project does not exist or you do not have rights to see it</div>
<?php endif; ?>

<link href="<?php echo template_url("css/jquery-ui-timepicker-addon.css")?>" type="text/css" rel="stylesheet" />
<script src="<?php echo template_url("js/jquery-ui-timepicker-addon.min.js")?>"></script>
<?php if($language != "en"): ?>
<script src="<?php echo template_url("js/i18n/jquery-ui-timepicker-".$language.".js")?>"></script>
<script src="<?php echo template_url("js/i18n/datepicker-".$language.".js")?>"></script>
<?php endif; ?>

<link href="<?php echo template_url("css/chosen.min.css")?>" type="text/css" rel="stylesheet" />
<script src="<?php echo template_url("js/chosen.jquery.min.js")?>"></script>
<script src="<?php echo template_url("js/ajax-chosen.min.js")?>"></script>