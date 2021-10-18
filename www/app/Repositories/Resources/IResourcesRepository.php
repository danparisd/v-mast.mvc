<?php


namespace App\Repositories\Resources;


interface IResourcesRepository
{
    public function getScripture($lang, $resource, $bookSlug, $bookNum, $chapter = null);
    public function getObs($lang, $chapter = null);
    public function refreshResource($lang, $slug);
}