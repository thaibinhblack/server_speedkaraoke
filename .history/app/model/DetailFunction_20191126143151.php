<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class DetailFunction extends Model
{
    protected $table = "table_detail_rule_function";
    protected $fillable = ["UUID_RULE", "UUID_FUNCTION"," FUNCTION_VIEW" ," FUNCTION_CREATE", "FUNCTION_EDIT", "FUNCTION_DELETE"];
}
