<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class ManagerKaraoke extends Model
{
    protected $table = "table_detail_manager_bar_karaoke";
    protected $fillable = ["UUID_USER", "UUID_BAR_KARAOKE", "USER_CREATE"];
}
