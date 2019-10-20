<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class DistrictModel extends Model
{
    protected $table = "TABLE_DISTRICT";
    protected $fillable = ["ID_DISTRICT", "ID_PROVINCE", "NAME_DISTRICT"];
}
