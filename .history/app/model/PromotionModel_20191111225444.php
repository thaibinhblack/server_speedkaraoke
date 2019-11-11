<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class PromotionModel extends Model
{
    protected $table = "table_promotion";
    protected $fillable = ["UUID_PROMOTION", "BANNER_PROMOTION", "NAME_PROMOTION", "CONTENT_PROMOTION", 
    "VALUE_SAFE_OFF", "USER_CREATE", "CODE_PROMOTION", "NUMBER_PROMOTION", "USE_PROMOTION",
    "DATE_STARTED", "DATE_END"];
}
