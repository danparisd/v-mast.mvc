<?php
if(isset($data["error"])) return;

use Helpers\Constants\EventCheckSteps;
use Helpers\Constants\EventMembers;
?>
<div class="comment_div panel panel-default font_sun">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __("write_note_title")?></h1>
        <span class="editor-close btn btn-success" data-level="2"><?php echo __("save") ?></span>
        <span class="xbtn glyphicon glyphicon-remove"></span>
    </div>
    <textarea style="overflow-x: hidden; word-wrap: break-word; overflow-y: visible;" class="textarea textarea_editor"></textarea>
    <div class="other_comments_list"></div>
    <img src="<?php echo template_url("img/loader.gif") ?>" class="commentEditorLoader">
</div>

<div class="footnote_editor panel panel-default">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __("write_footnote_title")?></h1>
        <span class="footnote-editor-close btn btn-success"><?php echo __("save") ?></span>
        <span class="xbtnf glyphicon glyphicon-remove"></span>
    </div>
    <div class="footnote_window">
        <div class="fn_preview"></div>
        <div class="fn_buttons" dir="<?php echo $data["event"][0]->sLangDir ?>">
            <button class="btn btn-default" data-fn="ft" title="footnote text">ft</button>
            <button class="btn btn-default" data-fn="fqa" title="footnote alternate translation">fqa</button>
        </div>
        <div class="fn_builder"></div>
    </div>
</div>

<div id="translator_contents" class="row panel-body">
    <div class="row main_content_header">
        <div class="main_content_title">
            <?php echo __("step_num", ["step_number" => 5]) . ": " . __(EventCheckSteps::CONTENT_REVIEW)?>
        </div>
    </div>

    <div class="" style="position: relative">
        <div class="main_content">
            <?php if($data["event"][0]->checkerID == 0): ?>
                <div class="alert alert-success check_request"><?php echo __("check_request_sent_success") ?></div>
            <?php endif; ?>
            <form action="" id="main_form" method="post">
                <div class="main_content_text" dir="<?php echo $data["event"][0]->sLangDir ?>">
                    <h4><?php echo $data["event"][0]->tLang." - "
                            .__($data["event"][0]->bookProject)." - "
                        .($data["event"][0]->sort <= 39 ? __("old_test") : __("new_test"))." - "
                        ."<span class='book_name'>".$data["event"][0]->name." ".$data["currentChapter"].":1-".$data["totalVerses"]."</span>"?></h4>

                    <div class="no_padding">
                        <?php if (str_contains($data["event"][0]->targetLang, "sgn")): ?>
                            <div class="sun_mode">
                                <label>
                                    <input type="checkbox" autocomplete="off" checked
                                           data-toggle="toggle"
                                           data-width="100"
                                           data-on="SUN"
                                           data-off="BACKSUN" />
                                </label>
                            </div>
                        <?php endif; ?>

                        <?php foreach($data["chunks"] as $key => $chunk) : ?>
                            <div class="row chunk_block no_autosize">
                                <div class="flex_container">
                                    <div class="chunk_verses flex_left" dir="<?php echo $data["event"][0]->sLangDir ?>">
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
                                            <p class="verse_text <?php echo "kwverse_".$data["currentChapter"]."_".$key."_".$verse ?>"
                                               data-verse="<?php echo $verse ?>">
                                                <strong class="<?php echo $data["event"][0]->sLangDir ?>">
                                                    <sup><?php echo $verse; ?></sup>
                                                </strong>
                                                <?php echo $data["text"][$verse]; ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="flex_middle editor_area font_<?php echo $data["event"][0]->targetLang ?>" dir="<?php echo $data["event"][0]->tLangDir ?>">
                                        <?php
                                        if(!empty($data["translation"][$key][EventMembers::L2_CHECKER]["verses"]))
                                            $verses = $data["translation"][$key][EventMembers::L2_CHECKER]["verses"];
                                        else
                                            $verses = $data["translation"][$key][EventMembers::TRANSLATOR]["verses"];
                                        ?>
                                        <div class="vnote">
                                            <?php foreach($verses as $verse => $text): ?>
                                                <div class="verse_block flex_chunk" data-verse="<?php echo $verse ?>">
                                                    <textarea name="chunks[<?php echo $key ?>][<?php echo $verse ?>]"
                                                              class="peer_verse_ta textarea" style="min-width: 400px"><?php echo $text; ?></textarea>

                                                    <span class="editFootNote mdi mdi-bookmark"
                                                          style="margin-top: -5px"
                                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="flex_right">
                                        <div class="notes_tools">
                                            <?php $hasComments = array_key_exists($data["currentChapter"], $data["comments"]) && array_key_exists($key, $data["comments"][$data["currentChapter"]]); ?>
                                            <div class="comments_number flex_commn_number <?php echo $hasComments ? "hasComment" : "" ?>">
                                                <?php echo $hasComments ? sizeof($data["comments"][$data["currentChapter"]][$key]) : ""?>
                                            </div>
                                            <span class="editComment mdi mdi-lead-pencil"
                                                  data="<?php echo $data["currentChapter"].":".$key ?>"
                                                  title="<?php echo __("write_note_title", [""])?>"></span>

                                            <div class="comments">
                                                <?php if(array_key_exists($data["currentChapter"], $data["comments"]) && array_key_exists($key, $data["comments"][$data["currentChapter"]])): ?>
                                                    <?php foreach($data["comments"][$data["currentChapter"]][$key] as $comment): ?>
                                                        <?php if($comment->memberID == $data["event"][0]->memberID
                                                            && $comment->level == 2): ?>
                                                            <div class="my_comment"><?php echo $comment->text; ?></div>
                                                        <?php else: ?>
                                                            <div class="other_comments">
                                                                <?php echo
                                                                    "<span>".$comment->firstName." ".mb_substr($comment->lastName, 0, 1).". 
                                                                    - L".$comment->level.":</span> 
                                                                ".$comment->text; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="chunk_divider"></div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="main_content_footer">
                    <div class="form-group">
                        <div class="main_content_confirm_desc"><?php echo __("confirm_finished")?></div>
                        <label><input name="confirm_step" id="confirm_step" type="checkbox" value="1" /> <?php echo __("confirm_yes")?></label>
                    </div>

                    <input type="hidden" name="level" value="l2">
                    <input type="hidden" name="checkingChapter" value="<?php echo $data["event"][0]->currentChapter ?>" />
                    <input type="hidden" name="isChecking" value="1" />
                    <button id="next_step" type="submit" name="submit_chk" class="btn btn-primary" disabled>
                        <?php echo __($data["next_step"])?>
                    </button>
                    <img src="<?php echo template_url("img/saving.gif") ?>" class="unsaved_alert">
                </div>
            </form>
            <div class="step_right alt"><?php echo __("step_num", ["step_number" => 5])?></div>
        </div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps is_checker_page_help">
            <div class="help_name_steps">
                <?php echo __("step_num", ["step_number" => 5])?>: <span><?php echo __(EventCheckSteps::CONTENT_REVIEW)?></span>
            </div>
            <div class="help_descr_steps">
                <ul>
                    <?php echo __("content-review-l2_desc", ["step" => __($data["next_step"])])?>
                </ul>
                <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
            </div>
        </div>

        <div class="event_info is_checker_page_help">
            <div class="participant_info">
                <div class="participant_name">
                    <span><?php echo __("your_checker") ?>:</span>
                    <span class="checker_name_span">
                            <?php echo $data["event"][0]->checkerFName !== null
                                ? $data["event"][0]->checkerFName . " "
                                . mb_substr($data["event"][0]->checkerLName, 0, 1)."."
                                : __("not_available") ?>
                        </span>
                </div>
                <div class="additional_info">
                    <a href="/events/information-revision/<?php echo $data["event"][0]->eventID ?>"><?php echo __("event_info") ?></a>
                </div>
            </div>
        </div>

        <div class="tr_tools">
            <button class="btn btn-primary ttools" data-tool="tn"><?php echo __("show_notes") ?></button>
            <button class="btn btn-primary ttools" data-tool="tq"><?php echo __("show_questions") ?></button>
            <button class="btn btn-primary ttools" data-tool="tw"><?php echo __("show_keywords") ?></button>
            <?php if (str_contains($data["event"][0]->targetLang, "sgn")): ?>
                <button class="btn btn-warning ttools" data-tool="saildict"><?php echo __("show_dictionary") ?></button>
            <?php else: ?>
                <button class="btn btn-warning ttools" data-tool="rubric"><?php echo __("show_rubric") ?></button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Data for tools -->
