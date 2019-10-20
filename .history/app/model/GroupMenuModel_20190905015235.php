<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class GroupMenuModel extends Model
{
    protected $table = "TABLE_GROUP_MENU";
    protected $fillable = ["UUID_GROUP_MENU", "NAME_GROUP_MENU"];
}
