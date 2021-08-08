<?php


namespace App\Models\ORM;

use Database\ORM\Model;

class TwGroup extends Model
{
    protected $table = "tw_groups";
    protected $fillable = array();
    protected $primaryKey = 'groupID';
    public $timestamps  = false;
}