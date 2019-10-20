<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class AttributeRoomModel extends Model
{
    protected $table = "table_attribute_room";
    protected $fillable = ["UUID_ATTRIBUTE_ROOM", "NAME_ATTRIBUTE", "CONTENT_ATTRIBUTE", "USER_CREATE"];
}
