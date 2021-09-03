<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>
    <meta charset="utf-8">
</head>
<body>

<h6>
    <?php echo __("chapter_assigned_msg") ?>
</h6>
<div style="font-weight: bold; margin-top: 20px;">

    <div style="color: #249b45;  font-weight: bolder; margin-top: 20px; ">
        <?php echo __("chapter", $chapter) ?>
    </div><br/>

    <?php echo __("book") . ": " . $book ?><br/>
    <?php echo __("project") . ": " . $project ?><br/>
    <?php echo __("gateway_language") . ": " . $language ?><br/>
    <?php echo __("target_lang") . ": " . $target?>

</div>
</body>
</html>
