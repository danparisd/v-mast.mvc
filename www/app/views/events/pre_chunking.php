<?php
use \Core\Language;
?>

<div id="translator_contents" class="row panel-body">
    <div class="row">
        <div class="main_content_title"><?php echo Language::show("pre-chunking", "Events")?></div>
    </div>

    <div class="row">
        <div class="main_content col-sm-9">
            <div class="main_content_text">
                <h4><?php echo $data["event"][0]->sLang." - "
                        .Language::show($data["event"][0]->bookProject, "Events")." - "
                        .($data["event"][0]->abbrID <= 39 ? Language::show("old_test", "Events") : Language::show("new_test", "Events"))." - "
                        .$data["event"][0]->name." ".$data["currentChapter"].":1-".$data["totalVerses"]?></h4>

                <?php for($i=2; $i <= sizeof($data["text"]); $i+=2): ?>
                    <p class="verse_p">
                        <label class="verse_number_label">
                            <input type="checkbox" name="verse" class="verse_number" value="<?php echo $data["text"][$i-1]; ?>">
                            <?php echo "<strong><sup>".$data["text"][$i-1]."</sup></strong> ".$data["text"][$i]; ?>
                        </label>
                    </p>
                <?php endfor; ?>
                <div class="chunks_reset">Reset chunks</div>
            </div>

            <div class="main_content_footer row">
                <form action="" method="post">
                    <div class="form-group">
                        <div class="main_content_confirm_desc"><?php echo Language::show("confirm_finished", "Events")?></div>
                        <label><input name="confirm_step" id="confirm_step" type="checkbox" value="1" /> <?php echo Language::show("confirm_yes", "Events")?></label>
                        <input type="hidden" name="chunks_array" id="chunks_array" value="[]">
                    </div>

                    <button id="next_step" type="submit" name="submit" class="btn btn-primary" disabled><?php echo Language::show("next_step", "Events")?></button>
                </form>
            </div>
        </div>

        <div class="content_help col-sm-3">
            <div class="help_info_steps">
                <div class="help_title_steps">HELP</div>

                <div class="clear"></div>

                <div class="help_name_steps"><span>Step 4:</span> <?php echo Language::show("pre-chunking", "Events")?></div>
                <div class="help_descr_steps"><?php echo Language::show("pre-chunking_desc", "Events")?></div>
            </div>
        </div>
    </div>
</div>

<span class="create_chunk">Make chunk</span>