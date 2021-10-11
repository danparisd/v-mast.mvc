<?php


namespace App\Repositories\Resources;


interface IResourcesRepository
{
    public function getObs($lang, $chapter = null);
}