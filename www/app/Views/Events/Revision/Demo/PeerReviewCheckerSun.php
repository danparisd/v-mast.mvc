<div class="comment_div panel panel-default">
    <div class="panel-heading">
        <h1 class="panel-title"><?php use Helpers\Constants\EventCheckSteps;

            echo __("write_note_title")?></h1>
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
            <div class="demo_title"><?php echo __("demo") . " (".__("revision_events").")" ?></div>
            <div><?php echo __("step_num", ["step_number" => 3]) . ": " . __(EventCheckSteps::PEER_REVIEW)?></div>
        </div>
    </div>

    <div class="">
        <div class="main_content">
            <div class="main_content_text">
                <h4>Symbolic Universal Notation - <?php echo __("ulb") ?> - <?php echo __("new_test") ?> - <span class="book_name">2 Timothy 2:1-26</span></h4>

                <ul class="nav nav-tabs">
                    <li role="presentation" id="target_scripture" class="my_tab">
                        <a href="#"><?php echo __("target_text") ?></a>
                    </li>
                    <li role="presentation" id="source_scripture" class="my_tab">
                        <a href="#"><?php echo __("source_text") ?></a>
                    </li>
                </ul>

                <div id="target_scripture_content" class="my_content shown">
                    <div class="no_padding">
                        <div class="sun_mode">
                            <label>
                                <input type="checkbox" autocomplete="off" checked
                                       data-toggle="toggle"
                                       data-width="100"
                                       data-on="SUN"
                                       data-off="BACKSUN" />
                            </label>
                        </div>
                        <div class="row chunk_block no_autosize">
                            <div class="flex_container">
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="1" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>1</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="1">         </ins></span>
                                    </p>
                                    <p class="verse_text" data-verse="2" style="height: 84px;">
                                        <strong class="ltr">
                                            <sup>2</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="2">                      </span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="1" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="1">           </span>
                                            </p>
                                        </div>
                                        <div class="verse_block flex_chunk" data-verse="2" style="margin-bottom: 10px; height: 84px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="2">                     </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex_right">
                                    <div class="comments_number hasComment">1 </div>
                                    <span class="editComment mdi mdi-lead-pencil"
                                          data="0:0"
                                          title="<?php echo __("write_note_title", [""])?>"></span>
                                    <div class="comments">
                                        <div class="other_comments">
                                            <span>Anna S. - L1:</span> This is comment from Level 1 Event
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chunk_divider"></div>
                        <div class="row chunk_block no_autosize">
                            <div class="flex_container">
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="3" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>3</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="3"><span>            </span></span>
                                    </p>
                                    <p class="verse_text" data-verse="4" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>4</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="4"><span>               </span></span>
                                    </p>
                                    <p class="verse_text" data-verse="5" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>5</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="5"><span>             </span></span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="3" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="3">            </span>
                                            </p>
                                        </div>
                                        <div class="verse_block flex_chunk" data-verse="4" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="4">               </span>
                                            </p>
                                        </div>
                                        <div class="verse_block flex_chunk" data-verse="5" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="5">             </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex_right">
                                    <div class="comments_number hasComment">1 </div>
                                    <span class="editComment mdi mdi-lead-pencil"
                                          data="0:0"
                                          title="<?php echo __("write_note_title", [""])?>"></span>
                                    <div class="comments">
                                        <div class="my_comment">This is my comment as a Revision checker</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chunk_divider"></div>
                        <div class="row chunk_block no_autosize">
                            <div class="flex_container">
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="6" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>6</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="6"><span>            </span></span>
                                    </p>
                                    <p class="verse_text" data-verse="7" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>7</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="7"><span>          </span></span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="6" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="6">            </span>
                                            </p>
                                        </div>
                                        <div class="verse_block flex_chunk" data-verse="7" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="7">          </span>
                                            </p>
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
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="8" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>8</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="8"><span>   ,            </span></span>
                                    </p>
                                    <p class="verse_text" data-verse="9" style="height: 84px;">
                                        <strong class="ltr">
                                            <sup>9</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="9"><span>                  </span></span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="8" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="8">   ,            </span>
                                            </p>
                                        </div>
                                        <div class="verse_block flex_chunk" data-verse="9" style="margin-bottom: 10px; height: 84px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="9">                  </span>
                                            </p>
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
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="10" style="height: 84px;">
                                        <strong class="ltr">
                                            <sup>10</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="10"><span>                      </span></span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="10" style="margin-bottom: 10px; height: 84px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="10">                      </span>
                                            </p>
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
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="11" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>11</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="11"><span>   "            </span></span>
                                    </p>
                                    <p class="verse_text" data-verse="12" style="height: 74px;">
                                        <strong class="ltr">
                                            <sup>12</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="12"><span>   ,          ,     </span></span>
                                    </p>
                                    <p class="verse_text" data-verse="13" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>13</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="13"><span>    ,          "</span></span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="11" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="11">   "            </span>
                                            </p>
                                        </div>
                                        <div class="verse_block flex_chunk" data-verse="12" style="margin-bottom: 10px; height: 74px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="12">   ,          ,     </span>
                                            </p>
                                        </div>
                                        <div class="verse_block flex_chunk" data-verse="13" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="13">    ,          "</span>
                                            </p>
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
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="14" style="height: 84px;">
                                        <strong class="ltr">
                                            <sup>14</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="14"><span>                         </span></span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="14" style="margin-bottom: 10px; height: 84px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="14">                         </span>
                                            </p>
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
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="15" style="height: 84px;">
                                        <strong class="ltr">
                                            <sup>15</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="15"><span>                     </span></span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="15" style="margin-bottom: 10px; height: 84px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="15">                     </span>
                                            </p>
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
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="16" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>16</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="16"><span>    ,           </span></span>
                                    </p>
                                    <p class="verse_text" data-verse="17" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>17</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="17"><span>            </span></span>
                                    </p>
                                    <p class="verse_text" data-verse="18" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>18</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="18"><span>               </span></span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="16" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="16">    ,           </span>
                                            </p>
                                        </div>
                                        <div class="verse_block flex_chunk" data-verse="17" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="17">            </span>
                                            </p>
                                        </div>
                                        <div class="verse_block flex_chunk" data-verse="18" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="18">               </span>
                                            </p>
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
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="19" style="height: 84px;">
                                        <strong class="ltr">
                                            <sup>19</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="19"><span>       , "      " "         "</span></span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="19" style="margin-bottom: 10px; height: 84px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="19">       , "      " "         "</span>
                                            </p>
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
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="20" style="height: 84px;">
                                        <strong class="ltr">
                                            <sup>20</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="20"><span>                          </span></span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="20" style="margin-bottom: 10px; height: 84px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="20">                          </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex_right">
                                    <div class="notes_tools">
                                        <div class="comments_number flex_commn_number ">
                                        </div>
                                        <span class="editComment mdi mdi-lead-pencil" data="2:10" title="<?php echo __("write_note_title", [""])?>"></span>

                                        <div class="comments">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chunk_divider"></div>
                        <div class="row chunk_block no_autosize">
                            <div class="flex_container">
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="21" style="height: 84px;">
                                        <strong class="ltr">
                                            <sup>21</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="21"><span>                       </span></span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="21" style="margin-bottom: 10px; height: 84px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="21">                       </span>
                                            </p>
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
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="22" style="height: 84px;">
                                        <strong class="ltr">
                                            <sup>22</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="22"><span>        ,  ,                 </span></span>
                                    </p>
                                    <p class="verse_text" data-verse="23" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>23</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="23"><span>            </span></span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="22" style="margin-bottom: 10px; height: 84px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="22">        ,  ,                 </span>
                                            </p>
                                        </div>
                                        <div class="verse_block flex_chunk" data-verse="23" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="23">            </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex_right">
                                    <div class="notes_tools">
                                        <div class="comments_number flex_commn_number ">
                                        </div>
                                        <span class="editComment mdi mdi-lead-pencil" data="2:12" title="<?php echo __("write_note_title", [""])?>"></span>

                                        <div class="comments">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chunk_divider"></div>
                        <div class="row chunk_block no_autosize">
                            <div class="flex_container">
                                <div class="chunk_verses flex_left font_sgn-US-symbunot" dir="ltr">
                                    <p class="verse_text" data-verse="24" style="height: 84px;">
                                        <strong class="ltr">
                                            <sup>24</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="24"><span>                  </span></span>
                                    </p>
                                    <p class="verse_text" data-verse="25" style="height: 84px;">
                                        <strong class="ltr">
                                            <sup>25</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="25"><span>                   </span></span>
                                    </p>
                                    <p class="verse_text" data-verse="26" style="height: 47px;">
                                        <strong class="ltr">
                                            <sup>26</sup>
                                        </strong>
                                        <span class="orig_text" data-orig-verse="26"><span>             </span></span>
                                    </p>
                                </div>
                                <div class="flex_middle editor_area font_sgn-US-symbunot" dir="ltr">
                                    <div class="vnote">
                                        <div class="verse_block flex_chunk" data-verse="24" style="margin-bottom: 10px; height: 84px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="24">                  </span>
                                            </p>
                                        </div>
                                        <div class="verse_block flex_chunk" data-verse="25" style="margin-bottom: 10px; height: 84px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="25">                   </span>
                                            </p>
                                        </div>
                                        <div class="verse_block flex_chunk" data-verse="26" style="margin-bottom: 10px; height: 47px;">
                                            <p>
                                                <span class="targetVerse" data-orig-verse="26">             </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex_right">
                                    <div class="notes_tools">
                                        <div class="comments_number flex_commn_number ">
                                        </div>
                                        <span class="editComment mdi mdi-lead-pencil" data="2:13" title="<?php echo __("write_note_title", [""])?>"></span>

                                        <div class="comments">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chunk_divider"></div>
                    </div>
                </div>

                <div id="source_scripture_content" class="my_content">
                    <div class="chunk_block">
                        <div class="chunk_verses" dir="ltr">
                            <div> <strong class="ltr"> <sup>1</sup> </strong> <div class="kwverse_2_0_1"><b data="0">You</b> therefore, my child, be <b data="0">strengthened</b> in the <b data="0">grace</b> that is in <b data="0">Christ Jesus</b>.</div></div>
                            <div> <strong class="ltr"> <sup>2</sup> </strong> <div class="kwverse_2_0_2">And the things you heard from me among many witnesses, entrust them to faithful people who will be able to teach others also.</div></div>
                            <div> <strong class="ltr"> <sup>3</sup> </strong> <div class="kwverse_2_0_3"><b data="0">Suffer</b> hardship with me, as a good soldier of Christ Jesus.</div></div>
                        </div>
                    </div>
                    <div class="chunk_block">
                        <div class="chunk_verses" dir="ltr">
                            <div> <strong class="ltr"> <sup>4</sup> </strong> <div class="kwverse_2_0_4">No soldier serves while entangled in the affairs of this life, so that he may please his superior officer.</div></div>
                            <div> <strong class="ltr"> <sup>5</sup> </strong> <div class="kwverse_2_0_5">Also, if someone competes as an athlete, he is not crowned unless he competes by the rules.</div></div>
                            <div> <strong class="ltr"> <sup>6</sup> </strong> <div class="kwverse_2_0_6">It is necessary that the hardworking farmer receive his share of the crops first.</div></div>
                            <div> <strong class="ltr"> <sup>7</sup> </strong> <div class="kwverse_2_0_7">Think about what I am saying, for the <b data="0">Lord</b> will give you understanding in everything.</div></div>
                        </div>
                    </div>
                    <div class="chunk_block">
                        <div class="chunk_verses" dir="ltr">
                            <div> <strong class="ltr"> <sup>8</sup> </strong> <div class="kwverse_2_0_8">Remember Jesus Christ, from David's seed, who was raised from the dead ones. This is according to my gospel message,</div></div>
                            <div> <strong class="ltr"> <sup>9</sup> </strong> <div class="kwverse_2_0_9">for which I am suffering to the point of being chained as a criminal. But the word of God is not chained.</div></div>
                            <div> <strong class="ltr"> <sup>10</sup> </strong> <div class="kwverse_2_0_10">Therefore I endure all things for those who are chosen, so that they also may obtain the salvation that is in Christ Jesus, with eternal glory.</div></div>
                        </div>
                    </div>
                    <div class="chunk_block">
                        <div class="chunk_verses" dir="ltr">
                            <div> <strong class="ltr"> <sup>11</sup> </strong> <div class="kwverse_2_0_11">This saying is trustworthy: "If we have died with him, we will also live with him.</div></div>
                            <div> <strong class="ltr"> <sup>12</sup> </strong> <div class="kwverse_2_0_12">If we endure, we will also reign with him. If we deny him, he also will deny us.</div></div>
                            <div> <strong class="ltr"> <sup>13</sup> </strong> <div class="kwverse_2_0_13">if we are unfaithful, he remains faithful, for he cannot deny himself."</div></div>
                            <div> <strong class="ltr"> <sup>14</sup> </strong> <div class="kwverse_2_0_14">Keep reminding them of these things. Warn them before God not to quarrel about words. Because of this there is nothing useful. Because of this there is destruction for those who listen.</div></div>
                        </div>
                    </div>
                    <div class="chunk_block">
                        <div class="chunk_verses" dir="ltr">
                            <div> <strong class="ltr"> <sup>15</sup> </strong> <div class="kwverse_2_0_15">Do your best to present yourself to God as one approved, a worker who has no reason to be ashamed, who accurately teaches the word of truth.</div></div>
                            <div> <strong class="ltr"> <sup>16</sup> </strong> <div class="kwverse_2_0_16">Avoid profane talk, which leads to more and more godlessness.</div></div>
                            <div> <strong class="ltr"> <sup>17</sup> </strong> <div class="kwverse_2_0_17">Their talk will spread like gangrene. Among whom are Hymenaeus and Philetus.</div></div>
                        </div>
                    </div>
                    <div class="chunk_block">
                        <div class="chunk_verses" dir="ltr">
                            <div> <strong class="ltr"> <sup>18</sup> </strong> <div class="kwverse_2_0_18">These are men who have missed the truth. They say that the resurrection has already happened. They overturn the faith of some.</div></div>
                            <div> <strong class="ltr"> <sup>19</sup> </strong> <div class="kwverse_2_0_19">However, the firm foundation of God stands. It has this inscription: "The Lord knows those who are his" and "Everyone who names the name of the Lord must depart from unrighteousness."</div></div>
                            <div> <strong class="ltr"> <sup>20</sup> </strong> <div class="kwverse_2_0_20">In a wealthy home, there are not only containers of gold and silver. There are also containers of wood and clay. Some of these are for honorable use, and some for dishonorable.</div></div>
                        </div>
                    </div>
                    <div class="chunk_block">
                        <div class="chunk_verses" dir="ltr">
                            <div> <strong class="ltr"> <sup>21</sup> </strong> <div class="kwverse_2_0_21">If someone cleans himself from dishonorable use, he is an honorable container. He is set apart, useful to the Master, and prepared for every good work.</div></div>
                            <div> <strong class="ltr"> <sup>22</sup> </strong> <div class="kwverse_2_0_22">Flee youthful lusts. Pursue righteousness, faith, love, and peace with those who call on the Lord out of a clean heart.</div></div>
                            <div> <strong class="ltr"> <sup>23</sup> </strong> <div class="kwverse_2_0_23">But refuse foolish and ignorant questions. You know that they give birth to arguments.</div></div>
                        </div>
                    </div>
                    <div class="chunk_block">
                        <div class="chunk_verses" dir="ltr">
                            <div> <strong class="ltr"> <sup>24</sup> </strong> <div class="kwverse_2_0_24">The Lord's servant must not quarrel. Instead he must be gentle toward all, able to teach, and patient.</div></div>
                            <div> <strong class="ltr"> <sup>25</sup> </strong> <div class="kwverse_2_0_25">He must in meekness educate those who oppose him. God may perhaps give them repentance for the knowledge of the truth.</div></div>
                            <div> <strong class="ltr"> <sup>26</sup> </strong> <div class="kwverse_2_0_26">They may become sober again and leave the devil's trap, after they have been captured by him for his will.</div></div>
                        </div>
                    </div>
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
                </form>
                <div class="step_right"></div>
            </div>
        </div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps is_checker_page_help isPeer">
            <div class="help_name_steps"><span><?php echo __("step_num", ["step_number" => 3])?>:</span> <?php echo __(EventCheckSteps::PEER_REVIEW)?></div>
            <div class="help_descr_steps">
                <ul><?php echo __("peer-review-l2_chk_desc", ["step" => __($data["next_step"])])?></ul>
                <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
            </div>
        </div>

        <div class="event_info is_checker_page_help isPeer">
            <div class="participant_info">
                <div class="participant_name">
                    <span><?php echo __("your_checker") ?>:</span>
                    <span class="checker_name_span">
                                Genry M.
                            </span>
                </div>
                <div class="additional_info">
                    <a href="/events/demo-revision/information/sun"><?php echo __("event_info") ?></a>
                </div>
            </div>
        </div>

        <div class="tr_tools">
            <button class="btn btn-primary ttools" data-tool="tn"><?php echo __("show_notes") ?></button>
            <button class="btn btn-primary ttools" data-tool="tq"><?php echo __("show_questions") ?></button>
            <button class="btn btn-primary ttools" data-tool="tw"><?php echo __("show_keywords") ?></button>
            <button class="btn btn-warning ttools" data-tool="saildict"><?php echo __("show_dictionary") ?></button>
        </div>

        <div class="checker_view">
            <a href="/events/demo-revision/peer_review"><?php echo __("checker_other_view", [1]) ?></a>
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
            <h3><?php echo __(EventCheckSteps::PEER_REVIEW)?></h3>
            <ul><?php echo __("peer-review-l2_chk_desc", ["step" => __($data["next_step"])])?></ul>
        </div>
    </div>
