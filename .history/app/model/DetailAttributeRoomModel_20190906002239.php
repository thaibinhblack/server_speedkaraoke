<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class DetailAttributeRoomModel extends Model
{
    protected $table = "TABLE_DETAIL_ATTRIBUTE_ROOM";
    protected $fillable = ["UUID_ROOM_BAR_KARAOKE", "UUID_ATTRIBUTE_ROOM"];
}
