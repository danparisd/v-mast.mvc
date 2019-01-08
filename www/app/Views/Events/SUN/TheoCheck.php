<?php
if(isset($data["error"])) return;

use Helpers\Constants\EventMembers;
?>
<div class="comment_div panel panel-default font_sun">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __("write_note_title")?></h1>
        <span class="editor-close btn btn-success"><?php echo __("save") ?></span>
        <span class="xbtn glyphicon glyphicon-remove"></span>
    </div>
    <textarea style="overflow-x: hidden; word-wrap: break-word; overflow-y: visible;" class="textarea textarea_editor"></textarea>
    <div class="other_comments_list"></div>
    <img src="<?php echo template_url("img/loader.gif") ?>" class="commentEditorLoader">
</div>

<div id="translator_contents" class="row panel-body">
    <div class="row main_content_header">
        <div class="main_content_title"><?php echo __("step_num", [6]) . ": " . __("theo-check")?></div>
    </div>

    <div class="row" style="position: relative">
        <div class="main_content col-sm-9">
            <form action="" id="main_form" method="post" >
                <div class="main_content_text" dir="<?php echo $data["event"][0]->sLangDir ?>">
                    <h4><?php echo $data["event"][0]->tLang." - "
                            .__($data["event"][0]->bookProject)." - "
                        .($data["event"][0]->abbrID <= 39 ? __("old_test") : __("new_test"))." - "
                        ."<span class='book_name'>".$data["event"][0]->name." ".$data["currentChapter"].":1-".$data["totalVerses"]."</span>"?></h4>

                    <div class="no_padding">
                        <?php foreach($data["chunks"] as $key => $chunk) : ?>
                            <div class="row chunk_block">
                                <div class="chunk_verses col-sm-6" dir="<?php echo $data["event"][0]->sLangDir ?>">
                                    <?php $firstVerse = 0; ?>
                                    <?php foreach ($chunk as $verse): ?>
                                        <?php
                                        // process combined verses
                                        if (!isset($data["text"][$verse]))
                                        {
                                            if($firstVerse == 0)
                                            {
                                                $firstVerse = $verse;
                                                continue;
                                            }
                                            $combinedVerse = $firstVerse . "-" . $verse;

                                            if(!isset($data["text"][$combinedVerse]))
                                                continue;
                                            $verse = $combinedVerse;
                                        }
                                        ?>
                                        <div>
                                            <strong dir="<?php echo $data["event"][0]->sLangDir ?>"
                                                    class="<?php echo $data["event"][0]->sLangDir ?>">
                                                <sup><?php echo $verse; ?></sup>
                                            </strong>
                                            <div class="<?php echo "kwverse_".$data["currentChapter"]."_".$key."_".$verse ?>"
                                                 dir="<?php echo $data["event"][0]->sLangDir ?>">
                                                <?php echo $data["text"][$verse]; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="col-sm-6 editor_area" dir="<?php echo $data["event"][0]->tLangDir ?>">
                                    <?php
                                    $bt = $data["translation"][$key][EventMembers::TRANSLATOR]["bt"];
                                    ?>
                                    <div class="vnote">
                                        <div class="verse_block font_backsun">
                                            <p><?php echo $bt; ?></p>
                                        </div>

                                        <?php $hasComments = array_key_exists($data["currentChapter"], $data["comments"]) && array_key_exists($key, $data["comments"][$data["currentChapter"]]); ?>
                                        <div class="comments_number <?php echo $hasComments ? "hasComment" : "" ?>">
                                            <?php echo $hasComments ? sizeof($data["comments"][$data["currentChapter"]][$key]) : ""?>
                                        </div>
                                        <img class="editComment" data="<?php echo $data["currentChapter"].":".$key ?>" width="16" src="<?php echo template_url("img/edit.png") ?>" title="<?php echo __("write_note_title", [""])?>"/>

                                        <div class="comments">
                                            <?php if(array_key_exists($data["currentChapter"], $data["comments"]) && array_key_exists($key, $data["comments"][$data["currentChapter"]])): ?>
                                                <?php foreach($data["comments"][$data["currentChapter"]][$key] as $comment): ?>
                                                    <?php if($comment->memberID == $data["event"][0]->myChkMemberID
                                                        && $comment->level == 2): ?>
                                                        <div class="my_comment"><?php echo $comment->text; ?></div>
                                                    <?php else: ?>
                                                        <div class="other_comments">
                                                            <?php echo
                                                                "<span>".$comment->firstName." ".mb_substr($comment->lastName, 0, 1).". 
                                                                    (L".$comment->level."):</span> 
                                                                ".$comment->text; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="chunk_divider col-sm-12"></div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="main_content_footer">
                    <div class="form-group">
                        <div class="main_content_confirm_desc"><?php echo __("confirm_finished")?></div>
                        <label><input name="confirm_step" id="confirm_step" type="checkbox" value="1" /> <?php echo __("confirm_yes")?></label>
                    </div>

                    <input type="hidden" name="level" value="l2">
                    <button id="next_step" type="submit" name="submit" class="btn btn-primary" disabled><?php echo __("continue")?></button>
                </div>
            </form>
            <div class="step_right alt"><?php echo __("step_num", [6])?></div>
        </div>

        <div class="content_help col-sm-3">
            <div class="help_float">
                <div class="help_info_steps is_checker_page_help">
                    <div class="help_hide toggle-help glyphicon glyphicon-eye-close" title="<?php echo __("hide_help") ?>"></div>
                    <div class="help_title_steps"><?php echo __("help") ?></div>

                    <div class="clear"></div>

                    <div class="help_name_steps">
                        <span><?php echo __("step_num", [6])?>: </span>
                        <?php echo __("theo-check")?>
                    </div>
                    <div class="help_descr_steps">
                        <ul><?php echo __("theo-check_desc")?></ul>
                        <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
                    </div>
                </div>

                <div class="event_info is_checker_page_help">
                    <div class="participant_info">
                        <div class="additional_info">
                            <a href="/events/information-sun/<?php echo $data["event"][0]->eventID ?>"><?php echo __("event_info") ?></a>
                        </div>
                    </div>
                </div>

                <div class="tr_tools">
                    <button class="btn btn-primary ttools" data-tool="tn"><?php echo __("show_notes") ?></button>
                    <button class="btn btn-primary ttools" data-tool="tw"><?php echo __("show_keywords") ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="help_show toggle-help glyphicon glyphicon-question-sign" data-mode="l2continue" title="<?php echo __("show_help") ?>"></div>
