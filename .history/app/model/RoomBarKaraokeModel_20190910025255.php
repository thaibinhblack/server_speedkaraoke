<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class RoomBarKaraokeModel extends Model
{
    protected $table = "TABLE_ROOM_BAR_KARAOKE";
    protected $fillable = ["UUID_ROOM_BAR_KARAOKE", "UUID_BAR_KARAOKE", "NAME_ROOM_BAR_KARAOKE", "RENT_COST", "CAPACITY", "CONTENT", "USER_CREATE", "STAR_RATING", "NUMBER_RATED", "UER_CREATE"];
}
