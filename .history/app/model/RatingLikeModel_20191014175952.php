<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class RatingLikeModel extends Model
{
    protected $table = "table_rating_like";
    protected $fillable = ["UUID_RATING_LIKE", "UUID_USER", "UUID_BAR_KARAOKE", "TYPE"]
}
