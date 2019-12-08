<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\RoomBarKaraokeModel;
use App\model\BarKaraokeModel;
use Illuminate\Support\Str;
use App\model\UserModel;
use App\model\HistoryModel;
use App\model\RatingLikeModel;
class RoomBarKaraokeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has('UUID_ROOM_BAR_KARAOKE'))
        {
            
            $rooms = RoomBarKaraokeModel::where('UUID_ROOM_BAR_KARAOKE',$request->get('UUID_ROOM_BAR_KARAOKE'))->orderBy('CREATED_AT','asc')->get();
            return response()->json($rooms, 200);
        }
        else if($request->has('URL_SAFE'))
        {
            $karaoke = BarKaraokeModel::where("URL_SAFE",$request->get('URL_SAFE'))->first();
            // return response()->json($karaoke, 200);
            if($karaoke)
            {   
                $room = RoomBarKaraokeModel::where([
                    ['UUID_ROOM_BAR_KARAOKE',$karaoke->UUID_ROOM_BAR_KARAOKE],
                    ['NAME_ROOM_BAR_KARAOKE',$request->get('NAME_ROOM_BAR_KARAOKE')]
                    ])->first();
                return response()->json($room, 200);
            }
        }  

        else if($request->has("UUID_BAR_KARAOKE"))
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
                $file = $request->file("IMAGE_ROOM_BAR_KARAOKE");
                $file->move(public_path().'/upload/karaoke/', $file->getClientOriginalName());
                
                $room = RoomBarKaraokeModel::create([
                    "UUID_ROOM_BAR_KARAOKE" => Str::uuid(),
                    "UUID_BAR_KARAOKE" => $request->get("UUID_BAR_KARAOKE"),
                    "NAME_ROOM_BAR_KARAOKE" => $request->get("NAME_ROOM_BAR_KARAOKE"),
                    "RENT_COST" => $request->get("RENT_COST"),
                    "CONTENT" => $request->get("CONTENT"),
                    "CAPACITY" => $request->get("CAPACITY"),
                    "NEW_ROOM" => $request->get("NEW_ROOM"),
                    "DESIGN" => $request->get("DESIGN"),
                    "EVENT" => $request->get("EVENT"),
                    "STAR_RATING" => 5,
                    "NUMBER_RATED" => 1,
                    "USER_CREATE" => $user->EMAIL
                ]);
                return response()->json($room, 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'User không tồn tại!',
                'status'=> 404
            ], 200);
        }
        
        return response()->json([
            'succes' => false,
            'message' => 'Authorizon',
            'status' => 401
        ], 200);
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
                
                if($request->has("NAME_ROOM_BAR_KARAOKE"))
                {
                    RoomBarKaraokeModel::where("UUID_ROOM_BAR_KARAOKE",$id)->update([
                        "NAME_ROOM_BAR_KARAOKE" => $request->get("NAME_ROOM_BAR_KARAOKE"),
                        "NEW_ROOM" => $request->get("NEW_ROOM"),
                        "DESIGN" => $request->get("DESIGN"),
                        "EVENT" => $request->get("EVENT"),
                        "CÂPCITY" => $request->get("CAPACITY"),
                        "RENT_COST" => $request->get("RENT_COST"),
                        "CONTENT" => $request->get("CONTENT")
                    ]);
                }
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
    
    public function view($id)
    {
        $room = RoomBarKaraokeModel::where("UUID_ROOM_BAR_KARAOKE",$id)->first();
        RoomBarKaraokeModel::where("UUID_ROOM_BAR_KARAOKE",$id)->update([
            "VIEW_ROOM" => $room->VIEW_ROOM + 1
        ]);
        return response()->json('success', 200);
    }

    public function rating(Request $request,$id)
    {
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get("api_token"))->first();
            if($user)
            {   
                $rating = RatingLikeModel::where([
                    ["UUID_ROOM_BAR_KARAOKE",$id],
                    ["UUID_USER",$user->UUID_USER],
                    ["TYPE",1]
                ])->first();
                if($request->has('rating'))
                {
                    
                    if(!$rating)
                    {
                        $room = RoomBarKaraokeModel::where("UUID_ROOM_BAR_KARAOKE",$id)->first();
                        $rating = $room->STAR_RATING * $room->NUMBER_RATED;
                        $rating = $rating + (float)$request->get('rating');
                        $rating = $rating / ($room->NUMBER_RATED + 1);
                        RoomBarKaraokeModel::where("UUID_ROOM_BAR_KARAOKE",$id)->update([
                            'STAR_RATING' => $rating,
                            'NUMBER_RATED' => $room->NUMBER_RATED + 1
                        ]);
                        RatingLikeModel::create([
                            "UUID_RATING_LIKE" => Str::uuid(),
                            "UUID_USER" => $user->UUID_USER,
                            "UUID_ROOM_BAR_KARAOKE" => $room->UUID_ROOM_BAR_KARAOKE
                        ]);
                        return response()->json([
                            'success' => true,
                            'message' => 'Bạn vừa đánh giá karaoke'
                        ], 200);
                    }
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đã đanh giá chi nhánh karaoke này!'
                    ], 200);
                }
                else if($rating)
                {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đã đánh giá chi nhánh karaoke này'
                    ], 200);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Bạn chưa đánh giá chi nhánh karaoke này'
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
