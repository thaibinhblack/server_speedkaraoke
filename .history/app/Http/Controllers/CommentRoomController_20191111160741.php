<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\UserModel;
use App\model\CommentRoomModel;
use Illuminate\Support\Str;
class CommentRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get("api_token"))->first();
            if($user)
            {
                $comment = CommentRoomModel::create([
                    "UUID_COMMENT_ROOM" => Str::uuid(),
                    "UUID_USER" => $user->UUID_USER,
                    "UUID_ROOM_BAR_KARAOKE" => $request->get("UUID_ROOM_BAR_KARAOKE"),
                    "CONTENT_COMMENT" => $request->get("CONTENT_COMMENT")
                ]);
                if($comment)
                {
                    return response()->json($comment, 200);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Không comment được',
                    'status' => 400
                ], 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'Authorizon',
                'status' => 401
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comments = CommentRoomModel::join("table_user","table_comment_room.UUID_USER","table_user.UUID_USER")
        ->where("UUID_ROOM_BAR_KARAOKE",$id)
        ->select("table_user.AVATAR","table_comment_room.*")
        ->orderBy("CREATED_AT","DESC")
        ->get();
        return response()->json($comments, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
