<?php


namespace App\Models\ORM;

use Database\ORM\Model;

class Language extends Model
{
    protected $primaryKey = 'langID';
    public $timestamps  = false;

    public function targetLanguages() {
        return $this->hasMany(Language::class, "gwLang", "langName");
    }
}