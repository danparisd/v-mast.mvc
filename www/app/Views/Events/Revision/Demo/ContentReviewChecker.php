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
            <div><?php echo __("step_num", ["step_number" => 5]) . ": " . __(EventCheckSteps::CONTENT_REVIEW)?></div>
        </div>
    </div>

    <div class="">
        <div class="main_content">
            <div class="main_content_text">
                <h4>Papuan Malay - <?php echo __("ulb") ?> - <?php echo __("new_test") ?> - <span class="book_name">2 Timothy 2:1-26</span></h4>

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
                        <div class="row chunk_block flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="1"> <strong class="ltr"> <sup>1</sup> </strong> <span class="orig_text" data-orig-verse="1">Jadi begitu, anakku kuat sudah dengan anugerah di dalam Kristus Yesus.</p>
                                <p class="verse_text" data-verse="2"> <strong class="ltr"> <sup>2</sup> </strong><span class="orig_text" data-orig-verse="2">Dan banyak hal yang ko dengar dari sa deng saksi yang banyak itu, beri percaya itu sama orang-orang yang setia, supaya dong dapat mengajar dong yang lain juga.</p>
                                <p class="verse_text" data-verse="3"> <strong class="ltr"> <sup>3</sup> </strong><span class="orig_text" data-orig-verse="3">Mari, tong sama sa dalam penderitaan jadi prajurit Kristus Yesus yang baik.</p>
                            </div>
                            <div class="editor_area flex_middle" dir="ltr">
                                <div class="vnote">
                                    <div class="verse_block flex_container" data-verse="1" style="margin-bottom: 10px;"> <p> <strong><sup>1</sup></strong> <span class="targetVerse" data-orig-verse="1">Jadi begitu, The text that was added will be marked green. anakku kuat sudah dengan anugerah di dalam Kristus Yesus.</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="2" style="margin-bottom: 10px;"> <p> <strong><sup>2</sup></strong> <span class="targetVerse" data-orig-verse="2">Dan banyak hal yang ko dengar dari sa deng saksi yang banyak itu, beri percaya itu sama orang-orang yang setia, supaya dong dapat mengajar dong yang lain juga.</span></p></div>
                                    <div class="verse_block flex_container" data-verse="3" style="margin-bottom: 10px;"> <p> <strong><sup>3</sup></strong> <span class="targetVerse" data-orig-verse="3">Mari, tong sama sa dalam penderitaan jadi (This is an example of replaced text) Kristus Yesus yang baik.</span></p></div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="comments_number hasComment">2 </div>
                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>
                                <div class="comments">
                                    <div class="other_comments">
                                        <div><span>Anna S. - L1:</span> This is comment from Level 1 Event</div>
                                        <div><span>Genry M. - L2:</span> This is comment of previous checker from Level 2 Event</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chunk_divider"></div>
                        <div class="row chunk_block flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="4"> <strong class="ltr"> <sup>4</sup> </strong><span class="orig_text" data-orig-verse="4">Trada satu orang tentara yang kerja sambil sibuk dengan de pu urusan hidup supaya de bisa buat de pu komandan senang.</p>
                                <p class="verse_text" data-verse="5"> <strong class="ltr"> <sup>5</sup> </strong><span class="orig_text" data-orig-verse="5">Begitu juga dengan atlit , tra akan terima mahkota kalo tra ikut aturan dalam lomba.</p>
                                <p class="verse_text" data-verse="6"> <strong class="ltr"> <sup>6</sup> </strong><span class="orig_text" data-orig-verse="6">Petani dong yang kerja keras akan (Deleted text will be marked red) terima hasil yang pertama,</p>
                                <p class="verse_text" data-verse="7"> <strong class="ltr"> <sup>7</sup> </strong><span class="orig_text" data-orig-verse="7">Ingat apa yang sa bilang, karena Tuhan akan kasi ko pengertian untuk mengerti semua ini,</p>
                            </div>
                            <div class="editor_area flex_middle" dir="ltr">
                                <div class="vnote">
                                    <div class="verse_block flex_container" data-verse="4" style="margin-bottom: 10px;"> <p> <strong><sup>4</sup></strong> <span class="targetVerse" data-orig-verse="4">Trada satu orang tentara yang kerja sambil sibuk dengan de pu urusan hidup supaya de bisa buat de pu komandan senang.</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="5" style="margin-bottom: 10px;"> <p> <strong><sup>5</sup></strong> <span class="targetVerse" data-orig-verse="5">Begitu juga dengan atlit , tra akan terima mahkota kalo tra ikut aturan dalam lomba.</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="6" style="margin-bottom: 10px;"> <p> <strong><sup>6</sup></strong> <span class="targetVerse" data-orig-verse="6">Petani dong yang kerja keras akan terima hasil yang pertama,</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="7" style="margin-bottom: 10px;"> <p> <strong><sup>7</sup></strong> <span class="targetVerse" data-orig-verse="7">Ingat apa yang sa bilang, karena Tuhan akan kasi ko pengertian untuk mengerti semua ini,</span></p> </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="comments_number hasComment">3 </div>
                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>
                                <div class="comments">
                                    <div class="my_comment">This is my comment as a Revision checker</div>
                                    <div class="other_comments">
                                        <div><span>Genry M. - L2:</span> This is another comment of previous checker from Level 2 Event</div>
                                        <div><span>Mark P. - L2:</span> This is another comment of previous checker from Level 2 Event</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chunk_divider"></div>
                        <div class="row chunk_block flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="8"> <strong class="ltr"> <sup>8</sup> </strong><span class="orig_text" data-orig-verse="8">Ingat: Yesus Kristus, keturunan Daud, su bangkit dari kematian. ini su sesuai dengan pesan Injil yang sa percaya.</p>
                                <p class="verse_text" data-verse="9"> <strong class="ltr"> <sup>9</sup> </strong><span class="orig_text" data-orig-verse="9">Sampe pada titik penderitaan karna diikat rantai seperti kriminal. tapi firman Allah tra diikat deng rantai.</p>
                                <p class="verse_text" data-verse="10"> <strong class="ltr"> <sup>10</sup> </strong><span class="orig_text" data-orig-verse="10">Jadi sa bertahan untuk orang-orang yang Tuhan pilih, supaya dong dapat keselamatan yang kekal dalam Kristus Yesus, deng kemuliaan yang abadi.</p>
                            </div>
                            <div class="editor_area flex_middle" dir="ltr">
                                <div class="vnote">
                                    <div class="verse_block flex_container" data-verse="8" style="margin-bottom: 10px;"> <p> <strong><sup>8</sup></strong> <span class="targetVerse" data-orig-verse="8">Ingat: Yesus Kristus, keturunan Daud, su bangkit dari kematian. ini su sesuai dengan pesan Injil yang sa percaya.</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="9" style="margin-bottom: 10px;"> <p> <strong><sup>9</sup></strong> <span class="targetVerse" data-orig-verse="9">Sampe pada titik penderitaan karna diikat rantai seperti kriminal. tapi firman Allah tra diikat deng rantai.</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="10" style="margin-bottom: 10px;"> <p> <strong><sup>10</sup></strong> <span class="targetVerse" data-orig-verse="10">Jadi sa bertahan untuk orang-orang yang Tuhan pilih, supaya dong dapat keselamatan yang kekal dalam Kristus Yesus, deng kemuliaan yang abadi.</span></p> </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="comments_number"> </div>
                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>
                                <div class="comments"></div>
                            </div>
                        </div>
                        <div class="chunk_divider"></div>
                        <div class="row chunk_block flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="11"> <strong class="ltr"> <sup>11</sup> </strong><span class="orig_text" data-orig-verse="11">Apa yang sa bilang ini, bisa dipercaya: kalo tong mau mati untuk Dia, torang juga akan hidup bersama deng Dia.</p>
                                <p class="verse_text" data-verse="12"> <strong class="ltr"> <sup>12</sup> </strong><span class="orig_text" data-orig-verse="12">Apalagi kalo tong bertahan , tong juga akan ditinggikan deng Dia. Klo tong menyangkal Dia, Dia juga akan menyangkal ketong,</p>
                                <p class="verse_text" data-verse="13"> <strong class="ltr"> <sup>13</sup> </strong><span class="orig_text" data-orig-verse="13">Klo tong tra setia, De tetap setia karena De tra bisa menyangkal diri.</p>
                                <p class="verse_text" data-verse="14"> <strong class="ltr"> <sup>14</sup> </strong><span class="orig_text" data-orig-verse="14">Selalu kasi ingat dong di hadapan Allah, supaya dong jangan berdebat tentang Firman karena itu akan bikin kacau orang yang dengar,</p>
                            </div>
                            <div class="editor_area flex_middle" dir="ltr">
                                <div class="vnote">
                                    <div class="verse_block flex_container" data-verse="11" style="margin-bottom: 10px;"> <p> <strong><sup>11</sup></strong> <span class="targetVerse" data-orig-verse="11">Apa yang sa bilang ini, bisa dipercaya: kalo tong mau mati untuk Dia, torang juga akan hidup bersama deng Dia.</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="12" style="margin-bottom: 10px;"> <p> <strong><sup>12</sup></strong> <span class="targetVerse" data-orig-verse="12">Apalagi kalo tong bertahan , tong juga akan ditinggikan deng Dia. Klo tong menyangkal Dia, Dia juga akan menyangkal ketong,</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="13" style="margin-bottom: 10px;"> <p> <strong><sup>13</sup></strong> <span class="targetVerse" data-orig-verse="13">Klo tong tra setia, De tetap setia karena De tra bisa menyangkal diri.</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="14" style="margin-bottom: 10px;"> <p> <strong><sup>14</sup></strong> <span class="targetVerse" data-orig-verse="14">Selalu kasi ingat dong di hadapan Allah, supaya dong jangan berdebat tentang Firman karena itu akan bikin kacau orang yang dengar,</span></p> </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="comments_number"> </div>
                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>
                                <div class="comments"></div>
                            </div>
                        </div>
                        <div class="chunk_divider"></div>
                        <div class="row chunk_block flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="15"> <strong class="ltr"> <sup>15</sup> </strong><span class="orig_text" data-orig-verse="15">Lakukan yang paling baik itu adalah persembahan yang Tuhan terima, jadi pekerja trada alasan untuk dapat kasi malu, yang ajar kebeneran Firman dengan pas.</p>
                                <p class="verse_text" data-verse="16"> <strong class="ltr"> <sup>16</sup> </strong><span class="orig_text" data-orig-verse="16">Hindari omong kosong dan tra bersih yang nanti jadi tra baik.</p>
                                <p class="verse_text" data-verse="17"> <strong class="ltr"> <sup>17</sup> </strong><span class="orig_text" data-orig-verse="17">Perkataan dong akan menyebar seperti kangker. Diantara dong itu ada Himeneus dan Filetus.</p>
                            </div>
                            <div class="editor_area flex_middle" dir="ltr">
                                <div class="vnote">
                                    <div class="verse_block flex_container" data-verse="15" style="margin-bottom: 10px;"> <p> <strong><sup>15</sup></strong> <span class="targetVerse" data-orig-verse="15">Lakukan yang paling baik itu adalah persembahan yang Tuhan terima, jadi pekerja trada alasan untuk dapat kasi malu, yang ajar kebeneran Firman dengan pas.</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="16" style="margin-bottom: 10px;"> <p> <strong><sup>16</sup></strong> <span class="targetVerse" data-orig-verse="16">Hindari omong kosong dan tra bersih yang nanti jadi tra baik.</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="17" style="margin-bottom: 10px;"> <p> <strong><sup>17</sup></strong> <span class="targetVerse" data-orig-verse="17">Perkataan dong akan menyebar seperti kangker. Diantara dong itu ada Himeneus dan Filetus.</span></p> </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="comments_number"> </div>
                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>
                                <div class="comments"></div>
                            </div>
                        </div>
                        <div class="chunk_divider"></div>
                        <div class="row chunk_block flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="18"> <strong class="ltr"> <sup>18</sup> </strong><span class="orig_text" data-orig-verse="18">Dong adalah orang-orang yang sudah tidak benar. dong katakan kebangkitan sudah terjadi, dong putar balik iman dari berapa orang tu.</p>
                                <p class="verse_text" data-verse="19"> <strong class="ltr"> <sup>19</sup> </strong><span class="orig_text" data-orig-verse="19">Biar begitu, Allah pu fondasi kuat tetap berdiri. ada piagam dengan tulisan yang bilang" Tuhan kenal dong Dia pu milik." . dan orang yang percaya Tuhan harus kasi tinggal yang tra benar.</p>
                                <p class="verse_text" data-verse="20"> <strong class="ltr"> <sup>20</sup> </strong><span class="orig_text" data-orig-verse="20">Dalam rumah kaya bukan saja ada emas dan perak tapi juga ada kotak-kotak kayu sama tanah liat. barang itu di pake untuk hal-hal yang terhormat, dan ada juga untuk hal-hal tra terhormat.</p>
                            </div>
                            <div class="editor_area flex_middle" dir="ltr">
                                <div class="vnote">
                                    <div class="verse_block flex_container" data-verse="18" style="margin-bottom: 10px;"> <p> <strong><sup>18</sup></strong> <span class="targetVerse" data-orig-verse="18">Dong adalah orang-orang yang sudah tidak benar. dong katakan kebangkitan sudah terjadi, dong putar balik iman dari berapa orang tu.</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="19" style="margin-bottom: 10px;"> <p> <strong><sup>19</sup></strong> <span class="targetVerse" data-orig-verse="19">Biar begitu, Allah pu fondasi kuat tetap berdiri. ada piagam dengan tulisan yang bilang" Tuhan kenal dong Dia pu milik." . dan orang yang percaya Tuhan harus kasi tinggal yang tra benar.</span></p></div>
                                    <div class="verse_block flex_container" data-verse="20" style="margin-bottom: 10px;"> <p> <strong><sup>20</sup></strong> <span class="targetVerse" data-orig-verse="20">Dalam rumah kaya bukan saja ada emas dan perak tapi juga ada kotak-kotak kayu sama tanah liat. barang itu di pake untuk hal-hal yang terhormat, dan ada juga untuk hal-hal tra terhormat.</span></p> </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="comments_number"> </div>
                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>
                                <div class="comments"></div>
                            </div>
                        </div>
                        <div class="chunk_divider"></div>
                        <div class="row chunk_block flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="21"> <strong class="ltr"> <sup>21</sup> </strong><span class="orig_text" data-orig-verse="21">Jika satu orang kasi bersih de pu diri dari yang tra terhormat, de itu bejana yang terhormat. de dipilih , dipake untuk tuannya, dan de disiapkan untuk semua perbuatan yang baik.</p>
                                <p class="verse_text" data-verse="22"> <strong class="ltr"> <sup>22</sup> </strong><span class="orig_text" data-orig-verse="22">Jauhi sudah dari nafsu anak-anak muda, kejar itu kebenaran, iman, kasih, dan damai, sama-sama deng dong yang panggil Tuhan dengan hati yang bersih.</p>
                                <p class="verse_text" data-verse="23"> <strong class="ltr"> <sup>23</sup> </strong><span class="orig_text" data-orig-verse="23">Tapi tolak sudah pertanyaan-pertanyaan bodok. Kam tahu itu semua nanti jadi sebab baku tengkar.</p>
                            </div>
                            <div class="editor_area flex_middle" dir="ltr">
                                <div class="vnote">
                                    <div class="verse_block flex_container" data-verse="21" style="margin-bottom: 10px;"> <p> <strong><sup>21</sup></strong> <span class="targetVerse" data-orig-verse="21">Jika satu orang kasi bersih de pu diri dari yang tra terhormat, de itu bejana yang terhormat. de dipilih , dipake untuk tuannya, dan de disiapkan untuk semua perbuatan yang baik.</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="22" style="margin-bottom: 10px;"> <p> <strong><sup>22</sup></strong> <span class="targetVerse" data-orig-verse="22">Jauhi sudah dari nafsu anak-anak muda, kejar itu kebenaran, iman, kasih, dan damai, sama-sama deng dong yang panggil Tuhan dengan hati yang bersih.</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="23" style="margin-bottom: 10px;"> <p> <strong><sup>23</sup></strong> <span class="targetVerse" data-orig-verse="23">Tapi tolak sudah pertanyaan-pertanyaan bodok. Kam tahu itu semua nanti jadi sebab baku tengkar.</span></p> </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="comments_number"> </div>
                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>
                                <div class="comments"></div>
                            </div>
                        </div>
                        <div class="chunk_divider"></div>
                        <div class="row chunk_block flex_container">
                            <div class="chunk_verses flex_left" dir="ltr">
                                <p class="verse_text" data-verse="24"> <strong class="ltr"> <sup>24</sup> </strong><span class="orig_text" data-orig-verse="24">Orang yang melayani Tuhan tra boleh bertengkar tapi harus lemah lembut pada semua dong, Dong harus pintar mengajar, sabar</p>
                                <p class="verse_text" data-verse="25"> <strong class="ltr"> <sup>25</sup> </strong><span class="orig_text" data-orig-verse="25">de kasi ajaran deng lemah lembut sama dong yang melawan dia. mungkin Allah kasi kesempatan untuk dong bertobat pada pengetahuan akan kebenaran.</p>
                                <p class="verse_text" data-verse="26"> <strong class="ltr"> <sup>26</sup> </strong><span class="orig_text" data-orig-verse="26">mungkin dong sadar kembali dan kasi tinggal jerat iblis setelah selama ini dong ditawan untuk ikut perintahnya.</p>
                            </div>
                            <div class="editor_area flex_middle" dir="ltr">
                                <div class="vnote">
                                    <div class="verse_block flex_container" data-verse="24" style="margin-bottom: 10px;"> <p> <strong><sup>24</sup></strong> <span class="targetVerse" data-orig-verse="24">Orang yang melayani Tuhan tra boleh bertengkar tapi harus lemah lembut pada semua dong, Dong harus pintar mengajar, sabar</span></p> </div>
                                    <div class="verse_block flex_container" data-verse="25" style="margin-bottom: 10px;"> <p> <strong><sup>25</sup></strong> <span class="targetVerse" data-orig-verse="25">de kasi ajaran deng lemah lembut sama dong yang melawan dia. mungkin Allah kasi kesempatan untuk dong bertobat pada pengetahuan akan kebenaran.</span></p></div>
                                    <div class="verse_block flex_container" data-verse="26" style="margin-bottom: 10px;"> <p> <strong><sup>26</sup></strong> <span class="targetVerse" data-orig-verse="26">mungkin dong sadar kembali dan kasi tinggal jerat iblis setelah selama ini dong ditawan untuk ikut perintahnya.</span></p> </div>
                                </div>
                            </div>
                            <div class="flex_right">
                                <div class="comments_number"> </div>
                                <span class="editComment mdi mdi-lead-pencil"
                                      data="0:0"
                                      title="<?php echo __("write_note_title", [""])?>"></span>
                                <div class="comments"></div>
                            </div>
                        </div>
                        <div class="chunk_divider"></div>
                    </div>
                </div>

                <div id="source_scripture_content" class="my_content">
                    <div class="chunk_block">
                        <div class="chunk_verses" dir="ltr">
                            <div> <strong class="ltr"> <sup>1</sup> </strong> <div class="kwverse_2_0_1">You therefore, my child, be strengthened in the grace that is in Christ Jesus.</div></div>
                            <div> <strong class="ltr"> <sup>2</sup> </strong> <div class="kwverse_2_0_2">And the things you heard from me among many witnesses, entrust them to faithful people who will be able to teach others also.</div></div>
                            <div> <strong class="ltr"> <sup>3</sup> </strong> <div class="kwverse_2_0_3">Suffer hardship with me, as a good soldier of Christ Jesus.</div></div>
                        </div>
                    </div>
                    <div class="chunk_block">
                        <div class="chunk_verses" dir="ltr">
                            <div> <strong class="ltr"> <sup>4</sup> </strong> <div class="kwverse_2_0_4">No soldier serves while entangled in the affairs of this life, so that he may please his superior officer.</div></div>
                            <div> <strong class="ltr"> <sup>5</sup> </strong> <div class="kwverse_2_0_5">Also, if someone competes as an athlete, he is not crowned unless he competes by the rules.</div></div>
                            <div> <strong class="ltr"> <sup>6</sup> </strong> <div class="kwverse_2_0_6">It is necessary that the hardworking farmer receive his share of the crops first.</div></div>
                            <div> <strong class="ltr"> <sup>7</sup> </strong> <div class="kwverse_2_0_7">Think about what I am saying, for the Lord will give you understanding in everything.</div></div>
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
                <div class="step_right alt"><?php echo __("step_num", ["step_number" => 5])?></div>
            </div>
        </div>
    </div>
</div>

<div class="content_help closed">
    <div id="help_hide" class="glyphicon glyphicon-chevron-left"> <?php echo __("help") ?></div>

    <div class="help_float">
        <div class="help_info_steps is_checker_page_help isPeer">
            <div class="help_name_steps"><span><?php echo __("step_num", ["step_number" => 5])?>:</span> <?php echo __(EventCheckSteps::CONTENT_REVIEW)?></div>
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
            <a href="/events/demo-revision/content_review"><?php echo __("checker_other_view", [1]) ?></a>
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

        <div class="tutorial_content is_checker_page_help">
            <h3><?php echo __(EventCheckSteps::CONTENT_REVIEW)?></h3>
            <ul><?php echo __("content-review-l2_chk_desc", ["step" => __($data["next_step"])])?></ul>
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

        $("#next_step").click(function (e) {
            renderConfirmPopup(Language.checkerConfirmTitle, Language.checkerConfirm,
                function () {
                    window.location.href = '/events/demo-revision/pray';
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
