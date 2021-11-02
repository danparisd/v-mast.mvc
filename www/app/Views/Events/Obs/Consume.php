<?php

use Helpers\Constants\EventSteps;

if(isset($data["error"])) return;
?>
<div id="translator_contents" class="row panel-body">
    <div class="row main_content_header">
        <div class="main_content_title"><?php echo __("step_num", ["step_number" => 1]) . ": " . __(EventSteps::CONSUME)?></div>
    </div>

    <div class="">
        <div class="main_content">
            <div class="main_content_text" dir="<?php echo $data["event"][0]->sLangDir ?>">
                <h4><?php echo $data["event"][0]->tLang." - "
                        ."<span class='book_name'>".$data["event"][0]->name." ".
                        $data["event"][0]->currentChapter."</span>"?></h4>

                <div class="col-sm-12 no_padding">
                    <?php foreach($data["obs"] as $chunk): ?>
                        <div class="row flex_container chunk_block">
                            <div class="chunk_verses flex_left" dir="<?php echo $data["event"][0]->sLangDir ?>">
                                <div class="obs_chunk no_margin">
                                    <div class="obs_title"><?php echo $chunk->title ?></div>
                                    <?php if ($chunk->img): ?>
                                        <div class="obs_img mdi mdi-image" data-img="<?php echo $chunk->img ?>"></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="clear"></div>
            </div>

            <div class="main_content_footer row">
                <form action="" method="post">
                    <div class="form-group">
                        <div class="main_content_confirm_desc"><?php echo __("confirm_finished")?></div>
                        <label><input name="confirm_step" id="confirm_step" type="checkbox" value="1" /> <?php echo __("confirm_yes")?></label>
                    </div>

                    <button id="next_step" type="submit" name="submit" class="btn btn-primary" disabled>
                        <?php echo __($data["next_step"])?>
                    </button>
                </form>
                <div class="step_right alt"><?php echo __("step_num", ["step_number" => 1])?></div>
            </div>
        </div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps">
            <div class="help_name_steps"><span><?php echo __("step_num", ["step_number" => 1])?>: </span><?php echo __(EventSteps::CONSUME)?></div>
            <div class="help_descr_steps">
                <ul><?php echo __("consume_desc", ["step" => __($data["next_step"])])?></ul>
                <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
            </div>
        </div>

        <div class="event_info">
            <div class="participant_info">
                <div class="additional_info">
                    <a href="/events/information-obs/<?php echo $data["event"][0]->eventID ?>"><?php echo __("event_info") ?></a>
                </div>
            </div>
        </div>

        <div class="tr_tools">
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

        <div class="tutorial_content">
            <h3><?php echo __(EventSteps::CONSUME)?></h3>
            <ul><?php echo __("consume_desc", ["step" => __($data["next_step"])])?></ul>
        </div>
    </div>
</div>