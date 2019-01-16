<?php

if(isset($data["error"])) return;
?>
<div id="translator_contents" class="row panel-body">
    <div class="row main_content_header">
        <div class="main_content_title"><?php echo __("step_num", [3]). ": " . __("rearrange")?></div>
    </div>

    <div class="row">
        <div class="main_content col-sm-9">
            <form action="" method="post" id="main_form">
                <div class="main_content_text row" style="padding-left: 15px">
                    <h4 dir="<?php echo $data["event"][0]->sLangDir ?>"><?php echo $data["event"][0]->tLang." - "
                            .__($data["event"][0]->bookProject)." - "
                            .($data["event"][0]->abbrID <= 39 ? __("old_test") : __("new_test"))." - "
                            ."<span class='book_name'>".$data["event"][0]->name." ".$data["currentChapter"].":".$data["chunk"][0]."-".$data["chunk"][sizeof($data["chunk"])-1]."</span>"?>
                    </h4>

                    <div class="col-sm-12 no_padding">
                        <div class="row chunk_block words_block">
                            <div class="chunk_verses col-sm-6" dir="<?php echo $data["event"][0]->sLangDir ?>">
                                <?php foreach($data["text"] as $verse => $text): ?>
                                    <p><?php echo "<strong><sup>".$verse."</sup></strong> ".$text; ?></p>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-sm-6 editor_area" dir="<?php echo $data["event"][0]->tLangDir ?>">
                                <?php $text = isset($data["words"]) ? $data["words"] : ""; ?>
                                <textarea name="draft" class="col-sm-6 verse_ta textarea"><?php
                                    echo isset($_POST["draft"]) ? $_POST["draft"] : $text
                                ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main_content_footer row">
                    <div class="form-group">
                        <div class="main_content_confirm_desc"><?php echo __("confirm_finished")?></div>
                        <label><input name="confirm_step" id="confirm_step" type="checkbox" value="1" /> <?php echo __("confirm_yes")?></label>
                    </div>

                    <button id="next_step" type="submit" name="submit" class="btn btn-primary" disabled><?php echo __("next_step")?></button>
                    <img src="<?php echo template_url("img/saving.gif") ?>" class="unsaved_alert" style="float:none">
                </div>
            </form>
            <div class="step_right alt"><?php echo __("step_num", [3])?></div>
        </div>

        <div class="content_help col-sm-3">
            <div class="help_float">
                <div class="help_info_steps">
                    <div class="help_hide toggle-help glyphicon glyphicon-eye-close" title="<?php echo __("hide_help") ?>"></div>
                    <div class="help_title_steps"><?php echo __("help") ?></div>

                    <div class="clear"></div>

                    <div class="help_name_steps"><span><?php echo __("step_num", [3])?>:</span> <?php echo __("rearrange")?></div>
                    <div class="help_descr_steps">
                        <ul><?php echo __("rearrange_desc")?></ul>
                        <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
                    </div>
                </div>

                <div class="event_info">
                    <div class="participant_info">
                        <div class="additional_info">
                            <a href="/events/information-sun/<?php echo $data["event"][0]->eventID ?>"><?php echo __("event_info") ?></a>
                        </div>
                    </div>
                </div>

                <div class="tr_tools">
                    <button class="btn btn-warning show_saildict"><?php echo __("show_dictionary") ?></button>
                    <button class="btn btn-primary ttools" data-tool="tn"><?php echo __("show_notes") ?></button>
                    <button class="btn btn-primary ttools" data-tool="tw"><?php echo __("show_keywords") ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="help_show toggle-help glyphicon glyphicon-question-sign" title="<?php echo __("show_help") ?>"></div>
</div>


<div class="tutorial_container">
    <div class="tutorial_popup">
        <div class="tutorial-close glyphicon glyphicon-remove"></div>
        <div class="tutorial_pic">
            <img src="<?php echo template_url("img/steps/icons/rearrange.png") ?>" width="100" height="100">
            <img src="<?php echo template_url("img/steps/big/rearrange.png") ?>" width="280" height="280">
            <div class="hide_tutorial">
                <label><input id="hide_tutorial" data="<?php echo $data["event"][0]->step ?>" type="checkbox" value="0" /> <?php echo __("do_not_show_tutorial")?></label>
            </div>
        </div>

        <div class="tutorial_content">
            <h3><?php echo __("rearrange")?></h3>
            <ul><?php echo __("rearrange_desc")?></ul>
        </div>
    </div>
</div>


<div class="saildict_panel panel panel-default" draggable="true">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __("sail_dictionary") ?></h1>
        <span class="panel-close glyphicon glyphicon-remove"></span>
    </div>

    <div class="sun_content saildict page-content panel-body">
        <div class="sail_filter">
            <div class="form-group">
                <label for="sailfilter" class="sr-only">Filter</label>
                <input type="text" class="form-control input-lg" id="sailfilter" placeholder="<?php echo __("filter_by_word") ?>" value="">
            </div>
        </div>
        <div class="sail_list">
            <ul>
            <?php foreach ($data["saildict"] as $word): ?>
                <li id="<?php echo $word->word ?>" title="<?php echo __("copy_symbol_tip") ?>">
                    <div class="sail_word"><?php echo $word->word ?></div>
                    <div class="sail_symbol"><?php echo $word->symbol ?></div>
                    <input type="text" value="<?php echo $word->symbol ?>" />
                    <div class="clear"></div>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="copied_tooltip"><?php echo __("copied_tip") ?></div>
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
