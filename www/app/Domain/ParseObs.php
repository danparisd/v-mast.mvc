<?php

namespace App\Domain;

use App\Data\Obs\ObsChapter;
use App\Data\Obs\ObsChunk;
use App\Data\Obs\ObsChunkType;
use Support\Collection;

class ParseObs
{
    public static function parse($md, $chapter) {
        $lines = preg_split("/\\r\\n|\\n|\\r/", $md);
        $obsChunks = new Collection();

        $tmpImg = null;
        foreach ($lines as $line) {
            $title = ParseObs::parseTitle($line);
            $image = ParseObs::parseImage($line);
            $paragraph = ParseObs::parseParagraph($line);
            $description = ParseObs::parseDescription($line);

            if ($title)
                $obsChunks->push(
                    ParseObs::toChunk(ObsChunkType::TITLE, $title)
                );
            if ($image)
                $tmpImg = $image;
            if ($paragraph)
                $obsChunks->push(
                    ParseObs::toChunk(ObsChunkType::PARAGRAPH, $paragraph, $tmpImg)
                );
            if ($description)
                $obsChunks->push(
                    ParseObs::toChunk(ObsChunkType::DESCRIPTION, $description)
                );
        }

        return new ObsChapter($chapter, $obsChunks);
    }

    private static function parseTitle($line) {
        $regex = "/^#\s(.*)/";
        $hasTitle = preg_match($regex, $line, $matches);
        if ($hasTitle && isset($matches[1])) {
            return $matches[1];
        }
        return null;
    }

    private static function parseImage($line) {
        $regex = "/^!\[OBS Image\]\((.*?)\)/";
        $hasImage = preg_match($regex, $line, $matches);
        if ($hasImage && isset($matches[1])) {
            return $matches[1];
        }
        return null;
    }

    private static function parseParagraph($line) {
        $regex = "/^(?!#|!|_).*/";
        $hasText = preg_match($regex, $line, $matches);
        if ($hasText && isset($matches[0])) {
            return $matches[0];
        }
        return null;
    }

    private static function parseDescription($line) {
        $regex = "/^_(.*?)_$/";
        $hasDescription = preg_match($regex, $line, $matches);
        if ($hasDescription && isset($matches[1])) {
            return $matches[1];
        }
        return null;
    }

    private static function toChunk( $type, $title, $img = null) {
        return new ObsChunk($type, $title, $img);
    }
}