<input type="hidden" id="bookCode" value="<?php echo $data["event"][0]->bookCode ?>">
<input type="hidden" id="chapter" value="<?php echo $data["event"][0]->currentChapter ?>">
<input type="hidden" id="tn_lang" value="<?php echo $data["event"][0]->tnLangID ?>">
<input type="hidden" id="tq_lang" value="<?php echo $data["event"][0]->tqLangID ?>">
<input type="hidden" id="tw_lang" value="<?php echo $data["event"][0]->twLangID ?>">
<input type="hidden" id="totalVerses" value="<?php echo $data["totalVerses"] ?>">
<input type="hidden" id="targetLang" value="<?php echo $data["event"][0]->targetLang ?>">

<div class="tutorial_container">
    <div class="tutorial_popup">
        <div class="tutorial-close glyphicon glyphicon-remove"></div>
        <div class="tutorial_pic">
            <img src="<?php echo template_url("img/steps/icons/content-review.png") ?>" width="100px" height="100px">
            <img src="<?php echo template_url("img/steps/big/content-review.png") ?>" width="280px" height="280px">
        </div>

        <div class="tutorial_content">
            <h3><?php echo __(EventCheckSteps::CONTENT_REVIEW)?></h3>
            <ul>
                <?php echo __("content-review-l2_desc", ["step" => __($data["next_step"])])?>
            </ul>
        </div>
    </div>
</div>

<script>
    (function() {
        setTimeout(function() {
            equal_verses_height();
        }, 500);

        $(".peer_verse_ta").blur(function() {
            equal_verses_height();
        });

        function equal_verses_height() {
            $(".verse_text").each(function() {
                var verse = $(this).data("verse");
                var p_height = $(this).outerHeight();
                var ta = $(".verse_block[data-verse="+verse+"] textarea");

                if(ta.length > 0) {
                    var t_height = ta.outerHeight();
                    ta.outerHeight(Math.max(p_height, t_height));
                    $(this).outerHeight(Math.max(p_height, t_height));
                }
            });
        }

        $(".peer_verse_ta").highlightWithinTextarea({
            highlight: /\\f\s[+-]\s(.*?)\\f\*/gi
        });

        $(".sun_mode input").change(function () {
            var active = $(this).prop('checked');

            if (active) {
                $(".flex_left, .flex_middle").removeClass("font_backsun");
                $(".flex_left, .flex_middle").addClass("font_sgn-US-symbunot");
            } else {
                $(".flex_left, .flex_middle").removeClass("font_sgn-US-symbunot");
                $(".flex_left, .flex_middle").addClass("font_backsun");
            }

            $(".verse_text").css("height", "initial");
            setTimeout(function () {
                autosize.update($(".vnote textarea"));
            }, 500);
            equal_verses_height();
        });
    })();

    disableHighlight = true;
    isLevel2 = true;
</script>
