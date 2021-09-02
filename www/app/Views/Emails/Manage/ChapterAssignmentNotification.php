<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>
    <meta charset="utf-8">
</head>
<body>

<h6>You have been added as a translator to the following chapter.</h6>
<div style="font-weight: bold; margin-top: 20px;">

    <div style="color: #249b45;  font-weight: bolder; margin-top: 20px; ">
        <?php echo "Assigned Chapter: " . $chapter ?>
    </div><br/>

    <?php echo "Book: " . $book ?><br/>
    <?php echo "Project: " . $project ?><br/>
    <?php echo "Gateway Language: " . $language ?><br/>
    <?php echo "Target Language: " . $target ?><br/>

</div>
</body>
</html>
