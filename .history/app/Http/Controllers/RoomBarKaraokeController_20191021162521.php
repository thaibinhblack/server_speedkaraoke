<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\RoomBarKaraokeModel;
use App\model\BarKaraokeModel;
use Illuminate\Support\Str;
use App\model\UserModel;
use App\model\HistoryModel;
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
        else if($request->has('URL_SAFE'))
        {
            $karaoke = BarKaraokeModel::where("URL_SAFE",$request->get('URL_SAFE'))->first();
            // return response()->json($karaoke, 200);
            if($karaoke)
            {   
                $room = RoomBarKaraokeModel::where([
                    ['UUID_BAR_KARAOKE',$karaoke->UUID_BAR_KARAOKE],
                    ['NAME_ROOM_BAR_KARAOKE',$request->get('NAME_ROOM_BAR_KARAOKE')]
                    ])->first();
                return response()->json($room, 200);
            }
        }   
    
        
        return response()->json($request->all(), 200);
        
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
                    "CONTENT" => $request->get("CONTENT"),
                    "STAR_RATING" => 5,
                    "NUMBER_RATED" => 1,
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
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                $room = RoomBarKaraokeModel::where("UUID_ROOM_BAR_KARAOKE",$id)->first();
                if($request->has('IMAGE_ROOM'))
                {
                    
                    $file = $request->file("IMAGE_ROOM");
                    $file->move(public_path().'/upload/karaoke/', $file->getClientOriginalName());
                    RoomBarKaraokeModel::where("UUID_ROOM_BAR_KARAOKE",$id)->update([
                        "IMAGE_ROOM_BAR_KARAOKE" => '/upload/karaoke/'.$file->getClientOriginalName(),
                    ]);
                    
                }
                
                RoomBarKaraokeModel::where("UUID_ROOM_BAR_KARAOKE",$id)->update([
                    "NAME_ROOM_BAR_KARAOKE" => $request->get("NAME_ROOM_BAR_KARAOKE"),
                    "RENT_COST" => $request->get("RENT_COST"),
                    "CONTENT" => $request->get("CONTENT")
                ]);
                HistoryModel::create([
                    "UUID_HISTORY_ACTION" => Str::uuid(),
                    "UUID_USER" => $user->UUID_USER,
                    "NAME_HISTORY" => "Room",
                    "CONTENT_ACTION" => $user->EMAIL.' cập nhật ảnh đại diện cho '.$room->NAME_ROOM_BAR_KARAOKE
                ]);
                return response()->json('success', 200);
            }
        }
        
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
