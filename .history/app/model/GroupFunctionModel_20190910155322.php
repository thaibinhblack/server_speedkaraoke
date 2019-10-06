<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class GroupFunctionModel extends Model
{
    protected $table = "TABLE_GROUP_FUNCTION";
    protected $fillable = ["UUID_GROUP_FUNCTION", "NAME_GROUP_FUNCTION", "USER_CREATE"];
}
