<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class ViewKaraokeModel extends Model
{
    protected $table = "table_view_karaoke";
    protected $fillable = ["UUID_USER", "UUID_BAR_KARAOKE"]
}
