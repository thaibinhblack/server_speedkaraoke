<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class RatingLikeModel extends Model
{
    protected $table = "tale_rating_like";
    protected $fillable = ["UUID_RATING_LIKE", "UUID_USER", "UUID_BAR_KARAOKE", "UUID_ROOM_BAR_KARAOKE", "TYPE", "NUMBER_LIKE"];
}
