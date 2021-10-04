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
            <div class="demo_title"><?php echo __("demo") . " (".__("vsail_l3").")" ?></div>
            <div><?php echo __("step_num", ["step_number" => 2]) . ": " . __("peer-edit-l3_full")?></div>
        </div>
    </div>

    <div class="">
        <div class="main_content">
            <div class="main_content_text">
                <h4>SUN - <?php echo __("sun") ?> - <?php echo __("new_test") ?> - <span class="book_name">Mathew 17:1-27</span></h4>

                <ul class="nav nav-tabs">
                    <li role="presentation" id="target_scripture" class="my_tab">
                        <a href="#"><?php echo __("target_text") ?></a>
                    </li>
                    <li role="presentation" id="source_scripture" class="my_tab">
                        <a href="#"><?php echo __("source_text") ?></a>
                    </li>
                </ul>

                <div id="target_scripture_content" class="my_content shown">
                    <div class="flex_container chunk_block no_autosize">
                        <div class="flex_left sun_content">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="1">
                                    <span class="verse_number_l2">1</span>
                                    <textarea name="chunks[0][1]" class="peer_verse_ta textarea" data-orig-verse="1" style="overflow-x: hidden; overflow-wrap: break-word; height: 106px;">     ,             </textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="2">
                                    <span class="verse_number_l2">2</span>
                                    <textarea name="chunks[0][2]" class="peer_verse_ta textarea" data-orig-verse="2" style="overflow-x: hidden; overflow-wrap: break-word; height: 81px;">                </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="flex_middle font_backsun">
                            <div class="verse_block sun_verse_block" data-verse="1" style="height: 106px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="1">     ,             </span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="2" style="height: 81px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="2">                </span>
                                </p>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number tncomml3_alt  hasComment">3</div>
                            <span class="editComment mdi mdi-lead-pencil"
                                  data="0:0"
                                  title="<?php echo __("write_note_title", [""])?>"></span>

                            <div class="comments">
                                <div class="my_comment">A note from level 3 checker</div>
                                <div class="other_comments"><span>Henry M. - L3: </span>A note from level 3 peer checker</div>
                                <div class="other_comments"><span>Marge S. - L2: </span>A note from level 2 checker</div>
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="flex_container chunk_block no_autosize">
                        <div class="flex_left sun_content">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="3">
                                    <span class="verse_number_l2">3</span>
                                    <textarea name="chunks[1][3]" class="peer_verse_ta textarea" data-orig-verse="3" style="overflow-x: hidden; overflow-wrap: break-word; height: 81px;"> ,  ,             </textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="4">
                                    <span class="verse_number_l2">4</span>
                                    <textarea name="chunks[1][4]" class="peer_verse_ta textarea" data-orig-verse="4" style="overflow-x: hidden; overflow-wrap: break-word; height: 111px;">    ”  ,        ,                 </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="flex_middle font_backsun">
                            <div class="verse_block sun_verse_block" data-verse="3" style="height: 81px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="3"> ,  ,             </span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="4" style="height: 111px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="4">    ”  ,        ,                 </span>
                                </p>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number ">
                            </div>
                            <span class="editComment mdi mdi-lead-pencil" data="17:1" title="<?php echo __("write_note_title", [""])?>"></span>

                            <div class="comments">
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="flex_container chunk_block no_autosize">
                        <div class="flex_left sun_content">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="5">
                                    <span class="verse_number_l2">5</span>
                                    <textarea name="chunks[2][5]" class="peer_verse_ta textarea" data-orig-verse="5" style="overflow-x: hidden; overflow-wrap: break-word; height: 106px;">   ,           ”              </textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="6">
                                    <span class="verse_number_l2">6</span>
                                    <textarea name="chunks[2][6]" class="peer_verse_ta textarea" data-orig-verse="6" style="overflow-x: hidden; overflow-wrap: break-word; height: 81px;">   ,         </textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="7">
                                    <span class="verse_number_l2">7</span>
                                    <textarea name="chunks[2][7]" class="peer_verse_ta textarea" data-orig-verse="7" style="overflow-x: hidden; overflow-wrap: break-word; height: 81px;">        ”      ”</textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="8">
                                    <span class="verse_number_l2">8</span>
                                    <textarea name="chunks[2][8]" class="peer_verse_ta textarea" data-orig-verse="8" style="overflow-x: hidden; overflow-wrap: break-word; height: 80px;">       </textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="9">
                                    <span class="verse_number_l2">9</span>
                                    <textarea name="chunks[2][9]" class="peer_verse_ta textarea" data-orig-verse="9" style="overflow-x: hidden; overflow-wrap: break-word; height: 106px;">         ”       </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="flex_middle font_backsun">
                            <div class="verse_block sun_verse_block" data-verse="5" style="height: 106px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="5">   ,           ”              </span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="6" style="height: 81px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="6">   ,         </span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="7" style="height: 81px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="7">        ”      ”</span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="8" style="height: 80px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="8">       </span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="9" style="height: 106px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="9">         ”       </span>
                                </p>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number ">
                            </div>
                            <span class="editComment mdi mdi-lead-pencil" data="17:2" title="<?php echo __("write_note_title", [""])?>"></span>

                            <div class="comments">
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="flex_container chunk_block no_autosize">
                        <div class="flex_left sun_content">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="10">
                                    <span class="verse_number_l2">10</span>
                                    <textarea name="chunks[3][10]" class="peer_verse_ta textarea" data-orig-verse="10" style="overflow-x: hidden; overflow-wrap: break-word; height: 81px;">   ”       ? “</textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="11">
                                    <span class="verse_number_l2">11</span>
                                    <textarea name="chunks[3][11]" class="peer_verse_ta textarea" data-orig-verse="11" style="overflow-x: hidden; overflow-wrap: break-word; height: 80px;">   ”       </textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="12">
                                    <span class="verse_number_l2">12</span>
                                    <textarea name="chunks[3][12]" class="peer_verse_ta textarea" data-orig-verse="12" style="overflow-x: hidden; overflow-wrap: break-word; height: 106px;">   ,                  </textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="13">
                                    <span class="verse_number_l2">13</span>
                                    <textarea name="chunks[3][13]" class="peer_verse_ta textarea" data-orig-verse="13" style="overflow-x: hidden; overflow-wrap: break-word; height: 81px;">       </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="flex_middle font_backsun">
                            <div class="verse_block sun_verse_block" data-verse="10" style="height: 81px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="10">   ”       ? “</span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="11" style="height: 80px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="11">   ”       </span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="12" style="height: 106px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="12">   ,                  </span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="13" style="height: 81px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="13">       </span>
                                </p>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number ">
                            </div>
                            <span class="editComment mdi mdi-lead-pencil" data="17:3" title="<?php echo __("write_note_title", [""])?>"></span>

                            <div class="comments">
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="flex_container chunk_block no_autosize">
                        <div class="flex_left sun_content">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="14">
                                    <span class="verse_number_l2">14</span>
                                    <textarea name="chunks[4][14]" class="peer_verse_ta textarea" data-orig-verse="14" style="overflow-x: hidden; overflow-wrap: break-word; height: 106px;">                   </textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="15">
                                    <span class="verse_number_l2">15</span>
                                    <textarea name="chunks[4][15]" class="peer_verse_ta textarea" data-orig-verse="15" style="overflow-x: hidden; overflow-wrap: break-word; height: 81px;">”  ,                    </textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="16">
                                    <span class="verse_number_l2">16</span>
                                    <textarea name="chunks[4][16]" class="peer_verse_ta textarea" data-orig-verse="16" style="overflow-x: hidden; overflow-wrap: break-word; height: 81px;">             ”</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="flex_middle font_backsun">
                            <div class="verse_block sun_verse_block" data-verse="14" style="height: 106px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="14">                   </span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="15" style="height: 81px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="15">”  ,                    </span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="16" style="height: 81px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="16">             ”</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number ">
                            </div>
                            <span class="editComment mdi mdi-lead-pencil" data="17:4" title="<?php echo __("write_note_title", [""])?>"></span>

                            <div class="comments">
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="flex_container chunk_block no_autosize">
                        <div class="flex_left sun_content">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="17">
                                    <span class="verse_number_l2">17</span>
                                    <textarea name="chunks[5][17]" class="peer_verse_ta textarea" data-orig-verse="17" style="overflow-x: hidden; overflow-wrap: break-word; height: 106px;">   ”             ?        ”</textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="18">
                                    <span class="verse_number_l2">18</span>
                                    <textarea name="chunks[5][18]" class="peer_verse_ta textarea" data-orig-verse="18" style="overflow-x: hidden; overflow-wrap: break-word; height: 81px;">              </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="flex_middle font_backsun">
                            <div class="verse_block sun_verse_block" data-verse="17" style="height: 106px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="17">   ”             ?        ”</span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="18" style="height: 81px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="18">              </span>
                                </p>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number ">
                            </div>
                            <span class="editComment mdi mdi-lead-pencil" data="17:5" title="<?php echo __("write_note_title", [""])?>"></span>

                            <div class="comments">
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="flex_container chunk_block no_autosize">
                        <div class="flex_left sun_content">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="19">
                                    <span class="verse_number_l2">19</span>
                                    <textarea name="chunks[6][19]" class="peer_verse_ta textarea" data-orig-verse="19" style="overflow-x: hidden; overflow-wrap: break-word; height: 81px;">         ”        ? “</textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="20">
                                    <span class="verse_number_l2">20</span>
                                    <textarea name="chunks[6][20]" class="peer_verse_ta textarea" data-orig-verse="20" style="overflow-x: hidden; overflow-wrap: break-word; height: 131px;">  ”                       ’       ’         ”</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="flex_middle font_backsun">
                            <div class="verse_block sun_verse_block" data-verse="19" style="height: 81px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="19">         ”        ? “</span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="20" style="height: 131px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="20">  ”                       ’       ’         ”</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number ">
                            </div>
                            <span class="editComment mdi mdi-lead-pencil" data="17:6" title="<?php echo __("write_note_title", [""])?>"></span>

                            <div class="comments">
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="flex_container chunk_block no_autosize">
                        <div class="flex_left sun_content">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="21">
                                    <span class="verse_number_l2">21</span>
                                    <textarea name="chunks[7][21]" class="peer_verse_ta textarea" data-orig-verse="21" style="overflow-x: hidden; overflow-wrap: break-word; height: 80px;">Verse removed in ULB.</textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="22">
                                    <span class="verse_number_l2">22</span>
                                    <textarea name="chunks[7][22]" class="peer_verse_ta textarea" data-orig-verse="22" style="overflow-x: hidden; overflow-wrap: break-word; height: 106px;">           ”     </textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="23">
                                    <span class="verse_number_l2">23</span>
                                    <textarea name="chunks[7][23]" class="peer_verse_ta textarea" data-orig-verse="23" style="overflow-x: hidden; overflow-wrap: break-word; height: 81px;">          ”    </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="flex_middle font_backsun">
                            <div class="verse_block sun_verse_block" data-verse="21" style="height: 80px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="21">Verse removed in ULB.</span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="22" style="height: 106px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="22">           ”     </span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="23" style="height: 81px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="23">          ”    </span>
                                </p>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number ">
                            </div>
                            <span class="editComment mdi mdi-lead-pencil" data="17:7" title="<?php echo __("write_note_title", [""])?>"></span>

                            <div class="comments">
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="flex_container chunk_block no_autosize">
                        <div class="flex_left sun_content">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="24">
                                    <span class="verse_number_l2">24</span>
                                    <textarea name="chunks[8][24]" class="peer_verse_ta textarea" data-orig-verse="24" style="overflow-x: hidden; overflow-wrap: break-word; height: 106px;">                ? “</textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="25">
                                    <span class="verse_number_l2">25</span>
                                    <textarea name="chunks[8][25]" class="peer_verse_ta textarea" data-orig-verse="25" style="overflow-x: hidden; overflow-wrap: break-word; height: 131px;">  ”  ”     ,      "    ?      ?</textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="26">
                                    <span class="verse_number_l2">26</span>
                                    <textarea name="chunks[8][26]" class="peer_verse_ta textarea" data-orig-verse="26" style="overflow-x: hidden; overflow-wrap: break-word; height: 81px;">  , “  ”    ,    </textarea>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="27">
                                    <span class="verse_number_l2">27</span>
                                    <textarea name="chunks[8][27]" class="peer_verse_ta textarea" data-orig-verse="27" style="overflow-x: hidden; overflow-wrap: break-word; height: 156px;">                                       ”</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="flex_middle font_backsun">
                            <div class="verse_block sun_verse_block" data-verse="24" style="height: 106px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="24">                ? “</span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="25" style="height: 131px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="25">  ”  ”     ,      "    ?      ?</span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="26" style="height: 81px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="26">  , “  ”    ,    </span>
                                </p>
                            </div>
                            <div class="verse_block sun_verse_block" data-verse="27" style="height: 156px;">
                                <p>
                                    <span class="targetVerse" data-orig-verse="27">                                       ”</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number ">
                            </div>
                            <span class="editComment mdi mdi-lead-pencil" data="17:8" title="<?php echo __("write_note_title", [""])?>"></span>

                            <div class="comments">
                            </div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                </div>

                <div id="source_scripture_content" class="my_content">
                    <div class="note_chunk l3">
                        <div class="flex_container">
                            <div class="flex_left" dir="ltr">
                                <p>
                                    <strong class="ltr"><sup>1</sup></strong>
                                    <span>Six days later Jesus took with him Peter, James, and John his brother, and brought them up a high mountain by themselves.</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>2</sup></strong>
                                    <span>He was transfigured before them. His face shone like the sun, and his garments became as brilliant as the light.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="note_chunk l3">
                        <div class="flex_container">
                            <div class="flex_left" dir="ltr">
                                <p>
                                    <strong class="ltr"><sup>3</sup></strong>
                                    <span>Behold, there appeared to them Moses and Elijah talking with him.</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>4</sup></strong>
                                    <span>Peter answered and said to Jesus, "Lord, it is good for us to be here. If you desire, I will make here three shelters—one for you, and one for Moses, and one for Elijah."</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="note_chunk l3">
                        <div class="flex_container">
                            <div class="flex_left" dir="ltr">
                                <p>
                                    <strong class="ltr"><sup>5</sup></strong>
                                    <span>While he was still speaking, behold, a bright cloud overshadowed them, and behold, there was a voice out of the cloud, saying, "This is my beloved Son, in whom I am well pleased. Listen to him."</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>6</sup></strong>
                                    <span>When the disciples heard it, they fell on their face and were very afraid.</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>7</sup></strong>
                                    <span>Then Jesus came and touched them and said, "Get up and do not be afraid."</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>8</sup></strong>
                                    <span>Then they looked up but saw no one except Jesus only.</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>9</sup></strong>
                                    <span>As they were coming down the mountain, Jesus commanded them, saying, "Report this vision to no one until the Son of Man has risen from the dead."</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="note_chunk l3">
                        <div class="flex_container">
                            <div class="flex_left" dir="ltr">
                                <p>
                                    <strong class="ltr"><sup>10</sup></strong>
                                    <span>His disciples asked him, saying, "Why then do the scribes say that Elijah must come first?"</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>11</sup></strong>
                                    <span>Jesus answered and said, "Elijah will indeed come and restore all things.</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>12</sup></strong>
                                    <span>But I tell you, Elijah has already come, but they did not recognize him. Instead, they did whatever they wanted to him. In the same way, the Son of Man will also suffer at their hands."</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>13</sup></strong>
                                    <span>Then the disciples understood that he was speaking to them about John the Baptist.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="note_chunk l3">
                        <div class="flex_container">
                            <div class="flex_left" dir="ltr">
                                <p>
                                    <strong class="ltr"><sup>14</sup></strong>
                                    <span>When they had come to the crowd, a man came to him, knelt before him, and said,</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>15</sup></strong>
                                    <span>"Lord, have mercy on my son, for he is epileptic and suffers severely. For he often falls into the fire or the water.</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>16</sup></strong>
                                    <span>I brought him to your disciples, but they could not cure him."</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="note_chunk l3">
                        <div class="flex_container">
                            <div class="flex_left" dir="ltr">
                                <p>
                                    <strong class="ltr"><sup>17</sup></strong>
                                    <span>Jesus answered and said, "Unbelieving and corrupt generation, how long will I have to stay with you? How long must I bear with you? Bring him here to me."</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>18</sup></strong>
                                    <span>Jesus rebuked the demon, and it came out of him, and the boy was healed from that hour.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="note_chunk l3">
                        <div class="flex_container">
                            <div class="flex_left" dir="ltr">
                                <p>
                                    <strong class="ltr"><sup>19</sup></strong>
                                    <span>Then the disciples came to Jesus privately and said, "Why could we not cast it out?"</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>20</sup></strong>
                                    <span>Jesus said to them, "Because of your small faith. For I truly say to you, if you have faith even as small as a grain of mustard seed, you can say to this mountain, 'Move from here to there,' and it will move, and nothing will be impossible for you.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="note_chunk l3">
                        <div class="flex_container">
                            <div class="flex_left" dir="ltr">
                                <p>
                                    <strong class="ltr"><sup>21</sup></strong>
                                    <span> <span data-toggle="tooltip" data-placement="auto auto" title="" class="booknote mdi mdi-bookmark" data-original-title="The best ancient copies omit v. 21,  But this kind of demon does not go out except with prayer and fasting  . "></span> </span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>22</sup></strong>
                                    <span>While they stayed in Galilee, Jesus said to his disciples, "The Son of Man will be delivered into the hands of people,</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>23</sup></strong>
                                    <span>and they will kill him, and the third day he will be raised up." The disciples became very upset.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="note_chunk l3">
                        <div class="flex_container">
                            <div class="flex_left" dir="ltr">
                                <p>
                                    <strong class="ltr"><sup>24</sup></strong>
                                    <span>When they had come to Capernaum, the men who collected the half-shekel tax came to Peter and said, "Does not your teacher pay the half-shekel tax?"</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>25</sup></strong>
                                    <span>He said, "Yes." When Peter came into the house, Jesus spoke to him first and said, "What do you think, Simon? From whom do the kings of the earth collect tolls or taxes? From their sons or from others?"</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>26</sup></strong>
                                    <span>When he said, "From others," Jesus said to him, "Then the sons are free."</span>
                                </p>
                                <p>
                                    <strong class="ltr"><sup>27</sup></strong>
                                    <span>But so that we do not cause the tax collectors to sin, go to the sea, throw in a hook, and draw in the fish that comes up first. When you have opened its mouth, you will find a shekel. Take it and give it to the tax collectors for me and you."</span>
                                </p>
                            </div>
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
                    <img src="<?php echo template_url("img/saving.gif") ?>" class="unsaved_alert">
                </form>
                <div class="step_right"></div>
            </div>
            <div class="step_right alt"><?php echo __("step_num", ["step_number" => 2])?></div>
        </div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps is_checker_page_help">
            <div class="help_name_steps">
                <span><?php echo __("step_num", ["step_number" => 2])?>: </span>
                <?php echo __("peer-edit-l3")?>
            </div>
            <div class="help_descr_steps">
                <ul><?php echo __("peer-edit-l3_desc", ["step" => __($data["next_step"])])?></ul>
                <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
            </div>
        </div>

        <div class="event_info is_checker_page_help">
            <div class="participant_info">
                <div class="participant_name">
                    <span><?php echo __("your_checker") ?>:</span>
                    <span class="checker_name_span">
                                Marge S.
                            </span>
                </div>
                <div class="additional_info">
                    <a href="/events/demo-sun-l3/information"><?php echo __("event_info") ?></a>
                </div>
            </div>
        </div>

        <div class="tr_tools">
            <button class="btn btn-warning ttools" data-tool="saildict"><?php echo __("show_dictionary") ?></button>
            <button class="btn btn-primary ttools" data-tool="tn"><?php echo __("show_notes") ?></button>
            <button class="btn btn-primary ttools" data-tool="tw"><?php echo __("show_keywords") ?></button>
        </div>

        <div class="checker_view">
            <a href="/events/demo-sun-l3/peer_edit_l3_checker"><?php echo __("checker_other_view", [2]) ?></a>
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

        <div class="tutorial_content">
            <h3><?php echo __("peer-edit-l3_full")?></h3>
            <ul><?php echo __("peer-edit-l3_desc", ["step" => __($data["next_step"])])?></ul>
        </div>
    </div>
