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

    protected $table = "table_user";
    protected $fillable = ["UUID_USER", "UUID_RULE", "USERNAME", "PASSWORD", "AVATAR", "EMAIL", "PHONE", 
    "DISPLAY_NAME", "GENDER", "BIRTH_DAY", "ADDRESS", "RELIABILITY","NUMBER_BOOK", "CANCLE_BOOK", "SPEED_COIN",
     "USER_CREATE", "TYPE_USER", "USER_TOKEN"];
    protected $hidden = ["PASSWORD", "USER_TOKEN"];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
