<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class BookingModel extends Model
{
    protected $table = "table_booking";
    protected $fillable = ["UUID_BOOKING", "UUID_ROOM_BAR_KARAOKE", "UUID_BAR_KARAOKE", "UUID_BILL", "UUID_USER", "TIME_START", "DATE_BOOK",
     "TIME_END", "TOTAL_TIME", "STATUS"];
}
