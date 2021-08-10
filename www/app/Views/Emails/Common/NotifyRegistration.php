<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2><?php echo __("new_account_title"); ?></h2>

        <div style="font-size: 18px">
            <div style="margin-top: 20px">
                <div><?php echo __("new_account_message", ["name" => $name, "username" => $userName]) ?></div>
                <div>
                    <strong><?php echo __("proj_lang_public") ?>: </strong>
                    <?php echo $projectLanguage ?>
                </div>
                <div>
                    <strong><?php echo __("Projects")  ?>: </strong>
                    <?php echo $projects ?>
                </div>
            </div>

            <div style="margin-top: 20px">
                <div><?php echo __("member_profile_message") ?>:</div>
                <div><a href="<?php echo SITEURL."members/profile/".$id ?>"><?php echo $name." (".$userName.")" ?></a></div>
            </div>

            <div style="margin-top: 20px">
                <div><a href="<?php echo SITEURL."admin/members" ?>"><?php echo __("members_area") ?></a></div>
            </div>
        </div>
    </body>
</html>
