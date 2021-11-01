<?php

namespace App\Data\Obs;

class ObsChunk
{
    public $type;
    public $title;
    public $img;

    public function __construct($type, $title, $img = null)
    {
        $this->type = $type;
        $this->title = $title;
        $this->img = $img;
    }
}