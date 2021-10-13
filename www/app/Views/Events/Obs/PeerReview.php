<?php
use Helpers\Constants\EventMembers;
use Helpers\Constants\EventSteps;

if(isset($data["error"])) return;

?>
<div class="comment_div panel panel-default font_sun"
     dir="<?php echo $data["event"][0]->tLangDir ?>">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __("write_note_title")?></h1>
        <span class="editor-close btn btn-success" data-level="2"><?php echo __("save") ?></span>
        <span class="xbtn glyphicon glyphicon-remove"></span>
    </div>
    <textarea dir="<?php echo $data["event"][0]->sLangDir ?>" style="overflow-x: hidden; word-wrap: break-word; overflow-y: visible;" class="textarea textarea_editor"></textarea>
    <div class="other_comments_list"></div>
    <img src="<?php echo template_url("img/loader.gif") ?>" class="commentEditorLoader">
</div>

<div id="translator_contents" class="row panel-body">
    <div class="row main_content_header">
        <div class="main_content_title">
            <?php echo __("step_num", ["step_number" => 2]) . ": " . __(EventSteps::PEER_REVIEW . "_obs")?>
            <div class="action_type type_checking"><?php echo __("type_checking1"); ?></div>
        </div>
    </div>

    <div class="" style="position: relative">
        <div class="main_content">
            <form action="" id="main_form" method="post">
            <div class="main_content_text">
            
                <?php if($data["event"][0]->checkerID == 0): ?>
                    <div class="alert alert-success check_request"><?php echo __("check_request_sent_success") ?></div>
                <?php endif; ?>

                <h4><?php echo $data["event"][0]->tLang." - "
                    ."<span class='book_name'>".$data["event"][0]->name." ".
                    $data["event"][0]->currentChapter."</span>"?></h4>

                <div class="col-sm-12 no_padding">
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
                        <div class="row flex_container chunk_block">
                            <div class="chunk_verses flex_left" dir="<?php echo $data["event"][0]->sLangDir ?>">
                                <div class="obs_chunk no_margin" data-chunk="<?php echo $key ?>">
                                    <div><?php echo $data["obs"]->get($key)->title ?></div>
                                    <!--<img src="<?php /*echo $data["obs"]->get($key)->img */?>" />-->
                                </div>
                            </div>
                            <div class="flex_middle editor_area font_<?php echo $data["event"][0]->targetLang ?>" dir="<?php echo $data["event"][0]->tLangDir ?>">
                                <?php $translation = $data["translation"][$key][EventMembers::TRANSLATOR]["verses"]; ?>
                                <div class="vnote" data-chunk="<?php echo $key ?>">
                                    <textarea name="chunks[<?php echo $key ?>][title]" class="col-sm-6 peer_verse_ta textarea"><?php echo $translation["title"] ?? "" ?></textarea>
                                    <input name="chunks[<?php echo $key ?>][img]" type="hidden" value="<?php echo $translation["img"] ?? "" ?>" />
                                </div>
                            </div>
                            <div class="flex_right">
                                <?php
                                $hasComments = array_key_exists($data["event"][0]->currentChapter, $data["comments"]) && array_key_exists($key, $data["comments"][$data["event"][0]->currentChapter]);
                                ?>
                                <div class="comments_number tncomm flex_commn_number <?php echo $hasComments ? "hasComment" : "" ?>">
                                    <?php echo $hasComments ? sizeof($data["comments"][$data["event"][0]->currentChapter][$key]) : ""?>
                                </div>
                                <span class="editComment mdi mdi-lead-pencil"
                                      data="<?php echo $data["event"][0]->currentChapter.":".$key ?>"
                                      title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                    <?php if(array_key_exists($data["event"][0]->currentChapter, $data["comments"]) && array_key_exists($key, $data["comments"][$data["event"][0]->currentChapter])): ?>
                                        <?php foreach($data["comments"][$data["event"][0]->currentChapter][$key] as $comment): ?>
                                            <?php if($comment->memberID == $data["event"][0]->myChkMemberID && $comment->level == 2): ?>
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
                        <div class="chunk_divider"></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="main_content_footer row">
                <div class="form-group">
                    <div class="main_content_confirm_desc"><?php echo __("confirm_finished")?></div>
                    <label><input name="confirm_step" id="confirm_step" type="checkbox" value="1" /> <?php echo __("confirm_yes")?></label>
                </div>

                <input type="hidden" name="chk" value="1">
                <input type="hidden" name="level" value="obsContinue">
                <input type="hidden" name="chapter" value="<?php echo $data["event"][0]->currentChapter ?>">
                <input type="hidden" name="memberID" value="<?php echo $data["event"][0]->memberID ?>">

                <button id="next_step" type="submit" name="submit" class="btn btn-primary" disabled>
                    <?php echo __($data["next_step"])?>
                </button>
                <img src="<?php echo template_url("img/saving.gif") ?>" class="unsaved_alert" style="float:none">
            </div>
            </form>
            <div class="step_right alt"><?php echo __("step_num", ["step_number" => 2])?></div>
        </div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps is_checker_page_help">
            <div class="help_name_steps">
                <span><?php echo __("step_num", ["step_number" => 2])?>: </span>
                <?php echo __(EventSteps::PEER_REVIEW . "_obs")?>
            </div>
            <div class="help_descr_steps">
                <ul><?php echo __("peer-review_obs_desc", ["step" => __($data["next_step"])])?></ul>
                <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
            </div>
        </div>

        <div class="event_info is_checker_page_help">
            <div class="participant_name">
                <span><?php echo __("your_checker") ?>:</span>
                <span class="checker_name_span"><?php echo $data["event"][0]->checkerFName !== null ? $data["event"][0]->checkerFName . " " . mb_substr($data["event"][0]->checkerLName, 0, 1)."." : __("not_available") ?></span>
            </div>
            <div class="participant_info">
                <div class="additional_info">
                    <a href="/events/information-obs/<?php echo $data["event"][0]->eventID ?>"><?php echo __("event_info") ?></a>
                </div>
            </div>
        </div>

        <div class="tr_tools">
            <?php if (str_contains($data["event"][0]->targetLang, "sgn")): ?>
                <button class="btn btn-warning ttools" data-tool="saildict"><?php echo __("show_dictionary") ?></button>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="tutorial_container">
    <div class="tutorial_popup">
        <div class="tutorial-close glyphicon glyphicon-remove"></div>
        <div class="tutorial_pic">
            <img src="<?php echo template_url("img/steps/icons/peer-review.png") ?>" width="100px" height="100px">
            <img src="<?php echo template_url("img/steps/big/peer-review.png") ?>" width="280px" height="280px">
        </div>

        <div class="tutorial_content<?php echo $data["isCheckerPage"] ? " is_checker_page_help" : "" ?>">
            <h3><?php echo __(EventSteps::PEER_REVIEW . "_obs")?></h3>
            <ul><?php echo __("peer-review_obs_desc", ["step" => __($data["next_step"])])?></ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        equal_verses_height();

        $(".peer_verse_ta").blur(function() {
            equal_verses_height();
        });

        function equal_verses_height() {
            $(".obs_chunk").each(function() {
                var chunk = $(this).data("chunk");
                var p_height = $(this).outerHeight();
                var ta = $(".vnote[data-chunk="+chunk+"] textarea");

                if(ta.length > 0) {
                    var t_height = ta.outerHeight();
                    ta.outerHeight(Math.max(p_height, t_height));
                    $(this).outerHeight(Math.max(p_height, t_height));
                }
            });
        }

        $(".sun_mode input").change(function () {
            var active = $(this).prop('checked');

            if (active) {
                $(".flex_middle").removeClass("font_backsun");
                $(".flex_middle").addClass("font_sgn-US-symbunot");
            } else {
                $(".flex_middle").removeClass("font_sgn-US-symbunot");
                $(".flex_middle").addClass("font_backsun");
            }

            $(".obs_chunk").css("height", "initial");
            autosize.update($(".vnote textarea"));
            equal_verses_height();
        });
    });
</script>