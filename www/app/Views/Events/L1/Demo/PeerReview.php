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

<div class="footnote_editor panel panel-default">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __("write_footnote_title")?></h1>
        <span class="footnote-editor-close btn btn-success"><?php echo __("save") ?></span>
        <span class="xbtnf glyphicon glyphicon-remove"></span>
    </div>
    <div class="footnote_window">
        <div class="fn_preview"></div>
        <div class="fn_buttons" dir="ltr">
            <button class="btn btn-default" data-fn="ft" title="footnote text">ft</button>
            <button class="btn btn-default" data-fn="fqa" title="footnote alternate translation">fqa</button>
        </div>
        <div class="fn_builder"></div>
    </div>
</div>

<div id="translator_contents" class="row panel-body">
    <div class="row main_content_header">
        <div class="main_content_title">
            <div class="demo_title"><?php echo __("demo") . " (".__("8steps_vmast").")" ?></div>
            <div><?php echo __("step_num", ["step_number" => 6]) . ": " . __("peer-review")?></div>
            <div class="action_type type_translation"><?php echo __("type_translation"); ?></div>
        </div>
    </div>

    <div class="main_content">
        <form action="" method="post" id="main_form">
            <div class="main_content_text row">
                <h4>English - <?php echo __("ulb") ?> - <?php echo __("new_test") ?> - <span class="book_name">2 Timothy 2:1-26</span></h4>

                <div class="col-sm-12">
                    <div class="row flex_container chunk_block">
                        <div style="padding: 0 15px 0 0;" class="chunk_verses flex_left">
                            <strong><sup>1</sup></strong>You therefore, my child, be strengthened in the grace that is in Christ Jesus.
                            <strong><sup>2</sup></strong>And the things you heard from me among many witnesses, entrust them to faithful people who will be able to teach others also.
                            <strong><sup>3</sup></strong>Suffer hardship with me, as a good soldier of Christ Jesus.
                        </div>
                        <div style="padding: 0;" class="editor_area flex_middle">
                            <div class="vnote">
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]" style="overflow: hidden; word-wrap: break-word; height: 266px;">Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <div class="comments_number  hasComment">2</div>

                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                    <div class="my_comment">This is translator's comment on this chunk</div>
                                    <div class="other_comments">
                                        <div><span>Marge S. - L1:</span> This is checker's comment on this chunk</div>
                                    </div>
                                </div>

                                <span class="editFootNote mdi mdi-bookmark" title="<?php echo __("write_footnote_title") ?>"></span>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div style="padding: 0 15px 0 0;" class="chunk_verses flex_left">
                            <strong><sup>4</sup></strong>No soldier serves while entangled in the affairs of this life, so that he may please his superior officer.
                            <strong><sup>5</sup></strong>Also, if someone competes as an athlete, he is not crowned unless he competes by the rules.
                            <strong><sup>6</sup></strong>It is necessary that the hardworking farmer receive his share of the crops first.
                            <strong><sup>7</sup></strong>Think about what I am saying, for the Lord will give you understanding in everything.
                        </div>
                        <div style="padding: 0;" class="editor_area flex_middle">
                            <div class="vnote">
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]" style="overflow: hidden; word-wrap: break-word; height: 266px;">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <div class="comments_number  hasComment">1</div>

                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                    <div class="my_comment">This is translator's comment on this chunk</div></div>

                                <span class="editFootNote mdi mdi-bookmark" title="<?php echo __("write_footnote_title") ?>"></span>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div style="padding: 0 15px 0 0;" class="chunk_verses flex_left">
                            <strong><sup>8</sup></strong>Remember Jesus Christ, from David's seed, who was raised from the dead ones. This is according to my gospel message,
                            <strong><sup>9</sup></strong>for which I am suffering to the point of being chained as a criminal. But the word of God is not chained.
                            <strong><sup>10</sup></strong>Therefore I endure all things for those who are chosen, so that they also may obtain the salvation that is in Christ Jesus, with eternal glory.
                        </div>
                        <div style="padding: 0;" class="editor_area flex_middle">
                            <div class="vnote">
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]" style="overflow: hidden; word-wrap: break-word; height: 266px;">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <div class="comments_number"></div>

                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments"></div>

                                <span class="editFootNote mdi mdi-bookmark" title="<?php echo __("write_footnote_title") ?>"></span>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div style="padding: 0 15px 0 0;" class="chunk_verses flex_left">
                            <strong><sup>11</sup></strong>This saying is trustworthy:   "If we have died with him, we will also live with him.
                            <strong><sup>12</sup></strong>If we endure, we will also reign with him. If we deny him, he also will deny us.
                            <strong><sup>13</sup></strong>if we are unfaithful, he remains faithful,  for he cannot deny himself."
                            <strong><sup>14</sup></strong>Keep reminding them of these things. Warn them before God not to quarrel about words. Because of this there is nothing useful. Because of this there is destruction for those who listen. <span class="mdi mdi-bookmark" title="" data-placement="auto right" data-toggle="tooltip" data-original-title="Some versions read, Warn them before the Lord "></span>
                        </div>
                        <div style="padding: 0;" class="editor_area flex_middle">
                            <div class="vnote">
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]" style="overflow: hidden; word-wrap: break-word; height: 266px;">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <div class="comments_number"></div>

                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments"></div>

                                <span class="editFootNote mdi mdi-bookmark" title="<?php echo __("write_footnote_title") ?>"></span>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div style="padding: 0 15px 0 0;" class="chunk_verses flex_left">
                            <strong><sup>15</sup></strong>Do your best to present yourself to God as one approved, a worker who has no reason to be ashamed, who accurately teaches the word of truth.
                            <strong><sup>16</sup></strong>Avoid profane talk, which leads to more and more godlessness.
                            <strong><sup>17</sup></strong>Their talk will spread like gangrene. Among whom are Hymenaeus and Philetus.
                        </div>
                        <div style="padding: 0;" class="editor_area flex_middle">
                            <div class="vnote">
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]" style="overflow: hidden; word-wrap: break-word; height: 266px;">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <div class="comments_number"></div>

                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments"></div>

                                <span class="editFootNote mdi mdi-bookmark" title="<?php echo __("write_footnote_title") ?>"></span>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div style="padding: 0 15px 0 0;" class="chunk_verses flex_left">
                            <strong><sup>18</sup></strong>These are men who have missed the truth. They say that the resurrection has already happened. They overturn the faith of some.
                            <strong><sup>19</sup></strong>However, the firm foundation of God stands. It has this inscription: "The Lord knows those who are his" and "Everyone who names the name of the Lord must depart from unrighteousness."
                            <strong><sup>20</sup></strong>In a wealthy home, there are not only containers of gold and silver. There are also containers of wood and clay. Some of these are for honorable use, and some for dishonorable.
                        </div>
                        <div style="padding: 0;" class="editor_area flex_middle">
                            <div class="vnote">
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]" style="overflow: hidden; word-wrap: break-word; height: 266px;">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <div class="comments_number"></div>

                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments"></div>

                                <span class="editFootNote mdi mdi-bookmark" title="<?php echo __("write_footnote_title") ?>"></span>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div style="padding: 0 15px 0 0;" class="chunk_verses flex_left">
                            <strong><sup>21</sup></strong>If someone cleans himself from dishonorable use, he is an honorable container. He is set apart, useful to the Master, and prepared for every good work.
                            <strong><sup>22</sup></strong>Flee youthful lusts. Pursue righteousness, faith, love, and peace with those who call on the Lord out of a clean heart.
                            <strong><sup>23</sup></strong>But refuse foolish and ignorant questions. You know that they give birth to arguments.
                        </div>
                        <div style="padding: 0;" class="editor_area flex_middle">
                            <div class="vnote">
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]" style="overflow: hidden; word-wrap: break-word; height: 266px;">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <div class="comments_number"></div>

                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments"></div>

                                <span class="editFootNote mdi mdi-bookmark" title="<?php echo __("write_footnote_title") ?>"></span>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row flex_container chunk_block">
                        <div style="padding: 0 15px 0 0;" class="chunk_verses flex_left">
                            <strong><sup>24</sup></strong>The Lord's servant must not quarrel. Instead he must be gentle toward all, able to teach, and patient.
                            <strong><sup>25</sup></strong>He must in meekness educate those who oppose him. God may perhaps give them repentance for the knowledge of the truth.
                            <strong><sup>26</sup></strong>They may become sober again and leave the devil's trap, after they have been captured by him for his will.
                        </div>
                        <div style="padding: 0;" class="editor_area flex_middle">
                            <div class="vnote">
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]" style="overflow: hidden; word-wrap: break-word; height: 266px;">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="notes_tools">
                                <div class="comments_number"></div>

                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments"></div>

                                <span class="editFootNote mdi mdi-bookmark" title="<?php echo __("write_footnote_title") ?>"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="main_content_footer row">
                <div class="form-group">
                    <div class="main_content_confirm_desc"><?php echo __("confirm_finished")?></div>
                    <label><input name="confirm_step" id="confirm_step" value="1" type="checkbox"> <?php echo __("confirm_yes")?></label>
                </div>

                <button id="next_step" class="btn btn-primary" disabled="disabled">
                    <?php echo __($data["next_step"])?>
                </button>
                <img src="<?php echo template_url("img/saving.gif") ?>" class="unsaved_alert">
            </div>
        </form>
        <div class="step_right alt"><?php echo __("step_num", ["step_number" => 6])?></div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps">
            <div class="help_name_steps"><span><?php echo __("step_num", ["step_number" => 6])?>:</span> <?php echo __("peer-review")?></div>
            <div class="help_descr_steps">
                <ul><?php echo __("peer-review_desc", ["step" => __($data["next_step"])])?></ul>
                <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
            </div>
        </div>

        <div class="event_info">
            <div class="participant_info">
                <div class="participant_name">
                    <span><?php echo __("your_checker") ?>:</span>
                    <span>Marge S.</span>
                </div>
                <div class="additional_info">
                    <a href="/events/demo/information"><?php echo __("event_info") ?></a>
                </div>
            </div>
        </div>

        <div class="tr_tools">
            <button class="btn btn-primary ttools" data-tool="tn"><?php echo __("show_notes") ?></button>
            <button class="btn btn-primary ttools" data-tool="tq"><?php echo __("show_questions") ?></button>
            <button class="btn btn-primary ttools" data-tool="bc"><?php echo __("show_bible_commentaries") ?></button>
            <button class="btn btn-warning ttools" data-tool="rubric"><?php echo __("show_rubric") ?></button>
        </div>

        <div class="checker_view">
            <a href="<?php echo SITEURL ?>events/demo/peer_review_checker"><?php echo __("checker_view") ?></a>
        </div>
    </div>
