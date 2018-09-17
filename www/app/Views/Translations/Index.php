<?php
if(isset($data['languages'])) {
    echo __("bible")."<br><br>";

    foreach ($data['languages'] as $language) {
        echo "<a href=\"" . SITEURL . "translations/" . $language->targetLang . "\">" . $language->angName . " (".$language->langName.")</a><br>";
    }
}

if(isset($data['bookProjects'])) {
    echo '<a href="'.SITEURL.'translations">'.__("bible").'</a> → '.$data['bookProjects'][0]->angName . ' ('.$data['bookProjects'][0]->langName.')<br><br>';

    foreach ($data['bookProjects'] as $bookProject) {
        echo "<a href=\"" . SITEURL . "translations/" . $bookProject->targetLang . "/". $bookProject->bookProject . "\">" . strtoupper($bookProject->bookProject) . " (".__($bookProject->bookProject).")</a><br>";
    }
}

if(isset($data['books'])) {
    echo '<a href="'.SITEURL.'translations">'.__("bible").'</a> → ';
    echo '<a href="'.SITEURL.'translations/'.$data['books'][0]->targetLang.'">'.$data['books'][0]->angName . ' ('.$data['books'][0]->langName.')</a> → ';
    echo __($data['books'][0]->bookProject).'</a><br><br>';

    if(!in_array($data["mode"], ["tn","tq","tw"]) && sizeof($data['books']) > 0)
        echo "<h4 style=\"text-align: right\"><a href='".$data['books'][0]->bookProject."/dl/usfm'>".__("download_usfm")."</a></h4>";
    else
        echo "<h4 style=\"text-align: right\"><a href='".$data['books'][0]->bookProject."/dl/md'>".__("download_markdown")."</a></h4>";

    foreach ($data['books'] as $book) {
        echo "<a href=\"" . SITEURL . "translations/" . $book->targetLang . "/" .$book->bookProject . "/" . $book->bookCode . "\">". __($book->bookCode) . "</a><br>";
    }
}

if(isset($data['book'])) {
    echo '<a href="'.SITEURL.'translations">'.__("bible").'</a> → ';
    echo '<a href="'.SITEURL.'translations/'.$data['data']->targetLang.'">'.$data['data']->angName . ' ('.$data['data']->langName.')</a> → ';
    echo '<a href="'.SITEURL.'translations/'.$data['data']->targetLang.'/'.$data['data']->bookProject.'">'.__($data["data"]->bookProject).'</a> → ';
    echo __($data['data']->bookCode).'</a><br><br>';

    echo '<h1 style="text-align: center">—— '.__($data['data']->bookCode).' ——</h1>';

    if(!in_array($data["mode"], ["tn","tq","tw"]))
        echo "<h4 style=\"text-align: right\"><a href='".$data['data']->bookCode."/usfm'>".__("download_usfm")."</a></h4>";
    else
        echo "<h4 style=\"text-align: right\"><a href='".$data['data']->bookCode."/md'>".__("download_markdown")."</a></h4>";

    echo '<div class="bible_book '.($data["data"]->bookProject == "sun" ? "sun_content" : "").' font_'.$data["data"]->targetLang.'" 
        dir="'.$data["data"]->direction.'">'.$data["book"].'</div>';
}