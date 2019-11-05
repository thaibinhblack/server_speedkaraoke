<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class CommentKaraokeModel extends Model
{
    protected $table = "table_comment_karaoke";
    protected $fillable = ["UUID_COMMENT_KARAOKE","UUID_USER", "UUID_BAR_KARAOKE", "CONTENT_COMMENT"];
}
