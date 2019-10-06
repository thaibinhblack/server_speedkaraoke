<?php

namespace App\model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\Model;

class UserModel  extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = "TABLE_USER";
    protected $fillable = ["UUID_USER", "UUID_RULE", "USERNAME", "PASSWORD", "AVATAR", "EMAIL", "PHONE", "DISPLAY_NAME", "GENDER", "BIRTH_DAY", "RELIABILITY", "USER_CREATE", "TYPE_USER", "DISPLAY_NAME", "USER_TOKEN"];
    protected $hidden = ["PASSWORD", "USER_TOKEN"];
}
