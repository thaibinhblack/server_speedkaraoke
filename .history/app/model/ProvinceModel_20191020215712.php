<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class ProvinceModel extends Model
{
    protected $table = "table_province";
    protected $fillable = ["ID_PROVINCE", "NAME_PROVINCE"];
}
