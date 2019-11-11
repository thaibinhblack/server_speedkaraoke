<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class BillModel extends Model
{
    protected $table = "table_bill";
    protected $fillable = ["UUID_BILL", "UUID_USER", "UUID_BAR_KARAOKE", "PRICE_BILL", "TOTAL_TIME",
     "RENT_COST", "CODE_PROMOTION", "PAYPAL"];
}
