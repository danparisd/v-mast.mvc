<?php


namespace App\Repositories\Resources;


interface IResourcesRepository
{
    public function getScripture($lang, $resource, $bookSlug, $bookNum, $chapter = null);

    public function getMdResource($lang, $resource, $bookSlug, $chapter = null, $toHtml = false);

    public function parseMdResource($lang, $resource, $bookSlug, $toHtml = false, $folderPath = null);

    public function getTw($lang, $category, $eventID = null, $chapter = null, $toHtml = false);

    public function getQaGuide($lang);

    public function getOtherResource($lang, $resource, $bookSlug);

    public function parseTw($lang, $bookSlug, $toHtml = true, $folderPath = null);

    public function parseTwByBook($lang, $bookSlug, $chapter, $toHtml = false);

    public function getObs($lang, $chapter = null);

    public function refreshResource($lang, $slug);

}