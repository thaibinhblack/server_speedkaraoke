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
                $karaokes = RatingLikeModel::join("table_bar_karaoke, tale_rating_like.UUID_BAR_KARAOKE","table_bar_karaoke.UUID_BAR_KARAOKE")
                ->where([
                    ["UUID_USER",$user->UUID_USER],
                    ["TYPE",2]
                ])->get();
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
                $rating = RatingLikeModel::create([
                    "UUID_RATING_LIKE" => Str::uuid(),
                    "UUID_USER" => $user->UUID_USER,
                    "UUID_BAR_KARAOKE" => $request->get("UUID_BAR_KARAOKE"),
                    "TYPE" => 2
                ]);
                return response()->json($rating, 200);
            }
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
        //
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
