<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class ImageModel extends Model
{
    protected $table = "table_image";
    protected $fillable = ["UUID_BAR_KARAOKE", "UUID_ROOM_BAR_KARAOKE", "UUID_MENU", "UUID_IMAGE", "URL_IMAGE", "TYPE"];
}
