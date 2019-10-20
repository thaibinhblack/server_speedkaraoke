<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class DetailAttributeRoomModel extends Model
{
    protected $table = "table_detail_attribute_room";
    protected $fillable = ["UUID_ROOM_BAR_KARAOKE", "UUID_ATTRIBUTE_ROOM"];
}
