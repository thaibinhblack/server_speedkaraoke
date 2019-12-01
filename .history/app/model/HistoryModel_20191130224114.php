<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class HistoryModel extends Model
{
    protected $table = "table_history_action";
    protected $fillable = ["UUID_HISTORY_ACTION", "UUID_USER", "NAME_HISTORY", "CONTENT_ACTION"];
}
