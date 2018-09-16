<?php
use Helpers\Constants\EventSteps;
use Helpers\Constants\StepsStates;

$groupOrder=1;
foreach ($data["chapters"] as $key => $chapter):?>
    <?php
    if(empty($chapter)) {
        echo '<div class="chapter_item">
                <div class="chapter_number nofloat">'.__("group_id", array($groupOrder)).'</div>
            </div>';
        $groupOrder++;
        continue;
    }
    ?>
    <div class="chapter_item">
        <div class="chapter_accordion">
            <div class="section_header" data="<?php echo "sec_".$key?>">
                <div class="section_arrow glyphicon glyphicon-triangle-right"></div>
                <div class="chapter_number section_title">
                    <?php echo __("group_id", [$groupOrder]) .
                        " <span class='glyphicon glyphicon-info-sign' 
                                        data-toggle='tooltip' 
                                        title='".$chapter["words"][0] . "..." . $chapter["words"][sizeof($chapter["words"])-1]."'
                                        style='font-size: 16px;'></span>" ?>
                </div>
                <div class="section_translator_progress_bar">
                    <div class="progress <?php echo $chapter["progress"] <= 0 ? "zero" : ""?>">
                        <div class="progress-bar progress-bar-success" role="progressbar"
                             aria-valuenow="<?php echo floor($chapter["progress"]) ?>" aria-valuemin="0"
                             aria-valuemax="100" style="min-width: 0em; width: <?php echo floor($chapter["progress"])."%" ?>">
                            <?php echo floor($chapter["progress"])."%" ?>
                        </div>
                    </div>
                    <div class="<?php echo $chapter["progress"] >= 100 ? "glyphicon glyphicon-ok" : "" ?> finished_icon"></div>
                    <div class="clear"></div>
                </div>
                <div>
                    <?php if(isset($chapter["lastEdit"])): ?>
                        <span style="font-weight: bold;"><?php echo __("last_edit") .": " ?></span>
                        <span class="datetime" data="<?php echo isset($chapter["lastEdit"]) ? date(DATE_RFC2822, strtotime($chapter["lastEdit"])) : "" ?>">
                                    <?php echo $chapter["lastEdit"] ?>
                                </span>
                    <?php endif; ?>
                </div>
                <div class="clear"></div>
            </div>
            <div class="section_content">
                <div class="section_translator">
                    <div class="section_translator_name">
                        <img width="50" src="<?php echo template_url("img/avatars/".$data["members"][$chapter["memberID"]]["avatar"].".png") ?>">
                        <span><b><?php echo $data["members"][$chapter["memberID"]]["name"] ?></b></span>
                    </div>
                </div>
                <div class="section_steps">
                    <!-- Multi Draft Step -->
                    <div class="section_step <?php echo $chapter["multi"]["state"] ?>" style="width: 200px;">
                        <div class="step_status"><?php echo __("step_status_" . $chapter["multi"]["state"]) ?></div>
                        <div class="step_light"></div>
                        <div class="step_icon"><img width="40" src="<?php echo template_url("img/steps/icons/".EventSteps::CONTENT_REVIEW.".png") ?>"></div>
                        <div class="step_name">1. <?php echo __(EventSteps::MULTI_DRAFT); ?></div>
                        <div class="step_chunks <?php echo sizeof($chapter["chunks"]) > 4 ? "more_chunks" : "" ?>">
                            <?php if(isset($chapter["chunks"])): ?>
                                <?php foreach ($chapter["chunks"] as $index => $chunk):?>
                                    <div class="section_translator_chunk">
                                        <?php echo $chapter["words"][$index]; ?>
                                        <?php if(array_key_exists($index, (array)$chapter["chunksData"])) {
                                            echo __("chunk_finished");
                                        } ?>
                                    </div>
                                <?php endforeach; ?>
                                <?php if(sizeof($chapter["chunks"]) > 4): ?>
                                    <div class="chunks_blind glyphicon glyphicon-triangle-bottom"></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Self Edit Step -->
                    <div class="section_step <?php echo $chapter["selfEdit"]["state"] ?>">
                        <div class="step_status"><?php echo __("step_status_" . $chapter["selfEdit"]["state"]) ?></div>
                        <div class="step_light"></div>
                        <div class="step_icon"><img width="40" src="<?php echo template_url("img/steps/icons/".EventSteps::SELF_CHECK.".png") ?>"></div>
                        <div class="step_name">5. <?php echo __(EventSteps::SELF_CHECK); ?></div>
                    </div>
                    <!-- Keyword Check Step -->
                    <div class="section_step <?php echo $chapter["kwc"]["state"] ?>">
                        <div class="step_status">
                            <?php echo __("step_status_" . $chapter["kwc"]["state"]) ?>
                        </div>
                        <div class="step_light"></div>
                        <div class="step_icon"><img width="40" src="<?php echo template_url("img/steps/icons/".EventSteps::KEYWORD_CHECK.".png") ?>"></div>
                        <div class="step_name">7. <?php echo __(EventSteps::KEYWORD_CHECK); ?></div>
                        <?php if($chapter["kwc"]["checkerID"] != "na"): ?>
                            <div class="step_checker">
                                <img width="50" src="<?php echo template_url("img/avatars/".$data["members"][$chapter["kwc"]["checkerID"]]["avatar"].".png") ?>">
                                <div><?php echo $data["members"][$chapter["kwc"]["checkerID"]]["name"] ?></div>
                                <?php if($chapter["kwc"]["state"] == StepsStates::CHECKED || $chapter["kwc"]["state"] == StepsStates::FINISHED): ?>
                                    <span class="glyphicon glyphicon-ok checked"></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if($chapter["kwc"]["state"] == StepsStates::WAITING): ?>
                            <img class="img_waiting" src="<?php echo template_url("img/waiting.png") ?>">
                        <?php endif; ?>
                    </div>
                    <!-- Peer Check Step -->
                    <div class="section_step <?php echo $chapter["peer"]["state"] ?>">
                        <div class="step_status">
                            <?php echo __("step_status_" . $chapter["peer"]["state"]) ?>
                        </div>
                        <div class="step_light"></div>
                        <div class="step_icon"><img width="40" src="<?php echo template_url("img/steps/icons/".EventSteps::PEER_REVIEW.".png") ?>"></div>
                        <div class="step_name">6. <?php echo __(EventSteps::PEER_REVIEW); ?></div>
                        <?php if($chapter["peer"]["checkerID"] != "na"): ?>
                            <div class="step_checker">
                                <img width="50" src="<?php echo template_url("img/avatars/".$data["members"][$chapter["peer"]["checkerID"]]["avatar"].".png") ?>">
                                <div><?php echo $data["members"][$chapter["peer"]["checkerID"]]["name"] ?></div>
                                <?php if($chapter["peer"]["state"] == StepsStates::CHECKED || $chapter["peer"]["state"] == StepsStates::FINISHED): ?>
                                    <span class="glyphicon glyphicon-ok checked"></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if($chapter["peer"]["state"] == StepsStates::WAITING): ?>
                            <img class="img_waiting" src="<?php echo template_url("img/waiting.png") ?>">
                        <?php endif; ?>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
<?php $groupOrder++; endforeach; ?>