<?php


namespace App\Repositories\Event;


interface IEventRepository
{
    public function create($data, $project);

    public function get($id);

    public function getWith($relation);

    public function calculateEventProgress($event, $level);

    public function delete(&$self);

    public function save(&$self);
}