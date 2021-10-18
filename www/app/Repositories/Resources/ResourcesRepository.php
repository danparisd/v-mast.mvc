<?php


namespace App\Repositories\Resources;


use App\Data\Obs\ObsChapter;
use App\Data\Obs\ObsMapper;
use App\Domain\ParseObs;
use App\Models\ORM\BookInfo;
use File;
use Helpers\UsfmParser;
use Support\Collection;
use Cache;
use Support\Str;
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
     * @param string $lang
     * @param null $chapter Filter by chapter
     * @return ObsChapter|Collection|null
     */
    public function getObs($lang, $chapter = null) {
        $obs_cache_key = $lang . "_obs_obs";

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
     * Get Scripture
     * @param string $lang
     * @param string $resource ulb or udb
     */
    public function getScripture($lang, $resource, $bookSlug, $bookNum, $chapter = null)
    {
        $scripture_cache_key = $lang . "_" . $resource . "_" . $bookSlug;

        if (Cache::has($scripture_cache_key)) {
            $source = Cache::get($scripture_cache_key);
            $book = json_decode($source, true);
        } else {
            $book = $this->parseScripture($lang, $resource, $bookSlug, $bookNum);
            if ($book && !empty($book["chapters"])) {
                Cache::add($scripture_cache_key, json_encode($book), 365 * 24 * 7);
            }
        }

        if ($chapter) {
            return $book[$chapter] ?? [];
        }

        return $book;
    }

    /**
     * Update resource
     * @param string $lang
     * @param string $slug
     * @return bool
     */
    public function refreshResource($lang, $slug)
    {
        $this->forgetCatalog($this->wacsCatalogPath);
        $this->forgetCatalog($this->dcsCatalogPath);

        $this->forgetResource($lang, $slug);

        switch ($slug) {
            case "rad":
            case "odb":
                return false;
            default:
                if ($this->downloadResource($lang, $slug)) return true;
                break;
        }

        return false;
    }

    /**
     * Remove downloaded resource and cache
     * @param string $lang
     * @param string $resource
     */
    private function forgetResource($lang, $resource) {
        $bookInfo = new BookInfo();
        $books = $bookInfo->all();

        switch ($resource) {
            case "ulb":
            case "udb":
            case "tq":
            case "tn":
                $category = "bible";
                break;
            default:
                $category = $resource;
        }

        // Forget cache of all the resource books
        $books->filter(function($book) use ($category) {
            return $book->category == $category;
        })->each(function($book) use ($lang, $resource) {
            $cacheKey = $lang . "_" . $resource . "_" . $book->code;
            Cache::forget($cacheKey);
        });

        $folderPath = $this->rootPath . $lang . "_" . $resource;
        File::deleteDirectory($folderPath);
    }

    /**
     * Get parsed catalog
     * @param string $path
     * @param string $url
     * @return mixed
     */
    private function getCatalog($path, $url) {
        $filepath = $path;
        if(!File::exists($filepath)) {
            $catalog = $this->downloadCatalog($url);

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
     * Update catalog
     * @param string $path
     * @param string $url
     */
    private function refreshCatalog($path, $url) {
        $this->forgetCatalog($path);
        $this->getCatalog($path, $url);
    }

    /**
     * Remove downloaded catalog
     * @param string $path
     */
    private function forgetCatalog($path) {
        File::delete($path);
    }

    /**
     * Download catalog
     * @param string $url
     * @return bool|string
     */
    private function downloadCatalog($url) {
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

    /**
     * Download resource and extract it
     * @param string $lang
     * @return null|string
     */
    private function downloadResource($lang, $resource) {
        $filePath = $this->rootPath . $lang . "_" . $resource . ".zip";
        $folderPath = $this->rootPath . $lang . "_" . $resource;

        if(!File::exists($folderPath)) {
            $zip = $this->fetchResource($lang, $resource);

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
     * Parse .usfm file of scripture and return array
     * @param string $lang
     * @param string $folderPath
     * @return array
     **/
    private function parseScripture($lang, $resource, $bookSlug, $bookNum)
    {
        $book = [];

        $folderPath = $this->downloadResource($lang, $resource);
        if(!$folderPath) return $book;

        if ($bookSlug && $bookNum) {
            $filePath = $folderPath . "/" . sprintf("%02d", $bookNum) . "-" . strtoupper($bookSlug) . ".usfm";

            if (!File::exists($filePath)) return [];

            $source = File::get($filePath);
            $usfm = UsfmParser::parse($source);

            if ($usfm && isset($usfm["chapters"])) {
                $book["id"] = $usfm["id"];
                $book["ide"] = $usfm["ide"];
                $book["h"] = $usfm["h"];
                $book["toc1"] = $usfm["toc1"];
                $book["toc2"] = $usfm["toc2"];
                $book["toc3"] = $usfm["toc3"];
                $book["mt"] = $usfm["toc3"];
                $book["chapters"] = $usfm["chapters"];

                foreach ($usfm["chapters"] as $chap => $chunks) {
                    if (!isset($book[$chap])) {
                        $book[$chap] = ["text" => []];
                    }

                    foreach ($chunks as $chunk) {
                        foreach ($chunk as $v => $text) {
                            $book[$chap]["text"][$v] = $text;
                        }
                    }

                    $arrKeys = array_keys($book[$chap]["text"]);
                    $lastVerse = explode("-", end($arrKeys));
                    $lastVerse = $lastVerse[sizeof($lastVerse)-1];
                    $book[$chap]["totalVerses"] = !empty($book[$chap]["text"]) ? $lastVerse : 0;
                }
            }
        }

        return $book;
    }

    /**
     * Parse .md files of obs and return array
     * @param string $lang
     * @return Collection
     **/
    private function parseObs($lang)
    {
        $collection = new Collection();
        $folderPath = $this->downloadResource($lang, "obs");
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
     * @param string $lang
     * @param string $type
     * @param string $catalog
     * @return string|null
     */
    private function fetchResource($lang, $resource) {
        $url = "";

        // Find resource on WACS first, if not there find in DCS
        $catalog = $this->getCatalog($this->wacsCatalogPath, $this->wacsCatalog);
        $url = $this->getResourceUrl($catalog, $lang, $resource);

        if ($url == "") {
            $catalog = $this->getCatalog($this->dcsCatalogPath, $this->dcsCatalog);
            $url = $this->getResourceUrl($catalog, $lang, $resource);
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

    private function getResourceUrl($catalog, $lang, $res) {
        $url = "";

        foreach($catalog->languages as $language)
        {
            if($language->identifier == $lang)
            {
                foreach($language->resources as $resource)
                {
                    if($resource->identifier == $res)
                    {
                        if (isset($resource->formats)) {
                            foreach ($resource->formats as $format) {
                                if (Str::endsWith($format->url, ".zip")) {
                                    $url = $format->url;
                                    break 3;
                                }
                            }
                        }

                        if (isset($resource->projects)) {
                            foreach ($resource->projects as $project) {
                                foreach($project->formats as $format)
                                {
                                    if (Str::endsWith($format->url, ".zip")) {
                                        $url = $format->url;
                                        break 4;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $url;
    }
}