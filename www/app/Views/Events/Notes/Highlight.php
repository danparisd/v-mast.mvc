<?php
if(isset($data["error"])) return;
?>
<div id="translator_contents" class="row panel-body">
    <div class="row main_content_header">
        <div class="main_content_title"><?php echo __("step_num", ["step_number" => 2]) . ": " . __("highlight_tn_full")?></div>
    </div>

    <div class="">
        <div class="main_content">
            <div class="main_content_text" dir="<?php echo $data["event"][0]->sLangDir ?>">
            <h4><?php echo $data["event"][0]->tLang." - "
                        .__($data["event"][0]->bookProject)." - "
                    .($data["event"][0]->abbrID <= 39 ? __("old_test") : __("new_test"))." - "
                    ."<span class='book_name'>".$data["event"][0]->name." ".
                    (!$data["nosource"] 
                        ? $data["currentChapter"].":1-".$data["totalVerses"]
                        : "(".__("front").")")."</span>"?></h4>

                    <?php foreach($data["chunks"] as $chunkNo => $chunk): $fv = $chunk[0]; ?>
                        <?php if($fv == 0) continue; ?>
                        <?php foreach(array_values($chunk) as $verse): ?>
                        <div class="chunk_verses" style="padding: 5px 0">
                            <strong><sup><?php echo $verse; ?></sup></strong>
                            <div class="<?php echo "kwverse_".$data["currentChapter"]."_".$chunkNo."_".$verse ?>">
                                <?php echo isset($data["text"][$verse]) ? $data["text"][$verse] : ""; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
            </div>

            <?php //if(empty($error)):?>
            <div class="main_content_footer row">
                <form action="" method="post">
                    <div class="form-group">
                        <div class="main_content_confirm_desc"><?php echo __("confirm_finished")?></div>
                        <label><input name="confirm_step" id="confirm_step" type="checkbox" value="1" /> <?php echo __("confirm_yes")?></label>
                    </div>

                    <button id="next_step" type="submit" name="submit" class="btn btn-primary" disabled><?php echo __("next_step")?></button>
                    <img src="<?php echo template_url("img/saving.gif") ?>" class="unsaved_alert">
                </form>
                <div class="step_right"><?php echo __("step_num", ["step_number" => 2])?></div>
            </div>
            <?php //endif; ?>
        </div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps is_checker_page_help">
            <div class="help_name_steps"><span><?php echo __("step_num", ["step_number" => 2])?>: </span><?php echo __("highlight_tn")?></div>
            <div class="help_descr_steps">
                <ul><?php echo __("highlight_tn_desc")?></ul>
                <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
            </div>
        </div>

        <div class="event_info is_checker_page_help">
            <div class="participant_info">
                <div class="additional_info">
                    <a href="/events/information-tn/<?php echo $data["event"][0]->eventID ?>"><?php echo __("event_info") ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tutorial_container">
    <div class="tutorial_popup">
        <div class="tutorial-close glyphicon glyphicon-remove"></div>
        <div class="tutorial_pic">
            <img src="<?php echo template_url("img/steps/icons/consume.png") ?>" width="100px" height="100px">
            <img src="<?php echo template_url("img/steps/big/consume.png") ?>" width="280px" height="280px">
            
        </div>

        <div class="tutorial_content is_checker_page_help">
            <h3><?php echo __("highlight_tn")?></h3>
            <ul><?php echo __("highlight_tn_desc")?></ul>
        </div>
    </div>
</div>