<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class ProvinceModel extends Model
{
    protected $table = "TABLE_PROVINCE";
    protected $fillable = ["ID_PROVINCE", "NAME_PROVINCE"];
}
