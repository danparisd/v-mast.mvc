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
            <div><?php echo __("step_num", ["step_number" => 8]), ": " . __("content-review")?></div>
            <div class="action_type type_translation"><?php echo __("type_translation"); ?></div>
        </div>
    </div>

    <div class="main_content">
        <form action="" method="post" id="main_form">
            <div class="main_content_text row">
                <h4>English - <?php echo __("ulb") ?> - <?php echo __("new_test") ?> - <span class="book_name">2 Timothy 2:1-26</span></h4>

                <div class="row">
                    <div class="col-sm-12 side_by_side_toggle">
                        <label><input type="checkbox" id="side_by_side_toggle" value="0"> <?php echo __("side_by_side_toggle") ?></label>
                    </div>
                </div>

                <div class="col-sm-12 side_by_side_translator">
                    <div class="row flex_container chunk_block">
                        <div style="padding: 0 15px 0 0;" class="chunk_verses flex_left">
                            <strong><sup>1</sup></strong>You therefore, my child, be strengthened in the grace that is in Christ Jesus.
                            <strong><sup>2</sup></strong>And the things you heard from me among many witnesses, entrust them to faithful people who will be able to teach others also.
                            <strong><sup>3</sup></strong>Suffer hardship with me, as a good soldier of Christ Jesus.
                        </div>
                        <div style="padding: 0;" class="editor_area flex_middle">
                            <div class="vnote">
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]">Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
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
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
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
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
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
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
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
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
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
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
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
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
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
                                <textarea class="col-sm-6 peer_verse_ta textarea" name="chunks[]">Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text, Demo translation text</textarea>
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
        <div class="step_right alt"><?php echo __("step_num", ["step_number" => 8])?></div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps">
            <div class="help_name_steps"><span><?php echo __("step_num", ["step_number" => 8])?>:</span> <?php echo __("content-review")?></div>
            <div class="help_descr_steps">
                <ul><?php echo __("content-review_desc", ["step" => __($data["next_step"])])?></ul>
                <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
            </div>
        </div>

        <div class="event_info">
            <div class="participant_info">
                <div class="participant_name">
                    <span><?php echo __("your_checker") ?>:</span>
                    <span class="checker_name_span">John B.</span>
                </div>
                <div class="additional_info">
                    <a href="/events/demo/information"><?php echo __("event_info") ?></a>
                </div>
            </div>
        </div>

        <div class="tr_tools">
            <button class="btn btn-primary ttools" data-tool="tn"><?php echo __("show_notes") ?></button>
            <button class="btn btn-primary ttools" data-tool="tq"><?php echo __("show_questions") ?></button>
            <button class="btn btn-primary ttools" data-tool="tw"><?php echo __("show_keywords") ?></button>
            <button class="btn btn-primary ttools" data-tool="bc"><?php echo __("show_bible_commentaries") ?></button>
            <button class="btn btn-warning ttools" data-tool="rubric"><?php echo __("show_rubric") ?></button>
        </div>

        <div class="checker_view">
            <a href="/events/demo/content_review_checker"><?php echo __("checker_view") ?></a>
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

