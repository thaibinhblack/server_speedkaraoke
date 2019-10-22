<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class DetailPromotionKaraokeModel extends Model
{
    protected $table = "table_detail_promotion_karaoke";
    protected $fillable = ["UUID_BAR_KARAOKE", "UUID_PROMOTION"];
}
