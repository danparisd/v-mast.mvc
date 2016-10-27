<div id="translator_contents" class="row panel-body">
    <div class="row main_content_header">
        <div class="main_content_title"><?php echo __("step_num", [2]) . ": "  . __("verbalize")?></div>
        <div class="demo_title"><?php echo __("demo") ?></div>
    </div>

    <div class="row">
        <div class="main_content col-sm-9">
            <div class="main_content_text">
                <h4>English - <?php echo __("ulb") ?> - <?php echo __("new_test") ?> - <span class="book_name">2 Timothy 2:1-26</span></h4>

                <p><strong><sup>1</sup></strong> You therefore, my child, be strengthened in the grace that is in Christ Jesus.</p>
                <p><strong><sup>2</sup></strong> And the things you heard from me among many witnesses, entrust them to faithful people who will be able to teach others also.</p>
                <p><strong><sup>3</sup></strong> Suffer hardship with me, as a good soldier of Christ Jesus.</p>
                <p><strong><sup>4</sup></strong> No soldier serves while entangled in the affairs of this life, so that he may please his superior officer.</p>
                <p><strong><sup>5</sup></strong> Also, if someone competes as an athlete, he is not crowned unless he competes by the rules.</p>
                <p><strong><sup>6</sup></strong> It is necessary that the hardworking farmer receive his share of the crops first.</p>
                <p><strong><sup>7</sup></strong> Think about what I am saying, for the Lord will give you understanding in everything.</p>
                <p><strong><sup>8</sup></strong> Remember Jesus Christ, from David's seed, who was raised from the dead ones. This is according to my gospel message,</p>
                <p><strong><sup>9</sup></strong> for which I am suffering to the point of being chained as a criminal. But the word of God is not chained.</p>
                <p><strong><sup>10</sup></strong> Therefore I endure all things for those who are chosen, so that they also may obtain the salvation that is in Christ Jesus, with eternal glory.</p>
                <p><strong><sup>11</sup></strong> This saying is trustworthy:   "If we have died with him, we will also live with him. </p>
                <p><strong><sup>12</sup></strong> If we endure, we will also reign with him. If we deny him, he also will deny us. </p>
                <p><strong><sup>13</sup></strong> if we are unfaithful, he remains faithful,  for he cannot deny himself." </p>
                <p><strong><sup>14</sup></strong> Keep reminding them of these things. Warn them before God not to quarrel about words. Because of this there is nothing useful. Because of this there is destruction for those who listen. <note data-original-title="Some versions read, Warn them before the Lord " data-toggle="tooltip" data-placement="auto right" title="" class="glyphicon glyphicon-file"></note></p>
                <p><strong><sup>15</sup></strong> Do your best to present yourself to God as one approved, a worker who has no reason to be ashamed, who accurately teaches the word of truth.</p>
                <p><strong><sup>16</sup></strong> Avoid profane talk, which leads to more and more godlessness.</p>
                <p><strong><sup>17</sup></strong> Their talk will spread like gangrene. Among whom are Hymenaeus and Philetus.</p>
                <p><strong><sup>18</sup></strong> These are men who have missed the truth. They say that the resurrection has already happened. They overturn the faith of some.</p>
                <p><strong><sup>19</sup></strong> However, the firm foundation of God stands. It has this inscription: "The Lord knows those who are his" and "Everyone who names the name of the Lord must depart from unrighteousness."</p>
                <p><strong><sup>20</sup></strong> In a wealthy home, there are not only containers of gold and silver. There are also containers of wood and clay. Some of these are for honorable use, and some for dishonorable.</p>
                <p><strong><sup>21</sup></strong> If someone cleans himself from dishonorable use, he is an honorable container. He is set apart, useful to the Master, and prepared for every good work.</p>
                <p><strong><sup>22</sup></strong> Flee youthful lusts. Pursue righteousness, faith, love, and peace with those who call on the Lord out of a clean heart.</p>
                <p><strong><sup>23</sup></strong> But refuse foolish and ignorant questions. You know that they give birth to arguments.</p>
                <p><strong><sup>24</sup></strong> The Lord's servant must not quarrel. Instead he must be gentle toward all, able to teach, and patient.</p>
                <p><strong><sup>25</sup></strong> He must in meekness educate those who oppose him. God may perhaps give them repentance for the knowledge of the truth.</p>
                <p><strong><sup>26</sup></strong> They may become sober again and leave the devil's trap, after they have been captured by him for his will.</p>
            </div>

            <div class="main_content_footer row">
                <form action="" method="post">
                    <div class="form-group">
                        <div class="main_content_confirm_desc"><?php echo __("confirm_finished")?></div>
                        <label><input name="confirm_step" id="confirm_step" value="1" type="checkbox"> <?php echo __("confirm_yes")?></label>
                    </div>

                    <button id="next_step" onclick="window.location.href='<?php echo SITEURL ?>events/demo/chunking'; return false;" class="btn btn-primary" disabled="disabled"><?php echo __("next_step")?></button>
                </form>
            </div>
        </div>

        <div class="content_help col-sm-3">
            <div class="help_info_steps">
                <div class="help_title_steps"><?php echo __("help") ?></div>

                <div class="clear"></div>

                <div class="help_name_steps"><span><?php echo __("step_num", [2])?>:</span> <?php echo __("verbalize")?></div>
                <div class="help_descr_steps">
                    <ul><?php echo mb_substr(__("verbalize_desc"), 0, 300)?>... <div class="show_tutorial_popup"> >>> <?php echo __("show_more")?></div></ul>
                </div>
            </div>

            <div class="event_info">
                <div class="participant_info">
                    <div class="participant_name">
                        <span><?php echo __("your_checker") ?>:</span>
                        <span>mSimpson</span>
                    </div>
                    <div class="additional_info">
                        <a href="#"><?php echo __("event_info") ?></a>
                    </div>
                </div>
            </div>

            <div class="checker_view">
                <a href="<?php echo SITEURL ?>events/demo/verbalize_checker"><?php echo __("checker_view") ?></a>
            </div>
        </div>
    </div>
</div>


<div class="tutorial_container">
    <div class="tutorial_popup">
        <div class="tutorial-close glyphicon glyphicon-remove"></div>
        <div class="tutorial_pic">
            <img src="<?php echo template_url("img/steps/icons/verbalize.png") ?>" height="100px" width="100px">
            <img src="<?php echo template_url("img/steps/big/verbalize.png") ?>" height="280px" width="280px">
            <div class="hide_tutorial">
                <label><input id="hide_tutorial" data="verbalize" value="0" type="checkbox"> <?php echo __("do_not_show_tutorial")?></label>
            </div>
        </div>

        <div class="tutorial_content">
            <h3><?php echo __("verbalize")?></h3>
            <ul><?php echo __("verbalize_desc")?></ul>
        </div>
    </div>
</div>