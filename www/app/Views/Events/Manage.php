<?php
use Helpers\Constants\EventSteps;
use Helpers\Constants\EventStates;
use Shared\Legacy\Error;

echo Error::display($error);

if(!isset($error)):
?> 

<div class="back_link">
    <span class="glyphicon glyphicon-chevron-left"></span>
    <a href="#" onclick="history.back(); return false;"><?php echo __("go_back") ?></a>
</div>

<div class="manage_container">
    <div class="row">
        <div class="col-sm-6">
            <div class="book_title" style="padding-left: 15px"><?php echo $event->bookInfo->name ?></div>
            <div class="project_title" style="padding-left: 15px">
                <?php echo ($event->project->sourceBible == "odb"
                        ? __($event->project->sourceBible)
                    : __($event->project->bookProject))." - ".$event->project->targetLanguage->langName ?>
            </div>
        </div>
        <div class="col-sm-6 start_translation">
            <?php if($event->state == EventStates::STARTED): ?>
                <form action="" method="post">
                    <button type="submit" name="submit" class="btn btn-warning" id="startTranslation" style="width: 150px; height: 50px;"><?php echo __("start_translation")?></button>
                </form>
            <?php else: ?>
                <div class="event_state"><?php echo __("event_status").": ".__("state_".$event->state) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="manage_body">
        <div class="manage_chapters">
            <h3><?php echo __("chapters") ?></h3>
            <ul>
                <?php foreach ($chapters as $chapter => $chapData): ?>
                    <?php
                    if(!empty($chapData))
                    {
                        $member = $members->find($chapData["memberID"]);
                        $userName = "unknown";
                        $userName = $member->userName;
                        $name = $member->firstName . " " . mb_substr($member->lastName, 0, 1).".";
                    }
                    ?>
                    <li style="position:relative;">
                        <div class="assignChapterLoader inline_f" data="<?php echo $chapter ?>">
                            <img src="<?php echo template_url("img/loader.gif") ?>" width="22">
                        </div>
                        <div class="manage_chapter">
                            <?php echo $chapter > 0 ? __("chapter_number", ["chapter" => $chapter]) : __("chapter_number", ["chapter" => __("intro")]); ?>
                            <?php if(isset($data["odb"]) && isset($data["odb"]["chapters"][$chapter])): ?>
                            <span class='glyphicon glyphicon-info-sign'
                                  data-toggle='tooltip'
                                  title="<?php echo $data["odb"]["chapters"][$chapter][1] ?>"
                                  style="font-size: 16px;"></span>
                            <?php elseif(isset($data["rad"]) && isset($data["rad"]["chapters"][$chapter])): ?>
                            <span class='glyphicon glyphicon-info-sign'
                                  data-toggle='tooltip'
                                  title="<?php echo $data["rad"]["chapters"][$chapter][1] . ": " . $data["rad"]["chapters"][$chapter][2] ?>"
                                  style="font-size: 16px;"></span>
                            <?php endif; ?>
                        </div>
                        <div class="manage_chapters_user chapter_<?php echo $chapter ?>">
                            <button class="btn btn-success add_person_chapter" data="<?php echo $chapter ?>" <?php echo !empty($chapData) ? 'style="display: none"' : '' ?>>
                                <?php echo __("add_person") ?>
                            </button>
                            <div class="manage_username" <?php echo !empty($chapData) ? 'style="display: block"' : '' ?>>
                                <div class="uname"><?php echo !empty($chapData) ? '<a href="/members/profile/'.$chapData["memberID"].'" target="_blank">'.$name.'</a>' : '' ?></div>
                                <div class="uname_delete glyphicon glyphicon-remove" data="<?php echo !empty($chapData) ? $chapData["memberID"] : '' ?>"></div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <?php if($event->project->bookProject == "sun"): ?>
                        <div class="manage_chapters_buttons" data-chapter="<?php echo $chapter ?>"
                             data-member="<?php echo !empty($chapData) ? $chapData["memberID"] : "" ?>">
                            <?php
                            $kw = !empty($chapData["kwCheck"])
                                && array_key_exists($chapter, $chapData["kwCheck"])
                                && $chapData["kwCheck"][$chapter]["memberID"] > 0;
                            $cr = !empty($chapData["crCheck"])
                                && array_key_exists($chapter, $chapData["crCheck"])
                                && $chapData["crCheck"][$chapter]["memberID"] > 0;
                            ?>
                            <?php if($kw): ?>
                                <div class="glyphicon glyphicon-menu-hamburger checker_remove_button"
                                     data-chapter="<?php echo $chapter?>"
                                     data-shown="0"></div>
                                <div class="checker_remove_menu" data-chapter="<?php echo $chapter?>">
                                    <div class="remove_menu_title"><?php echo __("remove_checker") ?></div>
                                    <button class="btn btn-danger remove_checker_alt" id="kw_checker"
                                            <?php echo $cr ? "disabled" : "" ?>>
                                        <?php echo __("sun".($event->project->sourceBible == "odb" ? "_odb" : "")."_theo_checker") ?>
                                    </button>
                                    <?php if($cr): ?>
                                        <button class="btn btn-danger remove_checker_alt" id="cr_checker"
                                                data-level="<?php echo $chapData["crCheck"][$chapter]["done"] ?>">
                                            <?php echo __("sun".($event->project->sourceBible == "odb" ? "_odb" : "")."_vbv_checker") ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php elseif (in_array($event->project->bookProject, ["tn", "tq"])): ?>
                        <div class="manage_chapters_buttons" data-chapter="<?php echo $chapter ?>"
                             data-member="<?php echo !empty($chapData) ? $chapData["memberID"] : "" ?>">
                            <?php
                            $other = !empty($chapData["otherCheck"])
                                && array_key_exists($chapter, $chapData["otherCheck"])
                                && $chapData["otherCheck"][$chapter]["memberID"] > 0;
                            $peer = !empty($chapData["peerCheck"])
                                && array_key_exists($chapter, $chapData["peerCheck"])
                                && $chapData["peerCheck"][$chapter]["memberID"] > 0;
                            ?>
                            <?php if($other): ?>
                                <div class="glyphicon glyphicon-menu-hamburger checker_remove_button"
                                     data-chapter="<?php echo $chapter?>"
                                     data-shown="0"></div>
                                <div class="checker_remove_menu" data-chapter="<?php echo $chapter?>">
                                    <div class="remove_menu_title"><?php echo __("remove_checker") ?></div>
                                    <button class="btn btn-danger remove_checker_alt" id="other_checker"
                                            data-level="<?php echo $chapData["otherCheck"][$chapter]["done"] ?>"
                                        <?php echo $peer ? "disabled" : "" ?>>
                                        <?php echo __("other_checker") ?>
                                    </button>
                                    <?php if($peer): ?>
                                        <button class="btn btn-danger remove_checker_alt" id="peer_checker"
                                                data-level="<?php echo $chapData["peerCheck"][$chapter]["done"] ?>">
                                            <?php echo __("other_peer_checker") ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php elseif ($event->project->bookProject == "rad"): ?>
                        <div class="manage_chapters_buttons" data-chapter="<?php echo $chapter ?>"
                             data-member="<?php echo !empty($chapData) ? $chapData["memberID"] : "" ?>">
                            <?php
                            $peer = !empty($chapData["peerCheck"])
                                && array_key_exists($chapter, $chapData["peerCheck"])
                                && $chapData["peerCheck"][$chapter]["memberID"] > 0;
                            ?>
                            <?php if($peer): ?>
                                <div class="glyphicon glyphicon-menu-hamburger checker_remove_button"
                                     data-chapter="<?php echo $chapter?>"
                                     data-shown="0"></div>
                                <div class="checker_remove_menu" data-chapter="<?php echo $chapter?>">
                                    <div class="remove_menu_title"><?php echo __("remove_checker") ?></div>
                                    <button class="btn btn-danger remove_checker_alt" id="peer_checker"
                                            data-level="<?php echo $chapData["peerCheck"][$chapter]["done"] ?>">
                                        <?php echo __("other_peer_checker") ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php elseif (in_array($event->project->bookProject, ["ulb", "udb"])): ?>
                        <div class="manage_chapters_buttons" data-chapter="<?php echo $chapter ?>"
                             data-member="<?php echo !empty($chapData) ? $chapData["memberID"] : "" ?>">
                            <?php
                            $verb = !empty($chapData["verbCheck"])
                                && array_key_exists($chapter, $chapData["verbCheck"]);
                            $peer = !empty($chapData["peerCheck"])
                                && array_key_exists($chapter, $chapData["peerCheck"])
                                && $chapData["peerCheck"][$chapter]["memberID"] > 0;
                            $kw = !empty($chapData["kwCheck"])
                                && array_key_exists($chapter, $chapData["kwCheck"])
                                && $chapData["kwCheck"][$chapter]["memberID"] > 0;
                            $cr = !empty($chapData["crCheck"])
                                && array_key_exists($chapter, $chapData["crCheck"])
                                && $chapData["crCheck"][$chapter]["memberID"] > 0;
                            $final = !empty($chapData["otherCheck"])
                                && array_key_exists($chapter, $chapData["otherCheck"]);
                            ?>
                            <?php if($verb): ?>
                                <div class="glyphicon glyphicon-menu-hamburger checker_remove_button"
                                     data-chapter="<?php echo $chapter?>"
                                     data-shown="0"></div>
                                <div class="checker_remove_menu" data-chapter="<?php echo $chapter?>">
                                    <div class="remove_menu_title"><?php echo __("remove_checker") ?></div>
                                    <button class="btn btn-danger remove_checker_alt" id="verb_checker"
                                            <?php echo $peer || $chapData["currentChapter"] != $chapter || $chapData["step"] != EventSteps::VERBALIZE ? "disabled" : "" ?>>
                                        <?php echo __("bible_verb_checker") ?>
                                    </button>
                                    <?php if($peer): ?>
                                        <button class="btn btn-danger remove_checker_alt" id="peer_checker"
                                                <?php echo $kw ? "disabled" : "" ?>>
                                            <?php echo __("bible_peer_checker") ?>
                                        </button>
                                    <?php endif; ?>
                                    <?php if($kw): ?>
                                        <button class="btn btn-danger remove_checker_alt" id="kw_checker"
                                                <?php echo $cr ? "disabled" : "" ?>
                                                data-level="<?php echo $chapData["kwCheck"][$chapter]["done"] ?>">
                                            <?php echo __("bible_keyword_checker") ?>
                                        </button>
                                    <?php endif; ?>
                                    <?php if($cr): ?>
                                        <button class="btn btn-danger remove_checker_alt" id="cr_checker"
                                                <?php echo $final ? "disabled" : "" ?>
                                                data-level="<?php echo $chapData["crCheck"][$chapter]["done"] ?>">
                                            <?php echo __("bible_vbv_checker") ?>
                                        </button>
                                    <?php endif; ?>
                                    <?php if($final): ?>
                                        <button class="btn btn-danger remove_checker_alt" id="final_checker"
                                                data-level="<?php echo $chapData["otherCheck"][$chapter]["done"] ?>">
                                            <?php echo __("bible_final_checker") ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="manage_members">
            <h3>
                <?php echo __("people_number", ["people_number" => sizeof($members)]) ?>
                <div class="manage_buttons">
                    <button
                            class="btn btn-primary"
                            id="openMembersSearch">
                        <?php echo __("add_translator") ?>
                    </button>
                    <button
                            class="btn btn-success glyphicon glyphicon-refresh"
                            id="refresh"
                            title="<?php echo __("refresh"); ?>">
                    </button>
                </div>
            </h3>
            <ul>
                <?php foreach ($members as $member):?>
                    <?php
                    $assignedChapters = $member->chapters->filter(function($chap) use($event) {
                        return $chap->eventID == $event->eventID;
                    })->getDictionary();
                    $chapterNumbers = array_map(function($chap) {
                        return $chap->chapter;
                    }, $assignedChapters);
                    ?>
                    <li>
                        <div class="member_usname" data="<?php echo $member->memberID ?>">
                            <a href="/members/profile/<?php echo $member->memberID ?>" target="_blank"><?php echo $member->firstName . " " . mb_substr($member->lastName, 0, 1)."."; ?></a>
                            (<span><?php echo sizeof($chapterNumbers); ?></span>)
                            <div class="glyphicon glyphicon-remove delete_user" title="<?php echo __("remove_from_event") ?>"></div>

                            <?php if(in_array($event->project->bookProject, ["tn","tq","rad"])): ?>
                            <label class="is_checker_label">
                                <input
                                    class="is_checker_input"
                                    type="checkbox"
                                    <?php echo $member->pivot->isChecker ? "checked" : "" ?>> <?php echo __("checking_tab_title") ?>
                            </label>
                            <?php endif; ?>
                        </div>
                        <div class="member_chapters" <?php echo !empty($chapterNumbers) ? "style='display:block'" : "" ?>>
                            <?php echo __("chapters").": <span><b>". join("</b>, <b>", $chapterNumbers)."</b></span>" ?>
                        </div>
                        <div class="step_selector_block row">
                            <div class="col-sm-6">
                                <?php
                                $mode = ($event->project->sourceBible == "odb" ? "odb" : "").$event->project->bookProject;
                                $s_disabled = EventSteps::enum($member->pivot->step, $mode) < 2;
                                ?>
                                <label><?php echo __("current_step") ?>:</label>
                                <select class="step_selector form-control"
                                    <?php echo $s_disabled ? "disabled" : "" ?>
                                        data-event="<?php echo $event->eventID ?>"
                                        data-member="<?php echo $member->memberID ?>"
                                        data-mode="<?php echo $mode ?>">
                                    <?php foreach (EventSteps::enumArray($mode) as $step => $i): ?>
                                        <?php
                                        // Skip None step
                                        if($step == EventSteps::NONE) continue;
                                        // Skip checking steps
                                        if($step == EventSteps::PEER_REVIEW || $step == EventSteps::KEYWORD_CHECK
                                            || $step == EventSteps::CONTENT_REVIEW || $step == EventSteps::FINAL_REVIEW
                                            || $step == EventSteps::FINISHED) {
                                            continue;
                                        }

                                        if($mode == "sun" || $mode == "odbsun") {
                                            if (EventSteps::enum($step, $mode) > EventSteps::enum(EventSteps::SELF_CHECK, $mode))
                                                continue;
                                        }

                                        $selected = $step == $member->pivot->step;
                                        $o_disabled = EventSteps::enum($member->pivot->step, $mode) < $i ||
                                            (EventSteps::enum($member->pivot->step, $mode) - $i) > 1;
                                        ?>

                                        <?php if($step == EventSteps::READ_CHUNK):
                                            $ch_disabled = $member->pivot->currentChunk <= 0 ||
                                                EventSteps::enum($member->pivot->step, $mode) >= EventSteps::enum(EventSteps::BLIND_DRAFT, $mode);
                                            ?>
                                            <option <?php echo ($ch_disabled ? "disabled" : "") ?>
                                                    value="<?php echo EventSteps::BLIND_DRAFT."_prev" ?>">
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <?php echo __(EventSteps::BLIND_DRAFT."_previous").($member->pivot->currentChunk > 0 ? " ".$member->pivot->currentChunk : "") ?>
                                            </option>
                                        <?php endif; ?>

                                        <?php if($step == EventSteps::REARRANGE):
                                            $ch_disabled = ($member->pivot->currentChunk <= 0 && $member->pivot->step != EventSteps::SYMBOL_DRAFT) ||
                                                ($member->pivot->step == EventSteps::SYMBOL_DRAFT && $member->pivot->currentChunk > 0) ||
                                                (EventSteps::enum($member->pivot->step, $mode) - EventSteps::enum($step, $mode)) > 1;

                                            //$chunks = (array)json_decode($member["chunks"], true);
                                            $currentChunk = $member->pivot->currentChunk > 0 || $member->pivot->step != EventSteps::SYMBOL_DRAFT
                                                ? $member->pivot->currentChunk
                                                : 0;
                                            ?>
                                            <option <?php echo ($ch_disabled ? "disabled" : "") ?>
                                                    value="<?php echo EventSteps::REARRANGE."_prev" ?>">
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <?php echo __(EventSteps::REARRANGE."_previous").($currentChunk > 0 ? " ".$currentChunk : "") ?>
                                            </option>
                                        <?php endif; ?>

                                        <?php if($step == EventSteps::SYMBOL_DRAFT):
                                            $ch_disabled = $member->pivot->currentChunk <= 0 ||
                                                EventSteps::enum($member->pivot->step, $mode) < EventSteps::enum($step, $mode) ||
                                                (EventSteps::enum($member->pivot->step, $mode) - EventSteps::enum($step, $mode)) > 1;

                                            //$chunks = (array)json_decode($member["chunks"], true);
                                            $currentChunk = $member->pivot->step != EventSteps::SELF_CHECK
                                                ? $member->pivot->currentChunk
                                                : 0;
                                            ?>
                                            <option <?php echo ($ch_disabled ? "disabled" : "") ?>
                                                    value="<?php echo EventSteps::SYMBOL_DRAFT."_prev" ?>">
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <?php echo __(EventSteps::SYMBOL_DRAFT."_previous").($member->pivot->currentChunk > 0 ? " ".$currentChunk : "") ?>
                                            </option>
                                        <?php endif; ?>

                                        <option <?php echo ($selected ? " selected" : "").($o_disabled ? " disabled" : "") ?> value="<?php echo $step ?>">
                                            <?php
                                            // Multistep is the step with sub steps
                                            // read-chunk, rearrange, symbol-draft,  etc...
                                            if($mode == "tn")
                                                $multiStep = 3;
                                            elseif($mode == "sun")
                                                $multiStep = 4;
                                            elseif($mode == "odbsun")
                                                $multiStep = 3;
                                            elseif(in_array($mode, ["tq","tw","rad"]))
                                                $multiStep = 0;
                                            else
                                                $multiStep = 5;

                                            $add = "";

                                            if($step != EventSteps::PRAY)
                                            {
                                                $add = in_array($mode, ["tn"]) ? "_" . $mode : "";
                                            }
                                            if($step == EventSteps::CHUNKING && $mode == "sun")
                                                $add = "_sun";
                                            echo EventSteps::enum($step, $mode) == $multiStep ? __($step."-alt") : __($step.$add)
                                            ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<input type="hidden" id="eventID" value="<?php echo $event->eventID ?>"/>
