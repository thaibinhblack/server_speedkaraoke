<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class BarKaraokeModel extends Model
{
    protected $table = "TABLE_BAR_KARAOKE";
    protected $fillable = ["UUID_BAR_KARAOKE","ID_DISTRICT", "ID_PROVINCE", "LOGO_BAR_KARAOKE", "NAME_BAR_KARAOKE", "ADDRESS_BAR_KARAOKE", "EMAIL_BAR_KARAOKE", "PHONE_BAR_KARAOKE", "USER_CREATE", "STAR_RATING", "NUMBER_REATED", "URL_SAFE"];
}
