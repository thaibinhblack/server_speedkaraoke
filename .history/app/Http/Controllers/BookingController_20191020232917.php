<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\BookingModel;
use App\model\UserModel;
use App\model\HistoryModel;
use Illuminate\Support\Str;
use DateTime;
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
                   if($request->get('status') == 'all')
                   {
                        $bookings = BookingModel::join("table_bar_karaoke","table_booking.UUID_BAR_KARAOKE","table_bar_karaoke.UUID_BAR_KARAOKE")
                        ->join("table_room_bar_karaoke","table_booking.UUID_ROOM_BAR_KARAOKE","table_room_bar_karaoke.UUID_ROOM_BAR_KARAOKE")
                        ->where("UUID_USER",$user->UUID_USER)
                        ->select("table_booking.*","table_bar_karaoke.LOGO_BAR_KARAOKE","table_bar_karaoke.NAME_BAR_KARAOKE","table_bar_karaoke.URL_SAFE","table_room_bar_karaoke.NAME_ROOM_BAR_KARAOKE","table_room_bar_karaoke.RENT_COST")->get();
                        return response()->json($bookings, 200);
                   }
                   else if($request->get('status') == 'check')
                   {
                        $other_booking = BookingModel::where([
                            ["UUID_ROOM_BAR_KARAOKE",$request->get("UUID_ROOM_BAR_KARAOKE")],
                            ["STATUS",1]
                        ])->first();
                        if(!$other_booking)
                        {
                            $booking = BookingModel::where([
                                [ "UUID_USER", $user->UUID_USER],
                                [ "UUID_ROOM_BAR_KARAOKE",$request->get("UUID_ROOM_BAR_KARAOKE")]
                             ])
                             ->whereIn('STATUS', [0,1])->orderBy('CREATED_AT','desc')->first();
                             if($booking)
                             {
                                 if($booking->STATUS == 0)
                                 {
                                    return response()->json([
                                        'success' => true,
                                        'booking' => false,
                                        'message' => 'Đang chờ nhận phòng',
                                        'data' => $booking
                                     ], 200);
                                 }
                                 return response()->json([
                                    'success' => true,
                                    'booking' => false,
                                    'message' => 'Đã đặt phòng',
                                    'data' => $booking
                                 ], 200);
                             }
                            return response()->json([
                                'success' => true,
                                'booking' => true,
                                'message' => 'Chưa có người đặt phòng'
                             ], 200);
                        }
                        return response()->json([
                            'success' => true,
                            'booking' => true,
                            'message' => 'Phòng đã được đặt',
                            'data' => $other_booking
                         ], 200);
                   }
                   else {
                        $bookings = BookingModel::join("table_bar_karaoke","table_booking.UUID_BAR_KARAOKE","table_bar_karaoke.UUID_BAR_KARAOKE")
                        ->join("table_room_bar_karaoke","table_booking.UUID_ROOM_BAR_KARAOKE","table_room_bar_karaoke.UUID_ROOM_BAR_KARAOKE")
                        ->where([
                            ["UUID_USER",$user->UUID_USER],
                            ['STATUS',$request->get('status')]])
                        ->select("table_booking.*","table_bar_karaoke.LOGO_BAR_KARAOKE","table_bar_karaoke.NAME_BAR_KARAOKE","table_room_bar_karaoke.NAME_ROOM_BAR_KARAOKE","table_room_bar_karaoke.RENT_COST")
                        ->orderBy("CREATED_AT",'DESC')
                        ->get();
                        return response()->json($bookings, 200);
                   }
                }
               
                $bookings = BookingModel::join("table_user","table_booking.UUID_USER","table_user.UUID_USER")
                ->join('table_bar_karaoke','table_booking.UUID_BAR_KARAOKE','table_bar_karaoke.UUID_BAR_KARAOKE')
                ->join("table_room_bar_karaoke","table_booking.UUID_ROOM_BAR_KARAOKE","table_room_bar_karaoke.UUID_ROOM_BAR_KARAOKE")
                ->where("table_booking.UUID_BAR_KARAOKE",$request->get("UUID_BAR_KARAOKE"))
                ->select('table_booking.*','table_user.AVATAR', 'table_user.DISPLAY_NAME','table_user.GENDER', 
                'table_user.BIRTH_DAY','table_user.PHONE', 'table_user.EMAIL','table_user.RELIABILITY', 
                'table_user.NUMBER_BOOK','table_user.CANCLE_BOOK', 'table_user.EMAIL','table_user.RELIABILITY',
                'table_user.NUMBER_BOOK','table_bar_karaoke.NAME_BAR_KARAOKE', 
                'table_room_bar_karaoke.NAME_ROOM_BAR_KARAOKE', 'table_room_bar_karaoke.RENT_COST')
                ->orderBy('CREATED_AT','DESC')
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
                $check_booking = BookingModel::where([
                    ["UUID_USER",$user->UUID_USER],
                    ["UUID_ROOM_BAR_KARAOKE",$request->get("UUID_ROOM_BAR_KARAOKE")],
                    ["STATUS",0]
                ])->first();
                if(!$check_booking)
                {
                    $booking = BookingModel::create([
                        "UUID_BOOKING" => Str::uuid(),
                        "UUID_ROOM_BAR_KARAOKE" => $request->get("UUID_ROOM_BAR_KARAOKE"),
                        "UUID_BAR_KARAOKE" => $request->get("UUID_BAR_KARAOKE"),
                        "UUID_USER" => $user->UUID_USER,
                        "TIME_START" => $request->get("TIME_START")
                    ]);
                    
                    return response()->json([
                        "success" => true,
                        "message" => "Booking thành công",
                        "data" => $booking,
                        "status" => 200
                    ], 200);
                }
                return response()->json([
                    "success" => false,
                    "message" => "Bạn đã booking phòng này!",
                    "status" => 400
                ], 200);
                
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
        if($request->has('status'))
        {
            $booking = BookingModel::where([
                ["STATUS" ,$request->get("status")],
                ["UUID_ROOM_BAR_KARAOKE",$id]
            ])->first();
            if($booking)
            {
                return response()->json([
                    "success" => true,
                    "message" => "Có tồn tại booking",
                    "data" => $booking
                ], 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'Không có booking'
            ], 200);
        }
        $bookings = BookingModel::where("UUID_ROOM_BAR_KARAOKE", $id)->get();
        return response()->json([
            'success' => false,
            'message' => 'List booking theo room',
            'data' => $bookings
        ], 200);
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
                if($request->get('status') == 2)
                {
                  
                    $date =  new DateTime();
                   
                    
                    $booking = BookingModel::where("UUID_BOOKING",$id)->first();
                    $start = new DateTime($booking->TIME_START);
                   
                    if($date->format('H') < $start->format('H'))
                    {
                        $end = ($date->format('H') + 24)*60 + $date->format('i');
                    }
                    else {
                        $end = $date->format('H')*60 + $date->format('i');
                    }
                    $start = $start->format('H')*60 + $start->format('i');
                    BookingModel::where("UUID_BOOKING",$id)->update([
                        "STATUS" => $request->get("status"),
                        'TIME_END' => $date->format('H:i:s'),
                        'TOTAL_TIME' => $end - $start
                    ]);
                    $booking = BookingModel::where("UUID_BOOKING",$id)->first();
                    $user_booking = UserModel::where("UUID_USER",$booking->UUID_USER)->first();
                    UserModel::where("UUID_USER",$booking->UUID_USER)->update([
                        'RELIABILITY' => $user_booking->RELIABILITY + 2,
                        'NUMBER_BOOK' => $user_booking->NUMBER_BOOK + 1 
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Cập nhật booking thành công!'
                    ], 200);
                }
                else {
                    BookingModel::where("UUID_BOOKING",$id)->update([
                        "STATUS" => $request->get("status")
                    ]);
                }
                $user_booking =   BookingModel::join("table_user","table_booking.UUID_USER","table_user.UUID_USER")
                ->where("UUID_BOOKING",$id)->select("table_user.EMAIL","table_booking.*")->first();
                
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
