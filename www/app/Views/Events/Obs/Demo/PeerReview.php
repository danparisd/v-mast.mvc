<?php
use Helpers\Constants\EventSteps;
?>

<div class="comment_div panel panel-default">
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
        <div class="main_content_title">
            <div class="demo_title"><?php echo __("demo") . " (".__("obs").")" ?></div>
            <div><?php echo __("step_num", ["step_number" => 2]) . ": " . __(EventSteps::PEER_REVIEW . "_obs")?></div>
            <div class="action_type type_checking"><?php echo __("type_checking1"); ?></div>
        </div>
    </div>

    <div class="">
        <div class="main_content">
            <div class="main_content_text" dir="ltr">
                <h4>Русский - <span class='book_name'><?php echo __("obs") ?> 4</span></h4>

                <div class="col-sm-12 no_padding">
                    <div class="row flex_container chunk_block">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <div class="obs_chunk no_margin" data-chunk="0" style="height: 90px;">
                                <div>4. God’s Covenant with Abraham</div>
                            </div>
                        </div>
                        <div class="flex_middle editor_area font_ru" dir="ltr">
                            <div class="vnote" data-chunk="0">
                                <textarea name="chunks[0][title]" class="col-sm-6 peer_verse_ta textarea" style="overflow-x: hidden; overflow-wrap: break-word; min-height: 90px; height: 90px;">4. Божий завет с Авраамом</textarea>
                                <input name="chunks[0][img]" type="hidden" value="">
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number tncomm hasComment"> 2 </div>
                            <span class="editComment mdi mdi-lead-pencil"
                                  data="0:0"
                                  title="<?php echo __("write_note_title", [""])?>"></span>

                            <div class="comments">
                                <div class="my_comment">This is the note of the checker...</div>
                                <div class="other_comments">
                                    <span>Tanya C. - L1</span> This is the note of the translator...
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <div class="obs_chunk no_margin" data-chunk="1" style="height: 152px;">
                                <div class="obs_title">Many years after the flood, there were again many people in the world, and they still sinned against God and each other. Because they all spoke the same language, they gathered together and built a city instead of spreading out over the earth as God had commanded.</div>
                                <div class="obs_img mdi mdi-image" data-img="https://cdn.door43.org/obs/jpg/360px/obs-en-04-01.jpg"></div>
                            </div>
                        </div>
                        <div class="flex_middle editor_area font_ru" dir="ltr">
                            <div class="vnote" data-chunk="1">
                                <textarea name="chunks[1][title]" class="col-sm-6 peer_verse_ta textarea" style="overflow-x: hidden; overflow-wrap: break-word; min-height: 152px; height: 152px;">Много лет после наводнения, в мире снова было много людей, и они все еще согрешили против Бога и друг друга. Потому что все они говорили на одном языке, они собрались вместе и построили город вместо того, чтобы распространяться над землей, когда Бог повелел.</textarea>
                                <input name="chunks[1][img]" type="hidden" value="https://cdn.door43.org/obs/jpg/360px/obs-en-04-01.jpg">
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <span class="editComment mdi mdi-lead-pencil" data="4:1" title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <div class="obs_chunk no_margin" data-chunk="2" style="height: 152px;">
                                <div class="obs_title">They were very proud, and they did not want to obey God’s commands about how they should live. They even began building a tall tower that would reach heaven. God saw that, if they all kept working together to do evil, they could do many more sinful things.</div>
                                <div class="obs_img mdi mdi-image" data-img="https://cdn.door43.org/obs/jpg/360px/obs-en-04-02.jpg"></div>
                            </div>
                        </div>
                        <div class="flex_middle editor_area font_ru" dir="ltr">
                            <div class="vnote" data-chunk="2">
                                <textarea name="chunks[2][title]" class="col-sm-6 peer_verse_ta textarea" style="overflow-x: hidden; overflow-wrap: break-word; min-height: 152px; height: 152px;">Они были очень гордыми, и они не хотели подчиняться Божьим командам о том, как они должны жить. Они даже начали строить высокую башню, которая достигла небес. Бог увидел, что, если все они продолжали работать вместе, чтобы сделать зло, они могли бы сделать много более греховных вещей.</textarea>
                                <input name="chunks[2][img]" type="hidden" value="https://cdn.door43.org/obs/jpg/360px/obs-en-04-02.jpg">
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <span class="editComment mdi mdi-lead-pencil" data="4:2" title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <div class="obs_chunk no_margin" data-chunk="3" style="height: 121px;">
                                <div class="obs_title">So God changed their language into many different languages and spread the people out all over the world. The city they had begun to build was called Babel, which means “confused.”</div>
                                <div class="obs_img mdi mdi-image" data-img="https://cdn.door43.org/obs/jpg/360px/obs-en-04-03.jpg"></div>
                            </div>
                        </div>
                        <div class="flex_middle editor_area font_ru" dir="ltr">
                            <div class="vnote" data-chunk="3">
                                <textarea name="chunks[3][title]" class="col-sm-6 peer_verse_ta textarea" style="overflow-x: hidden; overflow-wrap: break-word; min-height: 121px; height: 121px;">Поэтому Бог изменил свой язык на разные языки и распространил людей по всему миру. Город, который они начали строить, назывался Бабелом, что означает «смущенный».</textarea>
                                <input name="chunks[3][img]" type="hidden" value="https://cdn.door43.org/obs/jpg/360px/obs-en-04-03.jpg">
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <span class="editComment mdi mdi-lead-pencil" data="4:3" title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <div class="obs_chunk no_margin" data-chunk="4" style="height: 204px;">
                                <div class="obs_title">Hundreds of years later, God spoke to a man named Abram. God told him, “Leave your country and family and go to the land I will show you. I will bless you and make you a great nation. I will make your name great. I will bless those who bless you and curse those who curse you. All families on earth will be blessed because of you.”</div>
                                <div class="obs_img mdi mdi-image" data-img="https://cdn.door43.org/obs/jpg/360px/obs-en-04-04.jpg"></div>
                            </div>
                        </div>
                        <div class="flex_middle editor_area font_ru" dir="ltr">
                            <div class="vnote" data-chunk="4">
                                <textarea name="chunks[4][title]" class="col-sm-6 peer_verse_ta textarea" style="overflow-x: hidden; overflow-wrap: break-word; min-height: 183px; height: 204px;">Сотни лет спустя Бог говорил с человеком по имени Абрам. Бог сказал ему: «Оставьте свою страну и семью и пойдем на землю, которую я покажу тебе. Я благословляю тебя и сделаю тебя великой нацией. Я сделаю ваше имя великолепно. Я буду благословлять тех, кто благословит вас и проклинает тех, кто проклинает тебя. Все семьи на земле будут благословлены из-за вас.</textarea>
                                <input name="chunks[4][img]" type="hidden" value="https://cdn.door43.org/obs/jpg/360px/obs-en-04-04.jpg">
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <span class="editComment mdi mdi-lead-pencil" data="4:4" title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <div class="obs_chunk no_margin" data-chunk="5" style="height: 121px;">
                                <div class="obs_title">So Abram obeyed God. He took his wife, Sarai, together with all of his servants and everything he owned and went to the land God showed him, the land of Canaan.</div>
                                <div class="obs_img mdi mdi-image" data-img="https://cdn.door43.org/obs/jpg/360px/obs-en-04-05.jpg"></div>
                            </div>
                        </div>
                        <div class="flex_middle editor_area font_ru" dir="ltr">
                            <div class="vnote" data-chunk="5">
                                <textarea name="chunks[5][title]" class="col-sm-6 peer_verse_ta textarea" style="overflow-x: hidden; overflow-wrap: break-word; min-height: 121px; height: 121px;">Так Абрам повиновался Богу. Он взял свою жену, Сарай вместе со всеми своими слугами и всему, которое он принадлежит и пошел на землю, Бог показал ему, земля Ханаан.</textarea>
                                <input name="chunks[5][img]" type="hidden" value="https://cdn.door43.org/obs/jpg/360px/obs-en-04-05.jpg">
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <span class="editComment mdi mdi-lead-pencil" data="4:5" title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <div class="obs_chunk no_margin" data-chunk="6" style="height: 121px;">
                                <div class="obs_title">When Abram arrived in Canaan, God said, “Look all around you. I will give to you all this land, and your descendants will always possess it.” Then Abram settled in the land.</div>
                                <div class="obs_img mdi mdi-image" data-img="https://cdn.door43.org/obs/jpg/360px/obs-en-04-06.jpg"></div>
                            </div>
                        </div>
                        <div class="flex_middle editor_area font_ru" dir="ltr">
                            <div class="vnote" data-chunk="6">
                                <textarea name="chunks[6][title]" class="col-sm-6 peer_verse_ta textarea" style="overflow-x: hidden; overflow-wrap: break-word; min-height: 121px; height: 121px;">Когда Аврам прибыл в Ханаан, Бог сказал: «Посмотри вокруг вас. Я дам вам всю эту землю, и ваши потомки всегда будут иметь его ». Тогда Абрам поселился на земле.</textarea>
                                <input name="chunks[6][img]" type="hidden" value="https://cdn.door43.org/obs/jpg/360px/obs-en-04-06.jpg">
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <span class="editComment mdi mdi-lead-pencil" data="4:6" title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <div class="obs_chunk no_margin" data-chunk="7" style="height: 183px;">
                                <div class="obs_title">There was a man named Melchizedek who was a priest of God Most High. One day, after Abram had been in a battle, he and Abram met. Melchizedek blessed Abram and said, “May God Most High who owns heaven and earth bless Abram.” Then Abram gave Melchizedek a tenth of everything he had won in the battle.</div>
                                <div class="obs_img mdi mdi-image" data-img="https://cdn.door43.org/obs/jpg/360px/obs-en-04-07.jpg"></div>
                            </div>
                        </div>
                        <div class="flex_middle editor_area font_ru" dir="ltr">
                            <div class="vnote" data-chunk="7">
                                <textarea name="chunks[7][title]" class="col-sm-6 peer_verse_ta textarea" style="overflow-x: hidden; overflow-wrap: break-word; min-height: 183px; height: 183px;">Был человек по имени Мелхисерек, который был священником Бога самым высоким. Однажды после того, как Аврам был в битве, он и Аврам встретились. Мельхигенек благословил Аврам и сказал: «Пусть Бог самый высокий, кто владеет небесами и благословением земли Аврам». Тогда Абрам дал Мелхиседек на десятую часть всего, что он выиграл в битве.</textarea>
                                <input name="chunks[7][img]" type="hidden" value="https://cdn.door43.org/obs/jpg/360px/obs-en-04-07.jpg">
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <span class="editComment mdi mdi-lead-pencil" data="4:7" title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <div class="obs_chunk no_margin" data-chunk="8" style="height: 152px;">
                                <div class="obs_title">Many years went by, but Abram and Sarai still did not have a son. God spoke to Abram and promised again that he would have a son and as many descendants as the stars in the sky. Abram believed God’s promise. God declared that Abram was righteous because he believed in God’s promise.</div>
                                <div class="obs_img mdi mdi-image" data-img="https://cdn.door43.org/obs/jpg/360px/obs-en-04-08.jpg"></div>
                            </div>
                        </div>
                        <div class="flex_middle editor_area font_ru" dir="ltr">
                            <div class="vnote" data-chunk="8">
                                <textarea name="chunks[8][title]" class="col-sm-6 peer_verse_ta textarea" style="overflow-x: hidden; overflow-wrap: break-word; min-height: 152px; height: 152px;">Много лет прошло, но Аврам и Сарай все еще не было сына. Бог говорил с Абрамом и снова обещал, что у него будет сын и столько потомков, как звезды в небе. Абрам считал Божьим обещанием. Бог заявил, что Аврам был праведным, потому что он верил в Божье обещание.</textarea>
                                <input name="chunks[8][img]" type="hidden" value="https://cdn.door43.org/obs/jpg/360px/obs-en-04-08.jpg">
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <span class="editComment mdi mdi-lead-pencil" data="4:8" title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <div class="obs_chunk no_margin" data-chunk="9" style="height: 183px;">
                                <div class="obs_title">Then God made a covenant with Abram. Normally, a covenant is an agreement between two parties to do things for each other. But in this case, God made a promise to Abram while Abram was in a deep sleep, but he could still hear God. God said, “I will give you a son from your own body. I give the land of Canaan to your descendants.” But Abram still did not have a son.</div>
                                <div class="obs_img mdi mdi-image" data-img="https://cdn.door43.org/obs/jpg/360px/obs-en-04-09.jpg"></div>
                            </div>
                        </div>
                        <div class="flex_middle editor_area font_ru" dir="ltr">
                            <div class="vnote" data-chunk="9">
                                <textarea name="chunks[9][title]" class="col-sm-6 peer_verse_ta textarea" style="overflow-x: hidden; overflow-wrap: break-word; min-height: 183px; height: 183px;">Тогда Бог сделал завет с Абрамом. Обычно завет - это соглашение между двумя сторонами, чтобы делать вещи друг для друга. Но в этом случае Бог дал обещание Аврам, а Аврам был глубоко сон, но он все еще мог слышать Бога. Бог сказал: «Я дам тебе сына из своего собственного тела. Я даю землю Ханаана ваших потомков ». Но Аврам все еще не было сына.</textarea>
                                <input name="chunks[9][img]" type="hidden" value="https://cdn.door43.org/obs/jpg/360px/obs-en-04-09.jpg">
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <span class="editComment mdi mdi-lead-pencil" data="4:9" title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <div class="obs_chunk no_margin" data-chunk="10" style="height: 90px;">
                                <div class="obs_title">A Bible story from: Genesis 11-15</div>
                            </div>
                        </div>
                        <div class="flex_middle editor_area font_ru" dir="ltr">
                            <div class="vnote" data-chunk="10">
                                <textarea name="chunks[10][title]" class="col-sm-6 peer_verse_ta textarea" style="overflow-x: hidden; overflow-wrap: break-word; min-height: 90px; height: 90px;">Библейская история от: Бытие 11-15</textarea>
                                <input name="chunks[10][img]" type="hidden" value="">
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <span class="editComment mdi mdi-lead-pencil" data="4:10" title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                </div>
                <div class="clear"></div>
            </div>

            <div class="main_content_footer row">
                <form action="" method="post">
                    <div class="form-group">
                        <div class="main_content_confirm_desc"><?php echo __("confirm_finished")?></div>
                        <label><input name="confirm_step" id="confirm_step" value="1" type="checkbox"> <?php echo __("confirm_yes")?></label>
                    </div>

                    <button id="next_step" class="btn btn-primary" disabled="disabled">
                        <?php echo __($data["next_step"])?>
                    </button>
                    <img src="<?php echo template_url("img/saving.gif") ?>" class="unsaved_alert">
                </form>
                <div class="step_right"><?php echo __("step_num", ["step_number" => 2])?></div>
            </div>
        </div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps is_checker_page_help">
            <div class="help_name_steps"><span><?php echo __("step_num", ["step_number" => 2])?>:</span> <?php echo __(EventSteps::PEER_REVIEW . "_obs")?></div>
            <div class="help_descr_steps">
                <ul><?php echo __("peer-review_obs_desc", ["step" => __($data["next_step"])])?></ul>
                <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
            </div>
        </div>

        <div class="event_info is_checker_page_help">
            <div class="participant_info">
                <div class="participant_name">
                    <span><?php echo __("your_checker") ?>:</span>
                    <span class="checker_name_span">
                                John C.
                            </span>
                </div>
                <div class="additional_info">
                    <a href="/events/demo-obs/information"><?php echo __("event_info") ?></a>
                </div>
            </div>
        </div>

        <div class="checker_view">
            <a href="/events/demo-obs/peer_review_checker"><?php echo __("checker_other_view", [2]) ?></a>
        </div>
    </div>
</div>

<div class="tutorial_container">
    <div class="tutorial_popup">
        <div class="tutorial-close glyphicon glyphicon-remove"></div>
        <div class="tutorial_pic">
            <img src="<?php echo template_url("img/steps/icons/peer-review.png") ?>" height="100px" width="100px">
            <img src="<?php echo template_url("img/steps/big/peer-review.png") ?>" height="280px" width="280px">
        </div>

        <div class="tutorial_content is_checker_page_help">
            <h3><?php echo __(EventSteps::PEER_REVIEW . "_obs")?></h3>
            <ul><?php echo __("peer-review_obs_desc", ["step" => __($data["next_step"])])?></ul>
        </div>
    </div>
</div>

<script>
    var isChecker = true;

    $(document).ready(function () {
        $("#next_step").click(function (e) {
            e.preventDefault();
            if(!hasChangesOnPage) window.location.href = '/events/demo-obs/pray';
            return false;
        });
    });
</script>