<?php
use Helpers\Constants\EventCheckSteps;
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
            <div class="demo_title"><?php echo __("demo") . " (".__("vsail_revision").")" ?></div>
            <div><?php echo __("step_num", ["step_number" => 3]) . ": " . __(EventCheckSteps::PEER_REVIEW . "_sun")?></div>
        </div>
    </div>

    <div class="">
        <div class="main_content">
            <div class="main_content_text">
                <h4>SUN - <?php echo __("sun") ?> - <?php echo __("new_test") ?> - <span class="book_name">2 Timothy 2:1-26</span></h4>

                <div class="no_padding">
                    <div class="sun_mode">
                        <label>
                            <input type="checkbox" autocomplete="off" checked
                                   data-toggle="toggle"
                                   data-on="SUN"
                                   data-off="BACKSUN" />
                        </label>
                    </div>

                    <div class="row chunk_block no_autosize">
                        <div class="flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="1" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>1</sup>
                                    </strong>
                                    You therefore, my child, be strengthened in the grace that is in Christ Jesus.                                            </p>
                                <p class="verse_text" data-verse="2" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>2</sup>
                                    </strong>
                                    The things you heard from me among many witnesses, entrust them to faithful people who will be able to teach others also.                                            </p>
                            </div>
                            <div class="flex_middle editor_area sun_content">
                                <div class="vnote">
                                    <div class="verse_block flex_chunk" data-verse="1">
                                        <textarea name="chunks[0][1]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">         </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="2">
                                        <textarea name="chunks[0][2]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">                      </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="comments_number tncomml3_alt  hasComment">2</div>
                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>

                                <div class="comments">
                                    <div class="my_comment">A note from current theological checker</div>
                                    <div class="other_comments"><span>Henry M. - L2: </span>A note from level 2 peer checker</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize">
                        <div class="flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="3" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>3</sup>
                                    </strong>
                                    Suffer hardship with me, as a good soldier of Christ Jesus.                                            </p>
                                <p class="verse_text" data-verse="4" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>4</sup>
                                    </strong>
                                    No soldier serves while entangled in the affairs of this life, so that he may please his superior officer.                                            </p>
                                <p class="verse_text" data-verse="5" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>5</sup>
                                    </strong>
                                    Also, if someone competes as an athlete, he is not crowned unless he competes by the rules.                                            </p>
                            </div>
                            <div class="flex_middle editor_area sun_content">
                                <div class="vnote">
                                    <div class="verse_block flex_chunk" data-verse="3">
                                        <textarea name="chunks[1][3]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">           </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="4">
                                        <textarea name="chunks[1][4]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">              </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="5">
                                        <textarea name="chunks[1][5]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">             </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="notes_tools">
                                    <div class="comments_number flex_commn_number ">
                                    </div>
                                    <span class="editComment mdi mdi-lead-pencil" data="2:1" title="<?php echo __("write_note_title", [""])?>"></span>

                                    <div class="comments">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize">
                        <div class="flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="6" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>6</sup>
                                    </strong>
                                    It is necessary that the hardworking farmer receive his share of the crops first.                                            </p>
                                <p class="verse_text" data-verse="7" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>7</sup>
                                    </strong>
                                    Think about what I am saying, for the Lord will give you understanding in everything.                                            </p>
                            </div>
                            <div class="flex_middle editor_area sun_content">
                                <div class="vnote">
                                    <div class="verse_block flex_chunk" data-verse="6">
                                        <textarea name="chunks[2][6]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">           </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="7">
                                        <textarea name="chunks[2][7]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">          </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="notes_tools">
                                    <div class="comments_number flex_commn_number ">
                                    </div>
                                    <span class="editComment mdi mdi-lead-pencil" data="2:2" title="<?php echo __("write_note_title", [""])?>"></span>

                                    <div class="comments">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize">
                        <div class="flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="8" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>8</sup>
                                    </strong>
                                    Remember Jesus Christ, from David's seed, who was raised from the dead. This is according to my gospel message,                                            </p>
                                <p class="verse_text" data-verse="9" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>9</sup>
                                    </strong>
                                    for which I am suffering to the point of being bound with chains as a criminal. But the word of God is not bound.                                            </p>
                                <p class="verse_text" data-verse="10" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>10</sup>
                                    </strong>
                                    Therefore I endure all things for those who are chosen, so that they also may obtain the salvation that is in Christ Jesus, with eternal glory.                                            </p>
                            </div>
                            <div class="flex_middle editor_area sun_content">
                                <div class="vnote">
                                    <div class="verse_block flex_chunk" data-verse="8">
                                        <textarea name="chunks[3][8]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">   ,           </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="9">
                                        <textarea name="chunks[3][9]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">                  </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="10">
                                        <textarea name="chunks[3][10]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">                      </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="notes_tools">
                                    <div class="comments_number flex_commn_number ">
                                    </div>
                                    <span class="editComment mdi mdi-lead-pencil" data="2:3" title="<?php echo __("write_note_title", [""])?>"></span>

                                    <div class="comments">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize">
                        <div class="flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="11" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>11</sup>
                                    </strong>
                                    This is a trustworthy saying:   "If we have died with him, we will also live with him.</p>
                                <p class="verse_text" data-verse="12" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>12</sup>
                                    </strong>
                                    If we endure, we will also reign with him.  If we deny him, he also will deny us.</p>
                                <p class="verse_text" data-verse="13" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>13</sup>
                                    </strong>
                                    if we are unfaithful, he remains faithful,  for he cannot deny himself."</p>
                            </div>
                            <div class="flex_middle editor_area sun_content">
                                <div class="vnote">
                                    <div class="verse_block flex_chunk" data-verse="11">
                                        <textarea name="chunks[4][11]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">              </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="12">
                                        <textarea name="chunks[4][12]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">   ,          ,    </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="13">
                                        <textarea name="chunks[4][13]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">    ,          </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="notes_tools">
                                    <div class="comments_number flex_commn_number ">
                                    </div>
                                    <span class="editComment mdi mdi-lead-pencil" data="2:4" title="<?php echo __("write_note_title", [""])?>"></span>

                                    <div class="comments">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize">
                        <div class="flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="14" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>14</sup>
                                    </strong>
                                    Keep reminding them of these things. Warn them before God against quarreling about words; it is of no value, and only ruins those who listen.  <span data-toggle="tooltip" data-placement="auto auto" title="" class="booknote mdi mdi-bookmark" data-original-title="Some versions read,  Warn them before the Lord  . "></span></p>
                                <p class="verse_text" data-verse="15" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>15</sup>
                                    </strong>
                                    Do your best to present yourself to God as one approved, a worker who has no reason to be ashamed, who accurately teaches the word of truth.                                            </p>
                            </div>
                            <div class="flex_middle editor_area sun_content">
                                <div class="vnote">
                                    <div class="verse_block flex_chunk" data-verse="14">
                                        <textarea name="chunks[5][14]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">                         </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="15">
                                        <textarea name="chunks[5][15]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">                     </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="notes_tools">
                                    <div class="comments_number flex_commn_number ">
                                    </div>
                                    <span class="editComment mdi mdi-lead-pencil" data="2:5" title="<?php echo __("write_note_title", [""])?>"></span>

                                    <div class="comments">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize">
                        <div class="flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="16" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>16</sup>
                                    </strong>
                                    Avoid profane talk, which leads to more and more godlessness.                                            </p>
                                <p class="verse_text" data-verse="17" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>17</sup>
                                    </strong>
                                    Their talk will spread like cancer. Among them are Hymenaeus and Philetus,                                            </p>
                                <p class="verse_text" data-verse="18" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>18</sup>
                                    </strong>
                                    who have gone astray from the truth. They say that the resurrection has already happened, and they destroy the faith of some.                                            </p>
                            </div>
                            <div class="flex_middle editor_area sun_content">
                                <div class="vnote">
                                    <div class="verse_block flex_chunk" data-verse="16">
                                        <textarea name="chunks[6][16]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">    ,          </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="17">
                                        <textarea name="chunks[6][17]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">           </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="18">
                                        <textarea name="chunks[6][18]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">               </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="notes_tools">
                                    <div class="comments_number flex_commn_number ">
                                    </div>
                                    <span class="editComment mdi mdi-lead-pencil" data="2:6" title="<?php echo __("write_note_title", [""])?>"></span>

                                    <div class="comments">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize">
                        <div class="flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="19" style="height: 111px;">
                                    <strong class="ltr">
                                        <sup>19</sup>
                                    </strong>
                                    However, the firm foundation of God stands. It has this inscription: "The Lord knows those who are his" and "Everyone who names the name of the Lord must depart from unrighteousness."                                            </p>
                                <p class="verse_text" data-verse="20" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>20</sup>
                                    </strong>
                                    In a wealthy home, there are not only containers of gold and silver. There are also containers of wood and clay. Some of these are for honorable use, and some for dishonorable.                                            </p>
                                <p class="verse_text" data-verse="21" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>21</sup>
                                    </strong>
                                    If someone cleans himself from dishonorable use, he is an honorable container. He is set apart, useful to the Master, and prepared for every good work.                                            </p>
                            </div>
                            <div class="flex_middle editor_area sun_content">
                                <div class="vnote">
                                    <div class="verse_block flex_chunk" data-verse="19">
                                        <textarea name="chunks[7][19]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 111px;">       ,                 </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="20">
                                        <textarea name="chunks[7][20]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">                          </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="21">
                                        <textarea name="chunks[7][21]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">                       </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="notes_tools">
                                    <div class="comments_number flex_commn_number ">
                                    </div>
                                    <span class="editComment mdi mdi-lead-pencil" data="2:7" title="<?php echo __("write_note_title", [""])?>"></span>

                                    <div class="comments">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize">
                        <div class="flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="22" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>22</sup>
                                    </strong>
                                    Flee youthful lusts. Pursue righteousness, faith, love, and peace with those who call on the Lord out of a clean heart.                                            </p>
                                <p class="verse_text" data-verse="23" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>23</sup>
                                    </strong>
                                    But refuse foolish and ignorant questions. You know that they give birth to arguments.                                            </p>
                            </div>
                            <div class="flex_middle editor_area sun_content">
                                <div class="vnote">
                                    <div class="verse_block flex_chunk" data-verse="22">
                                        <textarea name="chunks[8][22]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">        ,  ,                </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="23">
                                        <textarea name="chunks[8][23]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">            </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="notes_tools">
                                    <div class="comments_number flex_commn_number ">
                                    </div>
                                    <span class="editComment mdi mdi-lead-pencil" data="2:8" title="<?php echo __("write_note_title", [""])?>"></span>

                                    <div class="comments">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize">
                        <div class="flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="24" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>24</sup>
                                    </strong>
                                    The Lord's servant must not quarrel. Instead he must be gentle toward all, able to teach, and patient.                                            </p>
                                <p class="verse_text" data-verse="25" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>25</sup>
                                    </strong>
                                    He must in meekness educate those who oppose him. God may perhaps give them repentance for the knowledge of the truth.                                            </p>
                                <p class="verse_text" data-verse="26" style="height: 80px;">
                                    <strong class="ltr">
                                        <sup>26</sup>
                                    </strong>
                                    They may become sober again and leave the devil's trap, after they have been captured by him for his will.                                            </p>
                            </div>
                            <div class="flex_middle editor_area sun_content">
                                <div class="vnote">
                                    <div class="verse_block flex_chunk" data-verse="24">
                                        <textarea name="chunks[9][24]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">                 </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="25">
                                        <textarea name="chunks[9][25]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">                  </textarea>
                                    </div>
                                    <div class="verse_block flex_chunk" data-verse="26">
                                        <textarea name="chunks[9][26]" class="peer_verse_ta textarea" style="min-width: 400px; overflow-x: hidden; overflow-wrap: break-word; height: 80px;">             </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="notes_tools">
                                    <div class="comments_number flex_commn_number ">
                                    </div>
                                    <span class="editComment mdi mdi-lead-pencil" data="2:9" title="<?php echo __("write_note_title", [""])?>"></span>

                                    <div class="comments">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                </div>
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
                <div class="step_right"><?php echo __("step_num", ["step_number" => 3])?></div>
            </div>
        </div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps is_checker_page_help">
            <div class="help_name_steps"><span><?php echo __("step_num", ["step_number" => 3])?>:</span> <?php echo __(EventCheckSteps::PEER_REVIEW . "_sun")?></div>
            <div class="help_descr_steps">
                <ul><?php echo __("theo-check_sun_l2_desc", ["step" => __($data["next_step"])])?></ul>
                <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
            </div>
        </div>

        <div class="event_info is_checker_page_help">
            <div class="participant_info">
                <div class="additional_info">
                    <a href="/events/demo-sun-revision/information"><?php echo __("event_info") ?></a>
                </div>
            </div>
        </div>

        <div class="tr_tools">
            <button class="btn btn-warning ttools" data-tool="saildict"><?php echo __("show_dictionary") ?></button>
            <button class="btn btn-primary ttools" data-tool="tn"><?php echo __("show_notes") ?></button>
            <button class="btn btn-primary ttools" data-tool="tw"><?php echo __("show_keywords") ?></button>
        </div>
    </div>
