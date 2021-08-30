<?php


namespace App\Repositories\Language;


use App\Models\ORM\Language;

class LanguageRepository implements ILanguageRepository
{
    protected $language = null;

    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    public function create($language)
    {
        $language = new Language();
        $language->save();
        return $language;
    }

    public function get($id)
    {
        return $this->language::find($id);
    }

    public function getGwLanguages() {
        return $this->language->where("isGW", 1)->get();
    }

    public function getLanguagesByGl($gwLangName) {
        return $this->language->where("gwLang", $gwLangName)->get();
    }

    public function getByName($langName)
    {
        return $this->language::where("langName", $langName)->first();
    }

    public function getWith($relation)
    {
        return $this->language::with($relation)->get();
    }

    public function delete(&$self)
    {
        return $self->delete();
    }

    public function save(&$self)
    {
        return $self->save();
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->language, $method], $args);
    }
}