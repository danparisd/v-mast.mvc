<?php

namespace App\Data\Obs;

use Support\Collection;

class ObsMapper
{
    public static function fromObs($obs) {
        return $obs->map(function($item) {
            return ObsMapper::fromChapter($item);
        })->toArray();
    }

    public static function toObs($array) {
        $chapters = array_map(function ($item) {
            return ObsMapper::toChapter($item);
        }, $array);
        return new Collection($chapters);
    }

    private static function fromChunk($chunk) {
        return [
            "type" => $chunk->type,
            "title" => $chunk->title,
            "img" => $chunk->img
        ];
    }

    private static function toChunk($data) {
        return new ObsChunk(
            $data["type"],
            $data["title"],
            $data["img"]
        );
    }

    private static function fromChapter($chapter) {
        return [
            "chapter" => $chapter->chapter,
            "chunks" => $chapter->chunks->map(function($item) {
                return ObsMapper::fromChunk($item);
            })->toArray()
        ];
    }

    private static function toChapter($data) {
        $chunksArr = array_map(function($item) {
            return ObsMapper::toChunk($item);
        }, $data["chunks"]);
        $chunks = new Collection($chunksArr);

        return new ObsChapter($data["chapter"], $chunks);
    }
}