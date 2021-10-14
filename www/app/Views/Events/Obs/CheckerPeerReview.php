<?php
/**
 * Created by PhpStorm.
 * User: Maxim
 * Date: 12 Apr 2016
 * Time: 17:30
 */
use Helpers\Constants\EventMembers;
use Helpers\Constants\EventSteps;
use Helpers\Session;

if(empty($error) && empty($data["success"])):
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
            <div><?php echo __("step_num", ["step_number" => 2]). ": " . __(EventSteps::PEER_REVIEW . "_obs")?></div>
            <div class="action_type type_checking <?php echo isset($data["isPeerPage"]) ? "isPeer" : "" ?>">
                <?php echo __("type_checking2"); ?>
            </div>
        </div>
    </div>

    <div class="">
        <div class="main_content">
            <div class="main_content_text">
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

                    <?php foreach($data["chunks"] as $chunkNo => $chunk): ?>
                    <div class="row chunk_block chunk_block_divider">
                        <div class="flex_container">
                            <div class="chunk_verses flex_left" dir="<?php echo $data["event"][0]->resLangDir ?>">
                                <div class="obs_chunk no_margin" data-chunk="<?php echo $chunkNo ?>">
                                    <div class="obs_title"><?php echo $data["obs"]->get($chunkNo)->title ?></div>
                                    <?php if ($data["obs"]->get($chunkNo)->img): ?>
                                        <div class="obs_img mdi mdi-image" data-img="<?php echo $data["obs"]->get($chunkNo)->img ?>"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex_middle font_<?php echo $data["event"][0]->targetLang ?>"
                                 dir="<?php echo $data["event"][0]->tLangDir ?>">
                                <?php
                                $translator = $data["translation"][$chunkNo][EventMembers::TRANSLATOR]["verses"];
                                ?>
                                <div class="chunk_translator" data-chunk="<?php echo $chunkNo ?>"><?php echo $translator["title"] ?></div>
                                <div class="chunk_checker" data-chunk="<?php echo $chunkNo ?>">
                                    <?php
                                    $checker = $data["translation"][$chunkNo][EventMembers::CHECKER]["verses"];
                                    echo $checker["title"];
                                    ?>
                                </div>
                            </div>
                            <div class="flex_right">
                                <?php $hasComments = array_key_exists($data["event"][0]->currentChapter, $data["comments"]) && array_key_exists($chunkNo, $data["comments"][$data["event"][0]->currentChapter]); ?>
                                <div class="comments_number tncommpeer flex_commn_number <?php echo $hasComments ? "hasComment" : "" ?>">
                                    <?php echo $hasComments ? sizeof($data["comments"][$data["event"][0]->currentChapter][$chunkNo]) : ""?>
                                </div>
                                <span class="editComment mdi mdi-lead-pencil"
                                      data="<?php echo $data["event"][0]->currentChapter.":".$chunkNo ?>"
                                      title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                    <?php if(array_key_exists($data["event"][0]->currentChapter, $data["comments"]) && array_key_exists($chunkNo, $data["comments"][$data["event"][0]->currentChapter])): ?>
                                        <?php foreach($data["comments"][$data["event"][0]->currentChapter][$chunkNo] as $comment): ?>
                                            <?php if($comment->memberID == Session::get("memberID") && $comment->level == 2): ?>
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
                    <?php endforeach; ?>
                </div>
                <div class="clear"></div>
            </div>

            <div class="main_content_footer row">
                <form action="" method="post" id="checker_submit">
                    <div class="form-group">
                        <div class="main_content_confirm_desc"><?php echo __("confirm_finished")?></div>
                        <label><input name="confirm_step" id="confirm_step" type="checkbox" value="1" /> <?php echo __("confirm_yes")?></label>
                    </div>

                    <button id="next_step" type="submit" name="submit" class="btn btn-primary" disabled>
                        <?php echo __($data["next_step"])?>
                    </button>
                </form>
                <div class="step_right chk"><?php echo __("step_num", ["step_number" => 2])?></div>
            </div>
        </div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps is_checker_page_help isPeer">
            <div class="help_name_steps"><span><?php echo __("step_num", ["step_number" => 2])?>: </span> <?php echo __(EventSteps::PEER_REVIEW . "_obs")?></div>
            <div class="help_descr_steps">
                <ul><?php echo __("peer-review_obs_chk_desc", ["step" => __($data["next_step"])])?></ul>
                <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
            </div>
        </div>

        <div class="event_info is_checker_page_help isPeer">
            <div class="participant_info">
                <div class="participant_name">
                    <span><?php echo __("your_partner") ?>:</span>
                    <span><?php echo $data["event"][0]->checkerFName . " " . mb_substr($data["event"][0]->checkerLName, 0, 1)."." ?></span>
                </div>
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
            <img src="<?php echo template_url("img/steps/icons/peer-review.png") ?>" width="100" height="100">
            <img src="<?php echo template_url("img/steps/big/peer-review.png") ?>" width="280" height="280">
        </div>

        <div class="tutorial_content<?php echo $data["isCheckerPage"] ? " is_checker_page_help" .
            (isset($data["isPeerPage"]) ? " isPeer" : ""): "" ?>">
            <h3><?php echo __(EventSteps::PEER_REVIEW . "_obs")?></h3>
            <ul><?php echo __("peer-review_obs_chk_desc", ["step" => __($data["next_step"])])?></ul>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo template_url("js/diff_match_patch.js?2")?>"></script>
<script type="text/javascript" src="<?php echo template_url("js/diff.js?7")?>"></script>
<script>
    var isChecker = true;

    $(document).ready(function() {
        $("#next_step").click(function (e) {
            renderConfirmPopup(Language.checkerConfirmTitle, Language.checkerConfirm,
                function () {
                    $("#checker_submit").submit();
                },
                function () {
                    $("#confirm_step").prop("checked", false);
                    $("#next_step").prop("disabled", true);
                    $( this ).dialog("close");
                },
                function () {
                    $("#confirm_step").prop("checked", false);
                    $("#next_step").prop("disabled", true);
                    $( this ).dialog("close");
                });

            e.preventDefault();
        });

        equal_verses_height();

        function equal_verses_height() {
            $(".obs_chunk").each(function() {
                var chunk = $(this).data("chunk");
                var p_height = $(this).outerHeight();
                var ta = $(".chunk_translator[data-chunk="+chunk+"]");

                if(ta.length > 0) {
                    var t_height = ta.outerHeight();
                    ta.outerHeight(Math.max(p_height, t_height));
                    $(this).outerHeight(Math.max(p_height, t_height));
                }
            });
        }

        $(".chunk_translator").each(function() {
            var chunk = $(this).data("chunk");
            var chkVersion = $(".chunk_checker[data-chunk='"+chunk+"']");
            diff_plain($(this).text().trim(), unEscapeStr(chkVersion.text().trim()), $(this));
        });

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
            $(".chunk_translator").css("height", "initial");
            equal_verses_height();
        });
    });

    
</script>
<?php endif; ?>