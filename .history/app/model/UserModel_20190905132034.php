<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = "TABLE_USER";
    protected $fillable = ["UUID_USER", "UUID_RULE", "USERNAME", "PASSWORD", "AVATAR", "EMAIL", "PHONE", "DISPLAY_NAME", "GENDER", "BIRTH_DAY", "RELIABILITY", "USER_CREATE", "TYPE_USER", "DISPLAY_NAME"];
    protected $hidden = ["PASSWORD"];
}
