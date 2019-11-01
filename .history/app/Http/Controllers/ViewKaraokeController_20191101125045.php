<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\ViewKaraokeModel;
user App\model\UserModel;
class ViewKaraokeController extends Controller
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
        if($request->has("api_token"))
        {
            $user = UserModel::where("USER_TOKEN",$request->get("api_token"))->first();
            if($user)
            {
                $view_karaoke = ViewKaraokeModel::where([
                    ["UUID_USER",$user->UUID_USER],
                    ["UUID_BAR_KARAOKE",$request->get("UUID_BAR_KARAOKE")]
                ])->first();
                if($view_karaoke)
                {
                    $update_view_karaoke = ViewKaraokeModel::where([
                        ["UUID_USER",$user->UUID_USER],
                        ["UUID_BAR_KARAOKE",$request->get("UUID_BAR_KARAOKE")]
                    ])->update([
                        "NUMBER_VIEW" => $view_karaoke->NUMBER_VIEW + 1
                    ]);
                    return response()->json($update_view_karaoke, 200);
                }
                else {
                    $create_view_karaoke = ViewKaraokeModel::create([
                        ["UUID_USER",$user->UUID_USER],
                        ["UUID_BAR_KARAOKE",$request->get("UUID_BAR_KARAOKE")]
                    ]);
                    return response()->json($create_view_karaoke, 200);
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