</div>

<div class="tutorial_container">
    <div class="tutorial_popup">
        <div class="tutorial-close glyphicon glyphicon-remove"></div>
        <div class="tutorial_pic">
            <img src="<?php echo template_url("img/steps/icons/theo-check-gray.png") ?>" height="100px" width="100px">
            <img src="<?php echo template_url("img/steps/big/keyword-check.png") ?>" height="280px" width="280px">
        </div>

        <div class="tutorial_content">
            <h3><?php echo __(EventCheckSteps::PEER_REVIEW . "_sun")?></h3>
            <ul><?php echo __("theo-check_sun_l2_desc", ["step" => __($data["next_step"])])?></ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#next_step").click(function (e) {
            e.preventDefault();
            if(!hasChangesOnPage) window.location.href = '/events/demo-sun-revision/pray';
            return false;
        });

        setTimeout(function() {
            equal_verses_height();
        }, 100);

        $(".peer_verse_ta").blur(function() {
            equal_verses_height();
        });

        function equal_verses_height() {
            $(".verse_text").each(function() {
                var verse = $(this).data("verse");
                var p_height = $(this).outerHeight();
                var ta = $(".verse_block[data-verse="+verse+"] textarea");

                if(ta.length > 0) {
                    var t_height = ta.outerHeight();
                    ta.outerHeight(Math.max(p_height, t_height));
                    $(this).outerHeight(Math.max(p_height, t_height));
                }
            });
        }

        $(".sun_mode input").change(function () {
            var active = $(this).prop('checked');

            if (active) {
                $(".flex_middle").removeClass("font_backsun");
                $(".flex_middle").addClass("sun_content");
            } else {
                $(".flex_middle").removeClass("sun_content");
                $(".flex_middle").addClass("font_backsun");
            }

            $("p.verse_text").css("height", "initial");
            autosize.update($(".verse_block textarea"));
            equal_verses_height();
        });

        $(".ttools_panel .word_def").each(function() {
            let html = convertRcLinks($(this).html());
            $(this).html(html);
        });
    });
</script>

<input type="hidden" id="bookCode" value="<?php echo $data["bookCode"] ?>">
<input type="hidden" id="chapter" value="<?php echo $data["currentChapter"] ?>">
<input type="hidden" id="tn_lang" value="<?php echo $data["tn_lang"] ?>">
<input type="hidden" id="tw_lang" value="<?php echo $data["tw_lang"] ?>">
<input type="hidden" id="totalVerses" value="<?php echo $data["totalVerses"] ?>">
<input type="hidden" id="targetLang" value="<?php echo $data["targetLang"] ?>">