</div>

<!-- Data for tools -->
<input type="hidden" id="bookCode" value="<?php echo $data["bookCode"] ?>">
<input type="hidden" id="chapter" value="<?php echo $data["chapter"] ?>">
<input type="hidden" id="tn_lang" value="<?php echo $data["tn_lang"] ?>">
<input type="hidden" id="tq_lang" value="<?php echo $data["tq_lang"] ?>">
<input type="hidden" id="tw_lang" value="<?php echo $data["tw_lang"] ?>">
<input type="hidden" id="totalVerses" value="<?php echo $data["totalVerses"] ?>">
<input type="hidden" id="targetLang" value="<?php echo $data["targetLang"] ?>">

<script type="text/javascript" src="<?php echo template_url("js/diff_match_patch.js?2")?>"></script>
<script type="text/javascript" src="<?php echo template_url("js/diff.js?7")?>"></script>
<script>
    isChecker = true;
    disableHighlight = true;
    $(document).ready(function () {
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
                var p = $(".verse_block[data-verse="+verse+"]");

                if(p.length > 0) {
                    var t_height = p.outerHeight();
                    p.outerHeight(Math.max(p_height, t_height));
                    $(this).outerHeight(Math.max(p_height, t_height));
                }
            });
        }

        $(".orig_text").each(function() {
            var verse = $(this).data("orig-verse");
            var chkText;
            chkVersion = $("span.targetVerse[data-orig-verse='"+verse+"']");
            chkText = chkVersion.text();

            diff_plain($(this).text(), chkText, $(this));
        });

        $(".sun_mode input").change(function () {
            var active = $(this).prop('checked');

            if (active) {
                $(".flex_left, .flex_middle").removeClass("font_backsun");
                $(".flex_left, .flex_middle").addClass("font_sgn-US-symbunot");
            } else {
                $(".flex_left, .flex_middle").removeClass("font_sgn-US-symbunot");
                $(".flex_left, .flex_middle").addClass("font_backsun");
            }

            $(".verse_text").css("height", "initial");
            equal_verses_height();
        });

        $("#next_step").click(function (e) {
            renderConfirmPopup(Language.checkerConfirmTitle, Language.checkerConfirm,
                function () {
                    window.location.href = '/events/demo-revision/keyword_check/sun';
                },
                function () {
                    $("#confirm_step").prop("checked", false);
                    $("#next_step").prop("disabled", true);
                    $( this ).dialog("close");
                },
                function () {
                    $("#confirm_step").prop("checked", false);
                    $("#next_step").prop("disabled", true);
                    $( this ).dialog("close");
                });

            e.preventDefault();
            return false;
        });

        $(".ttools_panel .word_def").each(function() {
            let html = convertRcLinks($(this).html());
            $(this).html(html);
        });
    });
</script>