</div>

<script>
    isChecker = true;
    isLevel3 = true;
    $(document).ready(function () {
        $("#next_step").click(function (e) {
            e.preventDefault();

            if(!hasChangesOnPage) window.location.href = '/events/demo-sun-l3/pray';

            return false;
        });

        $(".my_tab").click(function () {
            equal_verses_height();
            return false;
        });

        setTimeout(function() {
            equal_verses_height();
        }, 100);

        function equal_verses_height() {
            $(".flex_middle .verse_block").each(function() {
                const verse = $(this).data("verse");
                const p_height = $(this).outerHeight();
                const neighbor = $(".flex_left .verse_block[data-verse=" + verse + "] textarea");

                if(neighbor.length > 0) {
                    let n_height = neighbor.outerHeight();
                    neighbor.outerHeight(Math.max(p_height, n_height));
                    $(this).outerHeight(Math.max(p_height, n_height));
                }
            });
        }

        $(".verse_block textarea").on("keyup paste", function() {
            const verse = $(this).parent(".verse_block").data("verse");
            const neighbor = $(".flex_middle .verse_block[data-verse=" + verse + "]");
            neighbor.text($(this).val());
            equal_verses_height();
        })

        $(".ttools_panel .word_def").each(function() {
            let html = convertRcLinks($(this).html());
            $(this).html(html);
        });
    });
</script>

<input type="hidden" id="bookCode" value="<?php echo $data["bookCode"] ?>">
<input type="hidden" id="chapter" value="<?php echo $data["currentChapter"] ?>">
<input type="hidden" id="tn_lang" value="<?php echo $data["tnLangID"] ?>">
<input type="hidden" id="tw_lang" value="<?php echo $data["twLangID"] ?>">
<input type="hidden" id="totalVerses" value="<?php echo $data["totalVerses"] ?>">
<input type="hidden" id="targetLang" value="<?php echo $data["targetLang"] ?>">
