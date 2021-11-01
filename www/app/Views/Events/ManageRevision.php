<?php
use Helpers\Constants\EventCheckSteps;
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
            <div class="project_title" style="padding-left: 15px"><?php echo __($event->project->bookProject)." - ".$event->project->targetLanguage->langName ?></div>
        </div>
        <div class="col-sm-6 start_translation">
            <?php if($event->state == EventStates::L2_RECRUIT): ?>
                <form action="" method="post">
                    <button type="submit" name="submit" class="btn btn-warning" id="startTranslation" style="width: 150px; height: 50px;"><?php echo __("start_checking")?></button>
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
                        $member = $members->find($chapData["l2memberID"]);
                        $name = $member
                            ? $member->firstName . " " . mb_substr($member->lastName, 0, 1)."."
                            : $chapData["l2memberID"];
                    }
                    ?>
                    <li class="row" style="position:relative;">
                        <div class="assignChapterLoader inline_f" data="<?php echo $chapter ?>">
                            <img src="<?php echo template_url("img/loader.gif") ?>" width="22">
                        </div>
                        <div class="manage_chapter">
                            <?php echo $chapter > 0 ? __("chapter_number", ["chapter" => $chapter]) : __("chapter_number", ["chapter" => __("intro")]); ?>
                        </div>
                        <div class="manage_chapters_user chapter_<?php echo $chapter ?>">
                            <button class="btn btn-success add_person_chapter" data="<?php echo $chapter ?>" <?php echo !empty($chapData) ? 'style="display: none"' : '' ?>>
                                <?php echo __("add_person") ?>
                            </button>
                            <div class="manage_username" <?php echo !empty($chapData) ? 'style="display: block"' : '' ?>>
                                <div class="uname"><?php echo !empty($chapData) ? '<a href="/members/profile/'.$chapData["l2memberID"].'" target="_blank">'.$name.'</a>' : '' ?></div>
                                <div class="uname_delete glyphicon glyphicon-remove" data="<?php echo !empty($chapData) ? $chapData["l2memberID"] : '' ?>"></div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <div class="manage_chapters_buttons" data-chapter="<?php echo $chapter ?>"
                                data-member="<?php echo !empty($chapData) ? $chapData["l2memberID"] : "" ?>">
                            <?php
                            $peer = !empty($chapData["peerCheck"])
                                && array_key_exists($chapter, $chapData["peerCheck"])
                                && $chapData["peerCheck"][$chapter]["memberID"] > 0;
                            $kw = !empty($chapData["kwCheck"])
                                && array_key_exists($chapter, $chapData["kwCheck"])
                                && $chapData["kwCheck"][$chapter]["memberID"] > 0;
                            $cr = !empty($chapData["crCheck"])
                                && array_key_exists($chapter, $chapData["crCheck"])
                                && $chapData["crCheck"][$chapter]["memberID"] > 0;

                            $peerName = "unknown";
                            $kwName = "unknown";
                            $crName = "unknown";

                            if ($peer) {
                                $member = $members->find($chapData["peerCheck"][$chapter]["memberID"]);
                                $peerName = $member
                                    ? $member->firstName . " " . mb_substr($member->lastName, 0, 1)."."
                                    : $chapData["peerCheck"][$chapter]["memberID"];
                            }
                            if ($kw) {
                                $member = $members->find($chapData["kwCheck"][$chapter]["memberID"]);
                                $kwName = $member
                                    ? $member->firstName . " " . mb_substr($member->lastName, 0, 1)."."
                                    : $chapData["kwCheck"][$chapter]["memberID"];
                            }
                            if ($cr) {
                                $member = $members->find($chapData["crCheck"][$chapter]["memberID"]);
                                $crName = $member ?
                                    $member->firstName . " " . mb_substr($member->lastName, 0, 1)."."
                                    : $chapData["crCheck"][$chapter]["memberID"];
                            }
                            ?>
                            <?php if($peer): ?>
                                <div class="glyphicon glyphicon-menu-hamburger checker_remove_button"
                                     data-chapter="<?php echo $chapter?>"
                                     data-shown="0"></div>
                                <div class="checker_remove_menu" data-chapter="<?php echo $chapter?>">
                                    <div class="remove_menu_title"><?php echo __("remove_checker") ?></div>
                                    <button class="btn btn-danger remove_checker_alt" id="peer_checker"
                                            data-name="<?php echo $peerName ?>"
                                            data-level="<?php echo $chapData["peerCheck"][$chapter]["done"] ?>"
                                            <?php echo $kw ? "disabled" : "" ?>>
                                        <?php echo __("bible_peer_checker"); ?>
                                    </button>
                                    <?php if($kw): ?>
                                        <button class="btn btn-danger remove_checker_alt" id="kw_checker"
                                                data-name="<?php echo $kwName ?>"
                                                <?php echo $cr ? "disabled" : "" ?>>
                                            <?php echo __("bible_keyword_checker") ?>
                                        </button>
                                        <?php if($cr): ?>
                                            <button class="btn btn-danger remove_checker_alt" id="cr_checker"
                                                    data-name="<?php echo $crName ?>">
                                                <?php echo __("bible_vbv_checker") ?>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
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
                        <?php echo __("add_checker") ?>
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
                    $assignedChapters = $member->chaptersL2->filter(function($chap) use($event) {
                        return $chap->eventID == $event->eventID;
                    })->getDictionary();
                    $chapterNumbers = array_map(function($chap) {
                        return $chap->chapter;
                    }, $assignedChapters);
                    ?>
                    <li>
                        <div class="member_usname" data="<?php echo $member->memberID ?>">
                            <a href="/members/profile/<?php echo $member->memberID ?>" target="_blank"><?php echo $member->firstName . " " . mb_substr($member->lastName, 0, 1)."."; ?></a>
                            (<span><?php echo sizeof($chapterNumbers) ?></span>)
                            <div class="glyphicon glyphicon-remove delete_user" title="<?php echo __("remove_from_event") ?>"></div>
                        </div>
                        <div class="member_chapters" <?php echo !empty($chapterNumbers) ? "style='display:block'" : "" ?>>
                            <?php echo __("chapters").": <span><b>". join("</b>, <b>", $chapterNumbers)."</b></span>" ?>
                        </div>
                        <div class="step_selector_block row">
                            <div class="col-sm-6">
                                <?php
                                $mode = "l2";
                                $s_disabled = EventCheckSteps::enum($member->pivot->step, $mode) < 2;
                                ?>
                                <label><?php echo __("current_step") ?>:</label>
                                <select class="step_selector form-control"
                                    <?php echo $s_disabled ? "disabled" : "" ?>
                                    data-event="<?php echo $event->eventID ?>"
                                    data-member="<?php echo $member->memberID ?>"
                                    data-mode="<?php echo $mode ?>">
                                    <?php foreach (EventCheckSteps::enumArray($mode) as $step => $i): ?>
                                        <?php
                                        // Skip None step
                                        if($step == EventCheckSteps::NONE) continue;

                                        $add = "";
                                        if ($step == EventCheckSteps::SELF_CHECK && $event->project->bookProject == "sun") {
                                            $add = "_sun";
                                        }

                                        $selected = $step == $member->pivot->step;
                                        $o_disabled = EventCheckSteps::enum($member->pivot->step, $mode) < $i ||
                                            (EventCheckSteps::enum($member->pivot->step, $mode) - $i) > 1;
                                        ?>

                                        <option <?php echo ($selected ? " selected" : "").($o_disabled ? " disabled" : "") ?> value="<?php echo $step ?>">
                                            <?php echo __($step.$add) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <?php
                                $showButton = false;
                                if($member->pivot->step == EventCheckSteps::CONTENT_REVIEW)
                                {
                                    if($member["checkerID"] > 0)
                                        $showButton = true;
                                    else
                                    {
                                        $peerCheck = (array)json_decode($member->pivot->peerCheck, true);
                                            if(array_key_exists($member->pivot->currentChapter, $peerCheck))
                                                $showButton = true;
                                    }
                                }

                                if($showButton):
                                ?>
                                <button class="remove_checker btn btn-danger" style="margin-top: 22px;"
                                        data="<?php echo $event->eventID.":".$member->memberID ?>"
                                        data2="<?php echo $member->pivot->step ?>">
                                    <?php echo __("remove_checker") ?>
                                </button>
                                <?php endif; ?>
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
            <h1 class="panel-title"><?php echo __("assign_chapter_title")?> <span></span></h1>
            <span class="chapter-members-close glyphicon glyphicon-remove-sign"></span>
        </div>
        <div class="assignChapterLoader dialog_f">
            <img src="<?php echo template_url("img/loader.gif") ?>">
        </div>
        <ul>
            <?php foreach ($members as $member): ?>
            <?php
                $assignedChapters = $member->chaptersL2->filter(function($chap) use($event) {
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
            <h1 class="panel-title"><?php echo __("add_checker")?> <span></span></h1>
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
    manageMode = "l2";
    userType = EventMembers.L2_CHECKER;

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
