<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class FunctionModel extends Model
{
    protected $table = 'table_function';
    protected $fillable = ["UUID_FUNCTION", "UUID_GROUP_FUNCTION", "NAME_FUNCTION", "USER_CREATE"];
}