</div>

<input type="hidden" id="bookCode" value="2ti">
<input type="hidden" id="chapter" value="2">
<input type="hidden" id="tn_lang" value="en">
<input type="hidden" id="tq_lang" value="en">
<input type="hidden" id="bc_lang" value="en">
<input type="hidden" id="totalVerses" value="26">
<input type="hidden" id="targetLang" value="en">

<div class="tutorial_container">
    <div class="tutorial_popup">
        <div class="tutorial-close glyphicon glyphicon-remove"></div>
        <div class="tutorial_pic">
            <img src="<?php echo template_url("img/steps/icons/peer-review.png") ?>" height="100px" width="100px">
            <img src="<?php echo template_url("img/steps/big/peer-review.png") ?>" height="280px" width="280px">
            
        </div>

        <div class="tutorial_content">
            <h3><?php echo __("peer-review")?></h3>
            <ul><?php echo __("peer-review_desc", ["step" => __($data["next_step"])])?></ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#next_step").click(function (e) {
            e.preventDefault();
            if(!hasChangesOnPage) window.location.href = '/events/demo/keyword_check';
            return false;
        });

        $(".peer_verse_ta").highlightWithinTextarea({
            highlight: /\\f\s[+-]\s(.*?)\\f\*/gi
        });

        $(".ttools_panel .word_def").each(function() {
            let html = convertRcLinks($(this).html());
            $(this).html(html);
        });
    });
</script>