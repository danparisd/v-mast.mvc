<div class="manage_container row">
    <div class="row">
        <div class="col-sm-9">
            <div class="book_title" style="padding-left: 15px"><?php echo $data["event"][0]->name ?></div>
            <div class="project_title" style="padding-left: 15px"><?php echo __($data["event"][0]->bookProject)." - ".$data["event"][0]->langName ?></div>
        </div>
        <div class="col-sm-3 start_translation">
            <?php if($data["event"][0]->state == "started"): ?>
                <form action="" method="post">
                    <button type="submit" name="submit" class="btn btn-warning" id="startTranslation" style="width: 150px; height: 50px;"><?php echo __("start_translation")?></button>
                </form>
            <?php else: ?>
                <div class="event_state"><?php echo __("state_".$data["event"][0]->state) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="manage_chapters col-sm-4">
        <h3><?php echo __("chapters") ?></h3>
        <ul>
            <?php foreach ($data["chapters"] as $chapter => $chapData): ?>
                <?php
                if(!empty($chapData))
                {
                    $userName = "unknown";
                    $key = array_search($chapData["memberID"], array_column($data["members"], 'memberID'));
                    $userName = $data["members"][$key]["userName"];
                    $data["members"][$key]["assignedChapters"][] = $chapter;
                }
                ?>
                <li class="row" style="position:relative;">
                    <div class="assignChapterLoader inline_f" data="<?php echo $chapter ?>">
                        <img src="<?php echo template_url("img/loader.gif") ?>" width="22">
                    </div>
                    <div class="col-sm-4 manage_chapter"><?php echo __("chapter_number", array($chapter)); ?></div>
                    <div class="col-sm-8 manage_chapters_user chapter_<?php echo $chapter ?>">
                        <button class="btn btn-success add_person_chapter" data="<?php echo $chapter ?>" <?php echo !empty($chapData) ? 'style="display: none"' : '' ?>>
                            <?php echo __("add_person") ?>
                        </button>
                        <div class="manage_username" <?php echo !empty($chapData) ? 'style="display: block"' : '' ?>>
                            <div class="uname"><?php echo !empty($chapData) ? $userName : '' ?></div>
                            <div class="uname_delete glyphicon glyphicon-remove" data="<?php echo !empty($chapData) ? $chapData["memberID"] : '' ?>"></div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="manage_members col-sm-4">
        <h3><?php echo __("people_number", array(sizeof($data["members"]), $data["event"][0]->translatorsNum)) ?></h3>
        <ul>
            <?php foreach ($data["members"] as $member):?>
                <li>
                    <div class="member_usname" data="<?php echo $member["memberID"] ?>"><?php echo $member["userName"]; ?> (<span><?php echo isset($member["assignedChapters"]) ? sizeof($member["assignedChapters"]) : 0 ?></span>)</div>
                    <div class="member_chapters" <?php echo isset($member["assignedChapters"]) ? "style='display:block'" : "" ?>>
                        <?php echo __("chapters").": <span><b>". (isset($member["assignedChapters"]) ? join("</b>, <b>", $member["assignedChapters"]) : "")."</b></span>" ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="manage_pairs col-sm-4">
        <h3><?php echo __("pairs_number", array(floor(sizeof($data["members"])/2))) ?></h3>
        <ul>
            <?php for ($i=1; $i <= floor(sizeof($data["members"])/2); $i++): ?>
            <li>
                <div class="pair_block pair_<?php echo $i ?>">
                    <div class="assignPairLoader inline_f" data="<?php echo $i ?>">
                        <img src="<?php echo template_url("img/loader_alt.gif") ?>">
                    </div>
                    <h4><?php echo __("pair_number", array($i)); ?></h4>
                    <div class="pair_member1">
                        <?php if(isset($data["pairs"][$i])): ?>
                            <?php echo $data["pairs"][$i][0]["userName"] ?>
                        <?php endif; ?>
                    </div>
                    <div class="pair_member2">
                        <?php if(isset($data["pairs"][$i]) && isset($data["pairs"][$i][1])): ?>
                            <?php echo $data["pairs"][$i][1]["userName"] ?>
                        <?php endif; ?>
                    </div>
                    <div class="create_pair_block pair_button" style="<?php echo isset($data["pairs"][$i]) && sizeof($data["pairs"][$i]) == 2 ? "display:none" : "" ?>">
                        <button class="btn btn-success add_person_pair" data="<?php echo $i ?>"><?php echo __("assign_pair_title") ?></button>
                    </div>
                    <div class="reset_pair_block pair_button" style="<?php echo sizeof($data["pairs"][$i]) == 2 ? "display:block" : "" ?>">
                        <button class="btn btn-danger reset_pair" data="<?php echo $i ?>"><?php echo __("reset_pair_title") ?></button>
                    </div>
                </div>
            </li>
            <?php endfor; ?>
        </ul>
    </div>

    <div class="clear"></div>
</div>

<input type="hidden" id="eventID" value="<?php echo $data["event"][0]->eventID ?>">


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
            <?php foreach ($data["members"] as $member): ?>
            <li>
                <div class="member_usname userlist chapter_ver">
                    <div class="divname"><?php echo $member["userName"]; ?></div>
                    <div class="divvalue">(<span><?php echo isset($member["assignedChapters"]) ? sizeof($member["assignedChapters"]) : 0 ?></span>)</div>
                </div>
                <button class="btn btn-success assign_chapter" data="<?php echo $member["memberID"] ?>"><?php echo __("assign") ?></button>
                <div class="clear"></div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="pair_members">
    <div class="pair_members_div panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title"><?php echo __("assign_pair_title")?> <span></span></h1>
            <span class="pair-members-close glyphicon glyphicon-remove-sign"></span>
        </div>
        <div class="assignPairLoader dialog_f">
            <img src="<?php echo template_url("img/loader.gif") ?>">
        </div>
        <h4 style="margin: 10px 0 0 10px;"><?php echo __("check_pair_members") ?></h4>
        <ul class="col-sm-7">
            <?php foreach ($data["members"] as $member): ?>
                <?php if($member["pairOrder"] > 0) continue; ?>
                <li>
                    <div class="member_usname userlist"><?php echo $member["userName"]; ?></div>
                    <input type="checkbox" class="checkForPair" data="<?php echo $member["memberID"] ?>">
                    <div class="clear"></div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="col-sm-5 assign_pair_button">
            <button class="btn btn-success assign_pair" disabled><?php echo __("assign_pair_title") ?></button>
            <div class="assigned_members"></div>
        </div>
        <div class="clear"></div>
    </div>
</div>

<script>
    isManagePage = true;
</script>