<div class="ttools_panel rubric_tool panel panel-default" draggable="true">
    <div class="panel-heading">
        <h1 class="panel-title"><?php echo __("show_rubric") ?></h1>
        <span class="panel-close glyphicon glyphicon-remove" data-tool="rubric"></span>
    </div>

    <div class="ttools_content page-content panel-body">
        <ul class="nav nav-tabs nav-justified read_rubric_tabs">
            <li role="presentation" id="tab_orig" class="active"><a href="#">English demo1</a></li>
            <li role="presentation" id='tab_eng'><a href="#">English</a></li>
        </ul>
        <div class="read_rubric_qualities">
            <br>
            <div class="read_rubric_quality orig" dir="ltr"> 1. Accessible </div>
            <div class="read_rubric_quality eng" style="display: none;"> </div>
            <div class="read_rubric_defs">
                <div class="read_rubric_def orig" dir="ltr"> 1. Created in necessary formats. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Is it created in necessary formats? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 2. Easily reproduced and distributed. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Is it easily reproduced? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                    <div class="read_rubric_measurement orig" dir="ltr"> 2. Is it easily distributed? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 3. Appropriate font, size and layout. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Is it in the appropriate font, size and layout? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 4. Editable. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Is it editable? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
            </div>
            <div class="read_rubric_quality orig" dir="ltr"> 2. Faithful </div>
            <div class="read_rubric_quality eng" style="display: none;"> </div>
            <div class="read_rubric_defs">
                <div class="read_rubric_def orig" dir="ltr"> 1. Reflects Original Text. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Does in reflect original text? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 2. True to Greek and Hebrew. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Is it true to Greek and Hebrew? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 3. Does not have additions or deletions. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Does it have additions or deletions? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 4. Names of God retained. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Are the names of God retained? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 5. Accurate key terms/key words. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Are key terms/words accurate? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
            </div>
            <div class="read_rubric_quality orig" dir="ltr"> 3. Culturally Relevant </div>
            <div class="read_rubric_quality eng" style="display: none;"> </div>
            <div class="read_rubric_defs">
                <div class="read_rubric_def orig" dir="ltr"> 1. Idioms are understandable </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Are idioms understandable? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 2. Words and expressions appropriate for local culture. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Are words and expressions appropriate for local culture? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 3. Reflects original language artistry. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Does it reflect original language artistry? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 4. Captures literary genres. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Are literary genres captured accurately? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
            </div>
            <div class="read_rubric_quality orig" dir="ltr"> 4. Clear </div>
            <div class="read_rubric_quality eng" style="display: none;"> </div>
            <div class="read_rubric_defs">
                <div class="read_rubric_def orig" dir="ltr"> 1. Meaning is clear. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Is the meaning clear? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 2. Uses common language. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Does it use common language? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 3. Easily understood by wide audience. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Is it easily understood by a wide audience? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
            </div>
            <div class="read_rubric_quality orig" dir="ltr"> 5. Proper Grammar </div>
            <div class="read_rubric_quality eng" style="display: none;"> </div>
            <div class="read_rubric_defs">
                <div class="read_rubric_def orig" dir="ltr"> 1. Follows grammar norms. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Does it follow grammar norms? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 2. Correct punctuation. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Is correct punctuation used? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
            </div>
            <div class="read_rubric_quality orig" dir="ltr"> 6. Consistent </div>
            <div class="read_rubric_quality eng" style="display: none;"> </div>
            <div class="read_rubric_defs">
                <div class="read_rubric_def orig" dir="ltr"> 1. Translation reflects contextual meaning. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Does the translation reflect contextual meaning? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 2. Does not contradict itself. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Does the text contradict itself? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 3. Writing style consistent. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Is the writing style consistent? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
            </div>
            <div class="read_rubric_quality orig" dir="ltr"> 7. Historically Accurate </div>
            <div class="read_rubric_quality eng" style="display: none;"> </div>
            <div class="read_rubric_defs">
                <div class="read_rubric_def orig" dir="ltr"> 1. All names, dates, places, events are accurately represented. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Are all names accurately represented? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                    <div class="read_rubric_measurement orig" dir="ltr"> 2. Are all dates accurately represented? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                    <div class="read_rubric_measurement orig" dir="ltr"> 3. Are all places accurately represented? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                    <div class="read_rubric_measurement orig" dir="ltr"> 4. Are all events accurately represented? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
            </div>
            <div class="read_rubric_quality orig" dir="ltr"> 8. Natural </div>
            <div class="read_rubric_quality eng" style="display: none;"> </div>
            <div class="read_rubric_defs">
                <div class="read_rubric_def orig" dir="ltr"> 1. Translation uses common and natural language. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Does the translation use common and natural language? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 2. Pleasant to read/listen to. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. It is pleasant to read/listen to? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 3. Easy to read. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Is it easy to read? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
            </div>
            <div class="read_rubric_quality orig" dir="ltr"> 9. Objective </div>
            <div class="read_rubric_quality eng" style="display: none;"> </div>
            <div class="read_rubric_defs">
                <div class="read_rubric_def orig" dir="ltr"> 1. Translation does not explain or commentate. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Does the translation explain or commentate? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
                <div class="read_rubric_def orig" dir="ltr"> 2. Translation is free of political, social, denominational bias. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Is translation is free of political bias? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                    <div class="read_rubric_measurement orig" dir="ltr"> 2. Is translation is free of social bias? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                    <div class="read_rubric_measurement orig" dir="ltr"> 3. Is translation is free of denominational bias? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
            </div>
            <div class="read_rubric_quality orig" dir="ltr"> 10. Widely Accepted </div>
            <div class="read_rubric_quality eng" style="display: none;"> </div>
            <div class="read_rubric_defs">
                <div class="read_rubric_def orig" dir="ltr"> 1. Translation is widely accepted by local church. </div>
                <div class="read_rubric_def eng" style="display: none;"> </div>
                <div class="read_rubric_measurements">
                    <div class="read_rubric_measurement orig" dir="ltr"> 1. Is translation widely accepted by the local church? </div>
                    <div class="read_rubric_measurement eng" style="display: none;"> </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tutorial_container">
    <div class="tutorial_popup">
        <div class="tutorial-close glyphicon glyphicon-remove"></div>
        <div class="tutorial_pic">
            <img src="<?php echo template_url("img/steps/icons/content-review.png") ?>" height="100px" width="100px">
            <img src="<?php echo template_url("img/steps/big/content-review.png") ?>" height="280px" width="280px">

        </div>

        <div class="tutorial_content">
            <h3><?php echo __("content-review")?></h3>
            <ul><?php echo __("content-review_desc", ["step" => __($data["next_step"])]);?></ul>
        </div>
    </div>
</div>

<script>
    var isChecker = false;
    $(document).ready(function () {
        $("#next_step").click(function (e) {
            e.preventDefault();
            if(!hasChangesOnPage) window.location.href = '/events/demo/final_review';
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