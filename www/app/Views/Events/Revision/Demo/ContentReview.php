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
            <div class="demo_title"><?php echo __("demo") . " (".__("revision_events").")" ?></div>
            <div><?php echo __("step_num", ["step_number" => 5]) . ": " . __(EventCheckSteps::CONTENT_REVIEW)?></div>
        </div>
    </div>

    <div class="">
        <div class="main_content">
            <div class="main_content_text">
                <h4>Papuan Malay - <?php echo __("ulb") ?> - <?php echo __("new_test") ?> - <span class="book_name">2 Timothy 2:1-26</span></h4>

                <div class="no_padding">
                    <div class="row chunk_block no_autosize flex_container">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <p class="verse_text" data-verse="1"> <strong class="ltr"> <sup>1</sup> </strong> You therefore, my child, be strengthened in the grace that is in Christ Jesus.</p>
                            <p class="verse_text" data-verse="2"> <strong class="ltr"> <sup>2</sup> </strong> And the things you heard from me among many witnesses, entrust them to faithful people who will be able to teach others also.</p>
                            <p class="verse_text" data-verse="3"> <strong class="ltr"> <sup>3</sup> </strong> Suffer hardship with me, as a good soldier of Christ Jesus.</p>
                        </div>
                        <div class="editor_area flex_middle" dir="ltr">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="1">
                                    <textarea name="chunks[0][1]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Jadi begitu, anakku kuat sudah dengan anugerah di dalam Kristus Yesus. This text has been added by the checker</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="2">
                                    <textarea name="chunks[0][2]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 80px;">Dan banyak hal yang ko dengar dari sa deng saksi yang banyak itu, beri percaya itu sama orang-orang yang setia, supaya dong dapat this is the removed text.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="3">
                                    <textarea name="chunks[0][3]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Mari, tong sama sa dalam penderitaan jadi prajurit Kristus Yesus yang baik.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
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
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize flex_container">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <p class="verse_text" data-verse="4"> <strong class="ltr"> <sup>4</sup> </strong> No soldier serves while entangled in the affairs of this life, so that he may please his superior officer.</p>
                            <p class="verse_text" data-verse="5"> <strong class="ltr"> <sup>5</sup> </strong> Also, if someone competes as an athlete, he is not crowned unless he competes by the rules.</p>
                            <p class="verse_text" data-verse="6"> <strong class="ltr"> <sup>6</sup> </strong> It is necessary that the hardworking farmer receive his share of the crops first.</p>
                            <p class="verse_text" data-verse="7"> <strong class="ltr"> <sup>7</sup> </strong> Think about what I am saying, for the Lord will give you understanding in everything.</p>
                        </div>
                        <div class="editor_area flex_middle" dir="ltr">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="4">
                                    <textarea name="chunks[0][4]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Trada satu orang tentara yang kerja sambil sibuk dengan de pu urusan hidup supaya de bisa buat de pu komandan senang.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="5">
                                    <textarea name="chunks[0][5]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 80px;">Begitu juga dengan atlit , tra akan terima mahkota kalo tra ikut aturan dalam lomba.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="6">
                                    <textarea name="chunks[0][6]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Petani dong yang kerja keras akan terima hasil yang pertama,</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="7">
                                    <textarea name="chunks[0][7]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Ingat apa yang sa bilang, karena Tuhan akan kasi ko pengertian untuk mengerti semua ini,</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
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
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize flex_container">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <p class="verse_text" data-verse="8"> <strong class="ltr"> <sup>8</sup> </strong> Remember Jesus Christ, from David's seed, who was raised from the dead ones. This is according to my gospel message,</p>
                            <p class="verse_text" data-verse="9"> <strong class="ltr"> <sup>9</sup> </strong> for which I am suffering to the point of being chained as a criminal. But the word of God is not chained.</p>
                            <p class="verse_text" data-verse="10"> <strong class="ltr"> <sup>10</sup> </strong> Therefore I endure all things for those who are chosen, so that they also may obtain the salvation that is in Christ Jesus, with eternal glory.</p>
                        </div>
                        <div class="editor_area flex_middle" dir="ltr">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="8">
                                    <textarea name="chunks[0][8]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Ingat: Yesus Kristus, keturunan Daud, su bangkit dari kematian. ini su sesuai dengan pesan Injil yang sa percaya.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="9">
                                    <textarea name="chunks[0][9]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 80px;">Sampe pada titik penderitaan karna diikat rantai seperti kriminal. tapi firman Allah tra diikat deng rantai.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="10">
                                    <textarea name="chunks[0][10]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Jadi sa bertahan untuk orang-orang yang Tuhan pilih, supaya dong dapat keselamatan yang kekal dalam Kristus Yesus, deng kemuliaan yang abadi.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number"></div>
                            <span class="editComment mdi mdi-lead-pencil"
                                  data="0:0"
                                  title="<?php echo __("write_note_title", [""])?>"></span>
                            <div class="comments"></div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize flex_container">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <p class="verse_text" data-verse="11"> <strong class="ltr"> <sup>11</sup> </strong> This saying is trustworthy: "If we have died with him, we will also live with him.</p>
                            <p class="verse_text" data-verse="12"> <strong class="ltr"> <sup>12</sup> </strong> If we endure, we will also reign with him. If we deny him, he also will deny us.</p>
                            <p class="verse_text" data-verse="13"> <strong class="ltr"> <sup>13</sup> </strong> if we are unfaithful, he remains faithful, for he cannot deny himself."</p>
                            <p class="verse_text" data-verse="14"> <strong class="ltr"> <sup>14</sup> </strong> Keep reminding them of these things. Warn them before God not to quarrel about words. Because of this there is nothing useful. Because of this there is destruction for those who listen.</p>
                        </div>
                        <div class="editor_area flex_middle" dir="ltr">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="11">
                                    <textarea name="chunks[0][11]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Apa yang sa bilang ini, bisa dipercaya: kalo tong mau mati untuk Dia, torang juga akan hidup bersama deng Dia.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="12">
                                    <textarea name="chunks[0][12]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 80px;">Apalagi kalo tong bertahan , tong juga akan ditinggikan deng Dia. Klo tong menyangkal Dia, Dia juga akan menyangkal ketong,</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="13">
                                    <textarea name="chunks[0][13]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Klo tong tra setia, De tetap setia karena De tra bisa menyangkal diri.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="14">
                                    <textarea name="chunks[0][14]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Selalu kasi ingat dong di hadapan Allah, supaya dong jangan berdebat tentang Firman karena itu akan bikin kacau orang yang dengar,</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number"></div>
                            <span class="editComment mdi mdi-lead-pencil"
                                  data="0:0"
                                  title="<?php echo __("write_note_title", [""])?>"></span>
                            <div class="comments"></div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize flex_container">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <p class="verse_text" data-verse="15"> <strong class="ltr"> <sup>15</sup> </strong> Do your best to present yourself to God as one approved, a worker who has no reason to be ashamed, who accurately teaches the word of truth.</p>
                            <p class="verse_text" data-verse="16"> <strong class="ltr"> <sup>16</sup> </strong> Avoid profane talk, which leads to more and more godlessness.</p>
                            <p class="verse_text" data-verse="17"> <strong class="ltr"> <sup>17</sup> </strong> Their talk will spread like gangrene. Among whom are Hymenaeus and Philetus.</p>
                        </div>
                        <div class="editor_area flex_middle" dir="ltr">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="15">
                                    <textarea name="chunks[0][15]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Lakukan yang paling baik itu adalah persembahan yang Tuhan terima, jadi pekerja trada alasan untuk dapat kasi malu, yang ajar kebeneran Firman dengan pas.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="16">
                                    <textarea name="chunks[0][16]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 80px;">Hindari omong kosong dan tra bersih yang nanti jadi tra baik.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="17">
                                    <textarea name="chunks[0][17]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Perkataan dong akan menyebar seperti kangker. Diantara dong itu ada Himeneus dan Filetus.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number"></div>
                            <span class="editComment mdi mdi-lead-pencil"
                                  data="0:0"
                                  title="<?php echo __("write_note_title", [""])?>"></span>
                            <div class="comments"></div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize flex_container">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <p class="verse_text" data-verse="18"> <strong class="ltr"> <sup>18</sup> </strong> These are men who have missed the truth. They say that the resurrection has already happened. They overturn the faith of some.</p>
                            <p class="verse_text" data-verse="19"> <strong class="ltr"> <sup>19</sup> </strong> However, the firm foundation of God stands. It has this inscription: "The Lord knows those who are his" and "Everyone who names the name of the Lord must depart from unrighteousness."</p>
                            <p class="verse_text" data-verse="20"> <strong class="ltr"> <sup>20</sup> </strong> In a wealthy home, there are not only containers of gold and silver. There are also containers of wood and clay. Some of these are for honorable use, and some for dishonorable.</p>
                        </div>
                        <div class="editor_area flex_middle" dir="ltr">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="18">
                                    <textarea name="chunks[0][18]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Dong adalah orang-orang yang sudah tidak benar. dong katakan kebangkitan sudah terjadi, dong putar balik iman dari berapa orang tu.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="19">
                                    <textarea name="chunks[0][19]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 80px;">Biar begitu, Allah pu fondasi kuat tetap berdiri. ada piagam dengan tulisan yang bilang" Tuhan kenal dong Dia pu milik." . dan orang yang percaya Tuhan harus kasi tinggal yang tra benar.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="20">
                                    <textarea name="chunks[0][20]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Dalam rumah kaya bukan saja ada emas dan perak tapi juga ada kotak-kotak kayu sama tanah liat. barang itu di pake untuk hal-hal yang terhormat, dan ada juga untuk hal-hal tra terhormat.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number"></div>
                            <span class="editComment mdi mdi-lead-pencil"
                                  data="0:0"
                                  title="<?php echo __("write_note_title", [""])?>"></span>
                            <div class="comments"></div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize flex_container">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <p class="verse_text" data-verse="21"> <strong class="ltr"> <sup>21</sup> </strong> If someone cleans himself from dishonorable use, he is an honorable container. He is set apart, useful to the Master, and prepared for every good work.</p>
                            <p class="verse_text" data-verse="22"> <strong class="ltr"> <sup>22</sup> </strong> Flee youthful lusts. Pursue righteousness, faith, love, and peace with those who call on the Lord out of a clean heart.</p>
                            <p class="verse_text" data-verse="23"> <strong class="ltr"> <sup>23</sup> </strong> But refuse foolish and ignorant questions. You know that they give birth to arguments.</p>
                        </div>
                        <div class="editor_area flex_middle" dir="ltr">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="21">
                                    <textarea name="chunks[0][21]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Jika satu orang kasi bersih de pu diri dari yang tra terhormat, de itu bejana yang terhormat. de dipilih , dipake untuk tuannya, dan de disiapkan untuk semua perbuatan yang baik.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="22">
                                    <textarea name="chunks[0][22]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 80px;">Jauhi sudah dari nafsu anak-anak muda, kejar itu kebenaran, iman, kasih, dan damai, sama-sama deng dong yang panggil Tuhan dengan hati yang bersih.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="23">
                                    <textarea name="chunks[0][23]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Tapi tolak sudah pertanyaan-pertanyaan bodok. Kam tahu itu semua nanti jadi sebab baku tengkar.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number"></div>
                            <span class="editComment mdi mdi-lead-pencil"
                                  data="0:0"
                                  title="<?php echo __("write_note_title", [""])?>"></span>
                            <div class="comments"></div>
                        </div>
                    </div>
                    <div class="chunk_divider"></div>
                    <div class="row chunk_block no_autosize flex_container">
                        <div class="chunk_verses flex_left" dir="ltr">
                            <p class="verse_text" data-verse="24"> <strong class="ltr"> <sup>24</sup> </strong> The Lord's servant must not quarrel. Instead he must be gentle toward all, able to teach, and patient.</p>
                            <p class="verse_text" data-verse="25"> <strong class="ltr"> <sup>25</sup> </strong> He must in meekness educate those who oppose him. God may perhaps give them repentance for the knowledge of the truth.</p>
                            <p class="verse_text" data-verse="26"> <strong class="ltr"> <sup>26</sup> </strong> They may become sober again and leave the devil's trap, after they have been captured by him for his will.</p>
                        </div>
                        <div class="editor_area flex_middle" dir="ltr">
                            <div class="vnote">
                                <div class="verse_block flex_chunk" data-verse="24">
                                    <textarea name="chunks[0][24]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">Orang yang melayani Tuhan tra boleh bertengkar tapi harus lemah lembut pada semua dong, Dong harus pintar mengajar, sabar</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="25">
                                    <textarea name="chunks[0][25]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 80px;">de kasi ajaran deng lemah lembut sama dong yang melawan dia. mungkin Allah kasi kesempatan untuk dong bertobat pada pengetahuan akan kebenaran.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                                <div class="verse_block flex_chunk" data-verse="26">
                                    <textarea name="chunks[0][26]" class="peer_verse_ta textarea" style="overflow: hidden; min-width: 400px; overflow-wrap: break-word; height: 111px;">mungkin dong sadar kembali dan kasi tinggal jerat iblis setelah selama ini dong ditawan untuk ikut perintahnya.</textarea>
                                    <span class="editFootNote mdi mdi-bookmark"
                                          style="margin-top: -5px"
                                          title="<?php echo __("write_footnote_title") ?>"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex_right">
                            <div class="comments_number"></div>
                            <span class="editComment mdi mdi-lead-pencil"
                                  data="0:0"
                                  title="<?php echo __("write_note_title", [""])?>"></span>
                            <div class="comments"></div>
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
                <div class="step_right alt"><?php echo __("step_num", ["step_number" => 5])?></div>
            </div>
        </div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps is_checker_page_help">
            <div class="help_name_steps"><span><?php echo __("step_num", ["step_number" => 5])?>: </span><?php echo __(EventCheckSteps::CONTENT_REVIEW) ?> </div>
            <div class="help_descr_steps">
                <ul><?php echo __("content-review-l2_desc", ["step" => __($data["next_step"])])?></ul>
                <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div>
            </div>
        </div>

        <div class="event_info is_checker_page_help">
            <div class="participant_info">
                <div class="participant_name">
                    <span><?php echo __("your_checker") ?>:</span>
                    <span class="checker_name_span">
                                Dan S.
                            </span>
                </div>
                <div class="additional_info">
                    <a href="/events/demo-revision/information"><?php echo __("event_info") ?></a>
                </div>
            </div>
        </div>

        <div class="tr_tools">
            <button class="btn btn-primary ttools" data-tool="tn"><?php echo __("show_notes") ?></button>
            <button class="btn btn-primary ttools" data-tool="tq"><?php echo __("show_questions") ?></button>
            <button class="btn btn-primary ttools" data-tool="tw"><?php echo __("show_keywords") ?></button>
            <button class="btn btn-warning ttools" data-tool="rubric"><?php echo __("show_rubric") ?></button>
        </div>

        <div class="checker_view">
            <a href="/events/demo-revision/content_review_checker"><?php echo __("checker_other_view", [2]) ?></a>
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
            <h3><?php echo __(EventCheckSteps::CONTENT_REVIEW)?></h3>
            <ul><?php echo __("content-review-l2_desc", ["step" => __($data["next_step"])])?></ul>
        </div>
    </div>
</div>

<!-- Data for tools -->
<input type="hidden" id="bookCode" value="2ti">
<input type="hidden" id="chapter" value="2">
<input type="hidden" id="tn_lang" value="en">
<input type="hidden" id="tq_lang" value="en">
<input type="hidden" id="tw_lang" value="en">
<input type="hidden" id="totalVerses" value="26">
<input type="hidden" id="targetLang" value="en">

<script type="text/javascript" src="<?php echo template_url("js/diff_match_patch.js?2")?>"></script>
<script type="text/javascript" src="<?php echo template_url("js/diff.js?7")?>"></script>
<script>
    isChecker = true;
    isLevel2 = true;

    $(document).ready(function () {
        setTimeout(function() {
            equal_verses_height();
        }, 3000);

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

        $(".peer_verse_ta").highlightWithinTextarea({
            highlight: /\\f\s[+-]\s(.*?)\\f\*/gi
        });

        $("#next_step").click(function (e) {
            e.preventDefault();
            if(!hasChangesOnPage) window.location.href = '/events/demo-revision/pray';
            return false;
        });

        $(".ttools_panel .word_def").each(function() {
            let html = convertRcLinks($(this).html());
            $(this).html(html);
        });
    });
</script>