</div>

<div class="tutorial_container">
    <div class="tutorial_popup">
        <div class="tutorial-close glyphicon glyphicon-remove"></div>
        <div class="tutorial_pic">
            <img src="<?php echo template_url("img/steps/icons/keyword-check.png") ?>" width="100px" height="100px">
            <img src="<?php echo template_url("img/steps/big/keyword-check.png") ?>" width="280px" height="280px">
            <div class="hide_tutorial">
                <label><input id="hide_tutorial" data="<?php echo $data["event"][0]->step ?>" type="checkbox" value="0" /> <?php echo __("do_not_show_tutorial")?></label>
            </div>
        </div>

        <div class="tutorial_content is_checker_page_help">
            <h3><?php echo __("theo-check")?></h3>
            <ul><?php echo __("theo-check_desc")?></ul>
        </div>
    </div>
</div>

<?php if(!empty($data["notes"])): ?>
    <div class="ttools_panel tn_tool panel panel-default" draggable="true">
        <div class="panel-heading">
            <h1 class="panel-title"><?php echo __("tn") ?></h1>
            <span class="panel-close glyphicon glyphicon-remove" data-tool="tn"></span>
        </div>

        <div class="ttools_content page-content panel-body">
            <div class="labels_list">
                <?php if(isset($data["notes"])): ?>
                    <?php foreach ($data["notes"] as $verse => $notes): ?>
                        <?php $chunkVerses = $data["notesVerses"][$verse]; ?>
                        <label>
                            <ul>
                                <li>
                                    <div class="word_term">
                            <span style="font-weight: bold;">
                                <?php echo $chunkVerses > 0 ? __("verse_number", $chunkVerses) :
                                    __("intro")?>
                            </span>
                                    </div>
                                    <div class="word_def">
                                        <?php foreach ($notes as $note): ?>
                                            <?php echo  preg_replace('#<a.*?>(.*?)</a>#i', '<b>\1</b>', $note) ?>
                                        <?php endforeach; ?>
                                    </div>
                                </li>
                            </ul>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="word_def_popup">
                <div class="word_def-close glyphicon glyphicon-remove"></div>

                <div class="word_def_title"></div>
                <div class="word_def_content"></div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if(!empty($data["keywords"]) && !empty($data["keywords"]["words"])): ?>
    <div class="ttools_panel tw_tool panel panel-default" draggable="true">
        <div class="panel-heading">
            <h1 class="panel-title"><?php echo __("tw") ?></h1>
            <span class="panel-close glyphicon glyphicon-remove" data-tool="tw"></span>
        </div>

        <div class="ttools_content page-content panel-body">
            <div class="labels_list">
                <?php if(isset($data["keywords"]) && isset($data["keywords"]["words"])): ?>
                    <?php foreach ($data["keywords"]["words"] as $title => $tWord): ?>
                        <?php if(!isset($tWord["text"])) continue; ?>
                        <label>
                            <ul>
                                <li>
                                    <div class="word_term">
                                        <span style="font-weight: bold;"><?php echo ucfirst($title) ?> </span>
                                        (<?php echo strtolower(__("verses").": ".join(", ", $tWord["range"])); ?>)
                                    </div>
                                    <div class="word_def"><?php echo  preg_replace('#<a.*?>(.*?)</a>#i', '<b>\1</b>', $tWord["text"]); ?></div>
                                </li>
                            </ul>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="word_def_popup">
                <div class="word_def-close glyphicon glyphicon-remove"></div>

                <div class="word_def_title"></div>
                <div class="word_def_content"></div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    isChecker = true;
    isSun = true;
</script>