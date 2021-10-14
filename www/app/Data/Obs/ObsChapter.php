<?php

namespace App\Data\Obs;

class ObsChapter
{
    public $chapter;
    public $chunks;

    public function __construct($chapter, $chunks)
    {
        $this->chapter = $chapter;
        $this->chunks = $chunks;
    }
}