<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class RoomBarKaraokeModel extends Model
{
    protected $table = "table_room_bar_karaoke";
    protected $fillable = ["UUID_ROOM_BAR_KARAOKE", "UUID_BAR_KARAOKE", "NAME_ROOM_BAR_KARAOKE", "IMAGE_ROOM_BAR_KARAOKE",
    "RENT_COST", "CONTENT", "USER_CREATE", "STAR_RATING", "NUMBER_RATED", "USER_CREATE"];
}
