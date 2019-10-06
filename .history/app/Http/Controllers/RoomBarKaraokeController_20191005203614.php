<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\RoomBarKaraokeModel;
use Illuminate\Support\Str;
use App\model\UserModel;
class RoomBarKaraokeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has('UUID_BAR_KARAOKE'))
        {
            $rooms = RoomBarKaraokeModel::where('UUID_BAR_KARAOKE',$request->get('UUID_BAR_KARAOKE'))->orderBy('CREATED_AT','asc')->get();
            return response()->json($rooms, 200);
        }
            
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                $room = RoomBarKaraokeModel::create([
                    "UUID_ROOM_BAR_KARAOKE" => Str::uuid(),
                    "UUID_BAR_KARAOKE" => $request->get("UUID_BAR_KARAOKE"),
                    "NAME_ROOM_BAR_KARAOKE" => $request->get("NAME_ROOM_BAR_KARAOKE"),
                    "RENT_COST" => $request->get("RENT_COST"),
                    "CAPACITY" => $request->get("CAPACITY"),
                    "CONTENT" => $request->get("CONTENT"),
                    "STAR_RATING" => 0,
                    "NUMBER_RATED" => 0,
                    "USER_CREATE" => $user->EMAIL
                ]);
                return response()->json($room, 200);
            }
            return response()->json('error', 404);
        }
        
        return response()->json('error', 401);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $room = RoomBarKaraokeModel::where("UUID_ROOM_BAR_KARAOKE",$id)->first();
        return response()->json($room, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        $room = RoomBarKaraokeModel::where("UUID_ROOM_BAR_KARAOKE",$id)->update($request->all());
        return response()->json($room, 200);
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
