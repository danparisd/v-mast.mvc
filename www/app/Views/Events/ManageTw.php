<?php
use Helpers\Constants\EventSteps;
use Helpers\Constants\EventStates;
use Shared\Legacy\Error;

echo Error::display($error);

$groups = [];

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
                <div class="project_title" style="padding-left: 15px"><?php echo __($event->project->bookProject)." - ".$event->project->targetLanguage->langName ?></div>
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
                <div id="word_group_block">
                    <button class="btn btn-primary" id="word_group_create"><?php echo __("create_words_group") ?></button>
                </div>
                <ul>
                    <?php $group_order = 1; foreach ($chapters as $chapter => $chapData): ?>
                        <?php
                        $group_name = null;
                        foreach ($data["tw_groups"] as $tw_group) {
                            if($tw_group->groupID == $chapter)
                            {
                                $words = (array) json_decode($tw_group->words, true);
                                $group_name = join(", ", $words);
                                break;
                            }
                        }

                        if(!empty($chapData))
                        {
                            $member = $members->find($chapData["memberID"]);
                            $userName = "unknown";
                            $userName = $member->userName;
                            $name = $member->firstName . " " . mb_substr($member->lastName, 0, 1).".";

                            if (!array_key_exists($member->memberID, $groups)) {
                                $groups[$member->memberID] = [];
                            }
                            $groups[$member->memberID][] = $group_order;

                            //$data["members"][$key]["assignedGroups"][] = $group_order;
                        }
                        ?>
                        <li style="position:relative;">
                            <div class="assignChapterLoader inline_f" data="<?php echo $chapter ?>">
                                <img src="<?php echo template_url("img/loader.gif") ?>" width="22">
                            </div>
                            <div class="manage_chapter">
                                <?php echo __("group_id", $group_order); ?>
                                <span class='glyphicon glyphicon-info-sign'
                                      data-toggle='tooltip'
                                      title="<?php echo $group_name ? $group_name : "" ?>"
                                      style="font-size: 16px;"></span>
                                <div class="group_delete glyphicon glyphicon-remove" data-groupid="<?php echo $chapter ?>"></div>
                            </div>
                            <div class="manage_chapters_user chapter_<?php echo $chapter ?>">
                                <button class="btn btn-success add_person_chapter"
                                        data="<?php echo $chapter ?>"
                                        data-group="<?php echo $group_order ?>" <?php echo !empty($chapData) ? 'style="display: none"' : '' ?>>
                                    <?php echo __("assign") ?>
                                </button>
                                <div class="manage_username" <?php echo !empty($chapData) ? 'style="display: block"' : '' ?>>
                                    <div class="uname"><?php echo !empty($chapData) ? '<a href="/members/profile/'.$chapData["memberID"].'" target="_blank">'.$name.'</a>' : '' ?></div>
                                    <div class="uname_delete glyphicon glyphicon-remove" data="<?php echo !empty($chapData) ? $chapData["memberID"] : '' ?>"></div>
                                    <div class="clear"></div>
                                </div>
                            </div>
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
                        </li>
                        <?php $group_order++; endforeach; ?>
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
                        $assignedGroups = array_key_exists($member->memberID, $groups) ? $groups[$member->memberID] : [];
                        ?>
                        <li>
                            <div class="member_usname" data="<?php echo $member->memberID ?>">
                                <a href="/members/profile/<?php echo $member->memberID ?>" target="_blank"><?php echo $member->firstName . " " . mb_substr($member->lastName, 0, 1)."."; ?></a>
                                (<span><?php echo sizeof($assignedGroups) ?></span>)
                                <div class="glyphicon glyphicon-remove delete_user" title="<?php echo __("remove_from_event") ?>"></div>

                                <label class="is_checker_label">
                                    <input
                                            class="is_checker_input"
                                            type="checkbox"
                                        <?php echo $member->pivot->isChecker ? "checked" : "" ?>> <?php echo __("checking_tab_title") ?>
                                </label>
                            </div>
                            <div class="member_chapters" <?php echo !empty($assignedGroups) ? "style='display:block'" : "" ?>>
                                <?php echo __("chapters").": <span>". join(", ", $assignedGroups)."</span>" ?>
                            </div>
                            <div class="step_selector_block row">
                                <div class="col-sm-6">
                                    <?php
                                    $mode = $event->project->bookProject;
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

                                            $selected = $step == $member->pivot->step;
                                            $o_disabled = EventSteps::enum($member->pivot->step, $mode) < $i ||
                                                (EventSteps::enum($member->pivot->step, $mode) - $i) > 1;
                                            ?>

                                            <option <?php echo ($selected ? " selected" : "").($o_disabled ? " disabled" : "") ?> value="<?php echo $step ?>">
                                                <?php echo __($step) ?>
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

    <input type="hidden" id="eventID" value="<?php echo $event->eventID ?>">
    <input type="hidden" id="mode" value="<?php echo $event->project->bookProject ?>">

    <div class="chapter_members">
        <div class="chapter_members_div panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title"><?php echo __("assign_group_title")?> <span></span></h1>
                <span class="chapter-members-close glyphicon glyphicon-remove-sign"></span>
            </div>
            <div class="assignChapterLoader dialog_f">
                <img src="<?php echo template_url("img/loader.gif") ?>">
            </div>
            <ul>
                <?php foreach ($members as $member): ?>
                    <?php
                    $assignedGroups = [];
                    ?>
                    <li>
                        <div class="member_usname userlist chapter_ver">
                            <div class="divname"><?php echo $member->firstName . " " . mb_substr($member->lastName, 0, 1)."."; ?></div>
                            <div class="divvalue">(<span><?php echo sizeof($assignedGroups) ?></span>)</div>
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

    <div class="words_group_dialog">
        <div class="words_group_dialog_div panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title"><?php echo __("create_words_group")?> <span></span></h1>
                <span class="words-group-dialog-close glyphicon glyphicon-remove-sign"></span>
            </div>
            <div class="openWordsGroup dialog_f">
                <img src="<?php echo template_url("img/loader.gif") ?>">
            </div>
            <div class="words-group-dialog-content">
                <div class="word_group_hint"><?php echo __("word_group_hint") ?></div>
                <div class="form-group">
                    <select class="form-control input-lg" id="word_group" multiple>
                        <?php foreach ($data["words"] as $word): ?>
                            <option <?php echo in_array($word["word"], $data["words_in_groups"]) ? "disabled" : "" ?>>
                                <?php echo $word["word"] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="text-align: right">
                    <button class="btn btn-success" id="create_group"><?php echo __("create_group") ?></button>
                </div>
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
