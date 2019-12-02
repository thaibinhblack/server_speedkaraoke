<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\RatingLikeModel;
use App\model\UserModel;
use Illuminate\Support\Str;
class LikeKaraokeMobile extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get("api_token"))->first();
            if($user)
            {
                if($request->has('UUID_ROOM'))
                {
                    $rooms = RatingLikeModel::where([
                        ["tale_rating_like.UUID_USER",$user->UUID_USER],
                        ["tale_rating_like.TYPE",2]
                    ])->join("table_room_bar_karaoke",'tale_rating_like.UUID_ROOM_BAR_KARAOKE','table_room_bar_karaoke.UUID_ROOM_BAR_KARAOKE')
                    ->get();
                    return response()->json($rooms, 200);
                }
                $karaokes = RatingLikeModel::where([
                    ["tale_rating_like.UUID_USER",$user->UUID_USER],
                    ["tale_rating_like.TYPE",2]
                ])
                ->join("table_bar_karaoke","tale_rating_like.UUID_BAR_KARAOKE","table_bar_karaoke.UUID_BAR_KARAOKE")
                ->join("table_province","table_bar_karaoke.ID_PROVINCE","table_province.ID_PROVINCE")
                ->join("table_district","table_bar_karaoke.ID_DISTRICT","table_district.ID_DISTRICT")
                ->orderBy("tale_rating_like.NUMBER_LIKE","DESC")
                ->select("table_bar_karaoke.*","table_province.NAME_PROVINCE","table_district.NAME_DISTRICT")
                ->get();
                return response()->json($karaokes, 200);
            }
        }
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
            //like mobile
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                if($request->has("UUID_BAR_KARAOKE"))
                {
                    $karaoke_like = RatingLikeModel::where([
                        ["UUID_USER" , $user->UUID_USER],
                        [ "UUID_BAR_KARAOKE" , $request->get("UUID_BAR_KARAOKE")],
                        ["TYPE" , 2]
                    ])->first();
                    if($karaoke_like)
                    {
                        RatingLikeModel::where([
                            ["UUID_USER" , $user->UUID_USER],
                            [ "UUID_BAR_KARAOKE" , $request->get("UUID_BAR_KARAOKE")],
                            ["TYPE" , 2]
                        ])->update([
                            "NUMBER_LIKE" => $karaoke_like->NUMBER_LIKE + 1
                        ]);
                        return response()->json($karaoke_like, 200);
                    }
                    $rating = RatingLikeModel::create([
                        "UUID_RATING_LIKE" => Str::uuid(),
                        "UUID_USER" => $user->UUID_USER,
                        "UUID_BAR_KARAOKE" => $request->get("UUID_BAR_KARAOKE"),
                        "TYPE" => 2
                    ]);
                    return response()->json($rating, 200);
                }
                if($request->has('UUID_ROOM_BAR_KARAOKE'))
                {
                    $room_like = RatingLikeModel::where([
                        ["UUID_USER" , $user->UUID_USER],
                        [ "UUID_ROOM_BAR_KARAOKE" , $request->get("UUID_ROOM_BAR_KARAOKE")],
                        ["TYPE" , 2]
                    ])->first();
                    if($room_like)
                    {
                        RatingLikeModel::where([
                            ["UUID_USER" , $user->UUID_USER],
                            [ "UUID_ROOM_BAR_KARAOKE" , $request->get("UUID_ROOM_BAR_KARAOKE")],
                            ["TYPE" , 2]
                        ])->update([
                            "NUMBER_LIKE" => $room_like->NUMBER_LIKE + 1
                        ]);
                        return response()->json($room_like, 200);
                    }
                    $rating = RatingLikeModel::create([
                        "UUID_RATING_LIKE" => Str::uuid(),
                        "UUID_USER" => $user->UUID_USER,
                        "UUID_ROOM_BAR_KARAOKE" => $request->get("UUID_ROOM_BAR_KARAOKE"),
                        "TYPE" => 2
                    ]);
                    return response()->json($rating, 200);
                }
                
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get("api_token"))->first();
            if($request->has("UUID_ROOM_BAR_KARAOKE"))
            {
                $check = RatingLikeModel::where([
                   ["UUID_USER", $user->UUID_USER],
                   ["UUID_ROOM_BAR_KARAOKE",$request->get("UUID_BAR_KARAOKE")]
                ])->first();
                if($check)
                {
                    return response()->json([
                        'success' => true,
                        'rating' => false,
                        'message' => 'Bạn đã đánh giá phòng này!',
                        'status' => 200
                    ], 200);
                }
                return response()->json([
                    'success' => true,
                    'rating' => true,
                    'message' => 'Bạn chưa đánh giá phòng này!',
                    'status' => 200
                ], 200);
            }
        }
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
