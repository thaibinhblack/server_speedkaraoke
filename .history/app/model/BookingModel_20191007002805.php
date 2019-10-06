<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class BookingModel extends Model
{
    protected $table = "table_booking";
    protected $fillable = ["UUID_BOOKING", "UUID_USER", "TIME_START", "TIME_END", "STATUS"];
}
