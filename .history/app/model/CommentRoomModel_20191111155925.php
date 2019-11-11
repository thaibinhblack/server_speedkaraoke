<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class CommentRoomModel extends Model
{
    protected $table = "table_comment_room";
    protected $fillable = ["UUID_COMMENT_ROOM", "UUID_ROOM_BAR_KARAOKE","UUID_USER","CONTENT_ROOM"]
}
