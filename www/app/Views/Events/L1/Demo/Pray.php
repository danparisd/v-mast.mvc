<div id="translator_contents" class="row panel-body">
    <div class="row main_content_header">
        <div class="main_content_title">
            <div class="demo_title"><?php echo __("demo")
                    . " (".($data["isLangInput"] ? __("lang_input") : __("8steps_vmast")).")" ?></div>
            <div><?php echo __("pray")?></div>
        </div>
    </div>

    <div class="main_content">
        <div class="main_content_text">
            <div class="pray_left">
                <img src="<?php echo template_url("img/steps/icons/pray.png") ?>" width="80">
                <br><br>
                <img src="<?php echo template_url("img/steps/big/pray.png") ?>" width="300">
            </div>
            <div class="pray_right">
                <?php echo __("pray_text")?>
            </div>
            <div class="clear"></div>
        </div>

        <div class="main_content_footer row">
            <form action="" method="post">
                <div class="form-group">
                    <div class="main_content_confirm_desc"><?php echo __("confirm_finished")?></div>
                    <label><input name="confirm_step" id="confirm_step" value="1" type="checkbox"> <?php echo __("confirm_yes")?></label>
                </div>

                <button id="next_step" class="btn btn-primary" disabled="disabled"><?php echo __($data["next_step"])?></button>
            </form>
        </div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps">
            <div class="help_name_steps"><span><?php echo __("pray")?></span></div>
            <div class="help_descr_steps">
                <ul><?php echo __("pray_desc", ["step" => __($data["next_step"])])?></ul>
            </div>
        </div>

        <div class="event_info">
            <div class="participant_info">
                <div class="additional_info">
                    <a href="/events/demo<?php echo $data["isLangInput"] ? "-scripture-input" : "" ?>/information"><?php echo __("event_info") ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        
        $("#next_step").click(function (e) {
            e.preventDefault();
            if(isLangInput)
            {
                window.location.href = '/events/demo-scripture-input/input';
            }
            else
            {
                window.location.href = '/events/demo/consume';
            }
            return false;
        });
    });
</script>