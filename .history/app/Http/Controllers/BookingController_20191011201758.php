<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\BookingModel;
use App\model\UserModel;
use App\model\HistoryModel;
use Illuminate\Support\Str;

class BookingController extends Controller
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
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                // return response()->json($request->all(), 200);
                if($request->has('status'))
                {
                    $bookings = BookingModel::join("table_bar_karaoke","table_booking.UUID_BAR_KARAOKE","table_bar_karaoke.UUID_BAR_KARAOKE")
                    ->join("table_room_bar_karaoke","table_booking.UUID_ROOM_BAR_KARAOKE","table_room_bar_karaoke.UUID_ROOM_BAR_KARAOKE")
                    ->where("UUID_USER",$user->UUID_USER)
                    ->select("table_bar_karaoke.LOGO_BAR_KARAOKE","table_bar_karaoke.NAME_BAR_KARAOKE","table_room_bar_karaoke.NAME_ROOM_BAR_KARAOKE","table_room_bar_karaoke.RENT_COST")->get();
                    return response()->json($bookings, 200);
                }
               
                $bookings = BookingModel::join("table_user","table_booking.UUID_USER","table_user.UUID_USER")
                ->join('table_bar_karaoke','table_booking.UUID_BAR_KARAOKE','table_bar_karaoke.UUID_BAR_KARAOKE')
                ->join("table_room_bar_karaoke","table_booking.UUID_ROOM_BAR_KARAOKE","table_room_bar_karaoke.UUID_ROOM_BAR_KARAOKE")
                ->where("table_booking.UUID_BAR_KARAOKE",$request->get("UUID_BAR_KARAOKE"))
                ->select('table_booking.*','table_user.AVATAR', 'table_user.DISPLAY_NAME','table_user.GENDER', 'table_user.BIRTH_DAY'
                ,'table_user.PHONE', 'table_user.EMAIL','table_user.RELIABILITY', 'table_user.NUMBER_BOOK'
                ,'table_user.CANCLE_BOOK', 'table_user.EMAIL','table_user.RELIABILITY', 'table_user.NUMBER_BOOK'
                ,'table_bar_karaoke.NAME_BAR_KARAOKE', 'table_room_bar_karaoke.NAME_ROOM_BAR_kARAOKE')
                ->get();
                return response()->json($bookings, 200);
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
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                $booking = BookingModel::create([
                    "UUID_BOOKING" => Str::uuid(),
                    "UUID_ROOM_BAR_KARAOKE" => $request->get("UUID_ROOM_BAR_KARAOKE"),
                    "UUID_BAR_KARAOKE" => $request->get("UUID_BAR_KARAOKE"),
                    "UUID_USER" => $user->UUID_USER,
                    "TIME_START" => $request->get("TIME_START")
                ]);
                
                return response()->json($booking, 200);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        
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

    public function check(Request $request,$id)
    {
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                $user_booking =   BookingModel::join("table_user","table_booking.UUID_USER","table_user.UUID_USER")
                ->where("UUID_BOOKING",$id)->select("table_user.EMAIL","table_booking.*")->first();
                BookingModel::where("UUID_BOOKING",$id)->update([
                    "STATUS" => $request->get("status")
                ]);
                // HistoryModel::create([
                //     "UUID_HISTORY" => Str::uuid(),
                //     "UUID_USER" => $user->UUID_USER,
                //     "NAME_HISTORY" => "Đặt phòng karaoke",
                //     "CONTENT_ACTION" => $user->EMAIL.' cập nhật booking của user '.$user_booking->EMAIL
                // ]);
                return response()->json([
                    "success" => true,
                    "message" => "Cập nhật thành công!"
                ], 200);
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