<input type="hidden" id="mode" value="<?php echo $event->project->bookProject ?>"/>

<div class="chapter_members">
    <div class="chapter_members_div panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title"><?php echo __("assign_chapter_title")?> <span></span></h1>
            <span class="chapter-members-close glyphicon glyphicon-remove-sign"></span>
        </div>
        <div class="assignChapterLoader dialog_f">
            <img src="<?php echo template_url("img/loader.gif") ?>">
        </div>
        <ul>
            <?php foreach ($members as $member): ?>
                <?php
                $assignedChapters = $member->chapters->filter(function($chap) use($event) {
                    return $chap->eventID == $event->eventID;
                })->getDictionary();
                $chapterNumbers = array_map(function($chap) {
                    return $chap->chapter;
                }, $assignedChapters);
                ?>
            <li>
                <div class="member_usname userlist chapter_ver">
                    <div class="divname"><?php echo $member->firstName . " " . mb_substr($member->lastName, 0, 1)."."; ?></div>
                    <div class="divvalue">(<span><?php echo sizeof($chapterNumbers) ?></span>)</div>
                </div>
                <button class="btn btn-success assign_chapter" data="<?php echo $member->memberID ?>"><?php echo __("assign") ?></button>
                <div class="clear"></div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="members_search_dialog">
    <div class="members_search_dialog_div panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title"><?php echo __("add_translator")?> <span></span></h1>
            <span class="members-search-dialog-close glyphicon glyphicon-remove-sign"></span>
        </div>
        <div class="openMembersSearch dialog_f">
            <img src="<?php echo template_url("img/loader.gif") ?>">
        </div>
        <div class="members-search-dialog-content">
            <div class="form-group">
                <input type="text" class="form-control input-lg" id="user_translator" placeholder="Enter a name" required="">
            </div>
            <ul class="user_translators">

            </ul>
        </div>
    </div>
</div>
<?php else: ?>
    <a href="#" onclick="history.back(); return false"><?php echo __('go_back')?></a>
<?php endif; ?>

<script>
    isManagePage = true;
    manageMode = "l1";
    userType = EventMembers.TRANSLATOR;

    $(document).ready(function () {
        $('.step_selector').each(function () {
            $('option', this).each(function () {
                if (this.defaultSelected) {
                    this.selected = true;
                    return false;
                }
            });
        });
    });
</script>
