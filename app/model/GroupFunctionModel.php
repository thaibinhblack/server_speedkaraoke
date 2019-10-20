<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class GroupFunctionModel extends Model
{
    protected $table = "table_group_function";
    protected $fillable = ["UUID_GROUP_FUNCTION", "NAME_GROUP_FUNCTION", "USER_CREATE"];
}
