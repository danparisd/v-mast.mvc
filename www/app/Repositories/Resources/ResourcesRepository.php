<?php


namespace App\Repositories\Resources;


use App\Data\Obs\ObsChapter;
use App\Data\Obs\ObsMapper;
use App\Domain\ParseObs;
use File;
use Support\Collection;
use Cache;
use ZipArchive;

class ResourcesRepository implements IResourcesRepository {
    private $rootPath = "../app/Templates/Default/Assets/source/";

    private $wacsCatalog = "https://api.bibletranslationtools.org/v3/catalog.json";
    private $dcsCatalog = "https://api.door43.org/v3/catalog.json";

    private $wacsCatalogPath;
    private $dcsCatalogPath;

    public function __construct() {
        $this->wacsCatalogPath = $this->rootPath . "catalog.json";
        $this->dcsCatalogPath = $this->rootPath . "catalog_dcs.json";
    }

    /**
     * Get Obs collection
     * @param $lang
     * @param null $chapter Filter by chapter
     * @return ObsChapter|Collection|null
     */
    public function getObs($lang, $chapter = null) {
        $obs_cache_key = $lang . "_obs";

        if (Cache::has($obs_cache_key)) {
            $obsSource = Cache::get($obs_cache_key);
            $data = json_decode($obsSource, true);
            $obs = ObsMapper::toObs($data);
        } else {
            $obs = $this->parseObs($lang);
            if (!$obs->isEmpty()) {
                $data = ObsMapper::fromObs($obs);
                Cache::add($obs_cache_key, json_encode($data), 365 * 24 * 7);
            }
        }

        if ($chapter) {
            $obs = $obs->filter(function($item) use ($chapter) {
                return $item->chapter == $chapter;
            })->first();
        }

        return $obs;
    }

    /**
     * Remove downloaded resource and cache
     * @param $lang
     * @param $resource
     */
    public function forgetResource($lang, $resource) {
        $cacheKey = $lang . "_" . $resource;
        Cache::forget($cacheKey);

        $folderPath = $this->rootPath . $lang . "_" . $resource;
        File::deleteDirectory($folderPath);
    }

    public function forgetCatalog() {
        File::delete($this->dcsCatalogPath);
        File::delete($this->wacsCatalogPath);
    }

    private function getFullCatalog($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $cat = curl_exec($ch);

        if(curl_errno($ch)) {
            return false;
        }

        curl_close($ch);
        return $cat;
    }

    private function getCachedFullCatalog($path, $url) {
        $filepath = $path;
        if(!File::exists($filepath)) {
            $catalog = $this->getFullCatalog($url);

            if($catalog)
                File::put($filepath, $catalog);
            else
                $catalog = "[]";
        } else {
            $catalog = File::get($filepath);
        }

        return json_decode($catalog);
    }

    /**
     * Download OBS and extract them
     * @param string $lang
     * @param bool $update
     * @return null|string
     */
    private function downloadObs($lang = "en", $update = false) {
        $filePath = $this->rootPath . $lang . "_obs.zip";
        $folderPath = $this->rootPath . $lang . "_obs";

        if(!File::exists($folderPath) || $update) {
            // Get catalog
            $catalog = $this->getCachedFullCatalog($this->dcsCatalogPath, $this->dcsCatalog);
            if(empty($catalog)) return false;

            $zip = $this->fetchResource($lang, "obs", $catalog);

            if ($zip) {
                File::put($filePath, $zip);

                if(File::exists($filePath))
                {
                    $zip = new ZipArchive();
                    $zip->open($filePath);
                    $zip->extractTo($this->rootPath);
                    $zip->close();

                    File::delete($filePath);
                }
            } else {
                $folderPath = null;
            }
        }

        return $folderPath;
    }

    /**
     * Parses .md files of obs and returns array
     * @param $lang
     * @param $folderPath
     * @return  Collection
     **/
    private function parseObs($lang ="en", $folderPath = null)
    {
        $collection = new Collection();

        if($folderPath == null)
            $folderPath = $this->downloadObs($lang);

        if(!$folderPath) return $collection;

        $contentPath = $folderPath . "/content";

        $files = File::allFiles($contentPath);
        foreach($files as $file)
        {
            preg_match("/([0-9]{2,3}).md$/i", $file, $matches);
            if(!isset($matches[1])) continue;
            $chapter = (int)$matches[1];

            $md = File::get($file);
            $collection->push(ParseObs::parse($md, $chapter));
        }

        $collection->sortBy(function($item) {
            return $item->chapter;
        });

        return $collection;
    }

    /**
     * Fetch resource from remote url
     * @param $lang
     * @param $type
     * @param $catalog
     * @return string|null
     */
    private function fetchResource($lang, $type, $catalog) {
        $url = "";

        foreach($catalog->languages as $language)
        {
            if($language->identifier == $lang)
            {
                foreach($language->resources as $resource)
                {
                    if($resource->identifier == $type)
                    {
                        foreach($resource->formats as $format)
                        {
                            $url = $format->url;
                            break;
                        }
                    }
                }
            }
        }

        if($url == "") return null;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        $resource = curl_exec($ch);

        if(curl_errno($ch))
        {
            return null;
        }

        curl_close($ch);

        return $resource;
    }
}