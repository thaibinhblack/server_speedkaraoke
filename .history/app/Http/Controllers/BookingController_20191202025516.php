<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\BookingModel;
use App\model\UserModel;
use App\model\HistoryModel;
use App\model\BarKaraokeModel;
use Illuminate\Support\Str;
use DateTime;
use Twilio\Rest\Client;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function token($token)
    {
        $user = UserModel::where("USER_TOKEN",$token)->first();
        if($user)
        {
            return true;
        }
        return false;
    }
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
                        ->select("table_booking.*","table_bar_karaoke.LOGO_BAR_KARAOKE","table_bar_karaoke.NAME_BAR_KARAOKE","table_bar_karaoke.URL_SAFE","table_room_bar_karaoke.NAME_ROOM_BAR_KARAOKE","table_room_bar_karaoke.RENT_COST")
                        ->orderBy("table_booking.CREATED_AT","DESC")
                        ->get();
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
                                'success' => false,
                                'booking' => false,
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
                ->join('table_detail_manager_bar_karaoke',"table_booking.UUID_BAR_KARAOKE", "table_detail_manager_bar_karaoke.UUID_BAR_KARAOKE")
                // ->where("table_booking.UUID_BAR_KARAOKE",$request->get("UUID_BAR_KARAOKE"))
                // ->join('table_detail_manager_bar_karaoke,"table_bar_karaoke.UUID_BAR_KARAOKE","table_detail_manager_bar_karaoke.UUID_BAR_KARAOKE')
                ->where("table_detail_manager_bar_karaoke.UUID_USER",$user->UUID_USER)
                ->select('table_booking.*','table_user.AVATAR', 'table_user.DISPLAY_NAME','table_user.GENDER', 
                'table_user.BIRTH_DAY','table_user.PHONE', 'table_user.EMAIL','table_user.RELIABILITY', 
                'table_user.NUMBER_BOOK','table_user.CANCLE_BOOK', 'table_user.EMAIL','table_user.RELIABILITY',
                'table_user.NUMBER_BOOK','table_bar_karaoke.NAME_BAR_KARAOKE', 
                'table_room_bar_karaoke.NAME_ROOM_BAR_KARAOKE', 'table_room_bar_karaoke.RENT_COST')
                ->orderBy('CREATED_AT','DESC')
                ->get();
                return response()->json($bookings, 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'Not Found!',
                'status' => 404
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Authorizon!',
            'status' => 401
        ], 200);
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

    public function them_truc_tiep(Request $request)
    {
        $uuid_user = Str::uuid();
        UserModel::create([
            "UUID_USER" => $uuid_user,
            "DISPLAY_NAME" => $request->get("DISPLAY_NAME"),
            "PHONE_USER" => $request->get("DISPLAY_NAME")
        ]);
        $booking = BookingModel::create([
            "UUID_BOOKING" => Str::uuid(),
            "TIME_START" => $request->get("TIME_START"),
            "DATE_BOOK" => $request->get("DATE_BOOK"),
            "UUID_BAR_KARAOKE" => $request->get("UUID_BAR_KARAOKE"),
            "UUID_ROOM_BAR_KARAOKE" => $request->get("UUID_ROOM_BAR_KARAOKE"),
            "UUID_USER" => $request->get("UUID_USER"),
        ])
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
    public function duyet(Request $request, $id)
    {
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                // $karaoke = BarKaraokeModel::where("UUID_BAR_KARAOKE",$request->get("UUID_BAR_KARAOKE"))->first();
                $booking = BookingModel::join('table_user','table_booking.UUID_USER','table_user.UUID_USER')
                ->where("UUID_BOOKING",$id)->first();
                $sid = 'AC4db4f33d4ee7ca06e6dd7b06f9c72274';
                $token = 'c98c668873c3404720c2b4228e302d6c';
                $client = new Client($sid, $token);
                $result  = $client->messages->create(
                    // the number you'd like to send the message to
                    '+84'.$booking->PHONE,
                    array(
                        // A Twilio phone number you purchased at twilio.com/console
                        'from' => '+12055189442',
                        // the body of the text message you'd like to send
                        'body' => 'Ban da duoc duyet nhan phong, xin vui long lai chi nhanh vao luc .'.$booking->TIME_START.', '.$booking->DATE_BOOK.' de nhan phong', 
                    )
                );
                BookingModel::where("UUID_BOOKING",$id)->update([
                    "STATUS" => 1
                ]);
                $booking = BookingModel::join("table_user","table_booking.UUID_USER","table_user.UUID_USER")
                ->where("UUID_BOOKING",$id)->first();

                HistoryModel::create([
                    "UUID_HISTORY_ACTION" => Str::uuid(),
                    "UUID_USER" => $user->UUID_USER,
                    "NAME_HISTORY" => 'Duyệt đặt phòng',
                    "CONTENT_ACTION" => $user->EMAIL.' đã duyệt đặt phòng cho khách hàng '.$booking->EMAIL
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Duyệt phòng thành công',
                    'status' => $booking
                ], 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền thực hiện chức năng này',
                'status' => 404
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Không có quyền thực hiện chức năng này',
            'status' => 401
        ], 200);
    }
    public function huy(Request $request, $id)
    {
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                BookingModel::where("UUID_BOOKING",$id)->update([
                    "STATUS" => 5
                ]);
                $user_booking = BookingModel::join("table_user","table_booking.UUID_USER","table_booking.UUID_USER")
                ->where("UUID_BOOKING",$id)->select("table_booking.UUID_BOOKING","table_user.UUID_USER","table_user.CANCLE_BOOK")->first();
                UserModel::where("UUID_USER",$user_booking->UUID_USER)->update([
                    "CANCLE_BOOK" => $user_booking->CANCLE_BOOK + 1,
                ]);
                $booking = BookingModel::join("table_user","table_booking.UUID_USER","table_user.UUID_USER")
                ->where("UUID_BOOKING",$id)->first();

                HistoryModel::create([
                    "UUID_HISTORY_ACTION" => Str::uuid(),
                    "UUID_USER" => $user->UUID_USER,
                    "NAME_HISTORY" => 'Hủy đặt phòng',
                    "CONTENT_ACTION" => $user->EMAIL.' đã hủy đặt phòng của khách hàng '.$booking->EMAIL
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Hủy đặt phòng thành công',
                    'status' => $booking
                ], 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền thực hiện chức năng này',
                'status' => 404
            ], 200);
        } 
        return response()->json([
            'success' => false,
            'message' => 'Không có quyền thực hiện chức năng này',
            'status' => 401
        ], 200);
    }

    public function nhan(Request $request, $id)
    {
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                $date =  new DateTime();
                   
                    
                $booking = BookingModel::where("UUID_BOOKING",$id)->first();
                BookingModel::where("UUID_BOOKING",$id)->update([
                    "STATUS" => 2,
                    "TIME_START" =>  $date->format('H').':'. $date->format('i')
                ]);
                $booking = BookingModel::join("table_user","table_booking.UUID_USER","table_user.UUID_USER")
                ->where("UUID_BOOKING",$id)->first();

                HistoryModel::create([
                    "UUID_HISTORY_ACTION" => Str::uuid(),
                    "UUID_USER" => $user->UUID_USER,
                    "NAME_HISTORY" => 'Check nhận phòng',
                    "CONTENT_ACTION" => $user->EMAIL.' đã cho khách hàng '.$booking->EMAIL. ' nhận phòng'
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Nhận phòng thành công',
                    'status' => 200
                ], 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền thực hiện chức năng này',
                'status' => 404
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Không có quyền thực hiện chức năng này',
            'status' => 401
        ], 200);
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

    public function booking($id,Request $request)
    {
        if($request->has('api_token'))
        {
            $booking = BookingModel::where("table_booking.UUID_BOOKING", $id)
                ->join('table_bar_karaoke','table_booking.UUID_BAR_KARAOKE','table_bar_karaoke.UUID_BAR_KARAOKE')
                ->join("table_room_bar_karaoke","table_booking.UUID_ROOM_BAR_KARAOKE","table_room_bar_karaoke.UUID_ROOM_BAR_KARAOKE")
                ->select('table_booking.*','table_bar_karaoke.NAME_BAR_KARAOKE',
                'table_room_bar_karaoke.NAME_ROOM_BAR_KARAOKE', 'table_room_bar_karaoke.RENT_COST')->first();
            return response()->json($booking, 200);
        }
    }
    public function sms(Request $request)
    {
        $sid = 'AC4db4f33d4ee7ca06e6dd7b06f9c72274';
        $token = 'c98c668873c3404720c2b4228e302d6c';
        $client = new Client($sid, $token);
        $result  = $client->messages->create(
            // the number you'd like to send the message to
            '+84833615707',
            array(
                // A Twilio phone number you purchased at twilio.com/console
                'from' => '+12055189442',
                // the body of the text message you'd like to send
                'body' => 'So dien thoai cua ban da bi khoa'
            )
        );
        return response()->json($result, 200);
    }

    public function thanhtoan(Request $request)
    {
        if($request->has('api_token'))
        {
            $booking = BookingModel::where("UUID_BOOKING",$id)->first();
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
                // $karaoke = BarKaraokeModel::where("UUID_BAR_KARAOKE",$booking->UUID_BAR_KARAOKE)->first();
                // $sid = 'ACcd6bb7cabf87808423aa180a9e1acc49';
                // $token = 'b4cfc1ed2a215abc88db477577001447';
                // $client = new Client($sid, $token);
                // $result  = $client->messages->create(
                //     // the number you'd like to send the message to
                //     '+84'.$karaoke->PHONE_BAR_KARAOKE,
                //     array(
                //         // A Twilio phone number you purchased at twilio.com/console
                //         'from' => '+17752009952',
                //         // the body of the text message you'd like to send
                //         'body' => 'KHACH HANG YEU CAU THANH TOAN!'
                //     )
                // );
            $date =  new DateTime();    
            
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
                "STATUS" => 2,
                'TIME_END' => $date->format('H:i:s'),
                'TOTAL_TIME' => $end - $start
            ]);
            $booking = BookingModel::where("UUID_BOOKING",$id)
                ->join('table_user','table_booking.UUID_USER','table_user.UUID_USER')
                ->join('table_bar_karaoke','table_booking.UUID_BAR_KARAOKE','table_bar_karaoke.UUID_BAR_KARAOKE')
                ->join("table_room_bar_karaoke","table_booking.UUID_ROOM_BAR_KARAOKE","table_room_bar_karaoke.UUID_ROOM_BAR_KARAOKE")
                ->select('table_booking.*','table_user.AVATAR', 'table_user.DISPLAY_NAME','table_user.GENDER', 
                'table_user.BIRTH_DAY','table_user.PHONE', 'table_user.EMAIL','table_user.RELIABILITY', 
                'table_user.NUMBER_BOOK','table_user.CANCLE_BOOK', 'table_user.EMAIL','table_user.RELIABILITY',
                'table_user.NUMBER_BOOK','table_bar_karaoke.NAME_BAR_KARAOKE', 
                'table_room_bar_karaoke.NAME_ROOM_BAR_KARAOKE', 'table_room_bar_karaoke.RENT_COST')
                ->first();
            $user_booking = UserModel::where("UUID_USER",$booking->UUID_USER)->first();
            UserModel::where("UUID_USER",$booking->UUID_USER)->update([
                'RELIABILITY' => $user_booking->RELIABILITY + 2,
                'NUMBER_BOOK' => $user_booking->NUMBER_BOOK + 1 
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật booking thành công!',
                'result' => $booking
            ], 200);
        }
    }

    public function bookingmobile(Request $request)
    {
        $check = $this->token($request->get('api_token'));
        if($check)
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            $karaoke = BarKaraokeModel::where("UUID_BAR_KARAOKE",$request->get("UUID_BAR_KARAOKE"))->first();
            $sid = 'AC4db4f33d4ee7ca06e6dd7b06f9c72274';
            $token = 'c98c668873c3404720c2b4228e302d6c';
            $client = new Client($sid, $token);
            $result  = $client->messages->create(
                // the number you'd like to send the message to
                '+84'.$karaoke->PHONE_BAR_KARAOKE,
                array(
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => '+12055189442',
                    // the body of the text message you'd like to send
                    'body' => 'Co khach hang dat phong ben chi nhanh '.$karaoke->NAME_BAR_KARAOKE.' cua ban!'
                )
            );
            UserModel::where("UUID_USER",$user->UUID_USER)->update([
                'NUMBER_BOOK' => $user->NUMBER_BOOK + 1
            ]);
            $booking = BookingModel::create([
                "UUID_BOOKING" => Str::uuid(),
                "UUID_ROOM_BAR_KARAOKE" => $request->get("UUID_ROOM_BAR_KARAOKE"),
                "UUID_BAR_KARAOKE" => $request->get("UUID_BAR_KARAOKE"),
                "UUID_USER" => $user->UUID_USER,
                "TIME_START" => $request->get("TIME_START"),
                "DATE_BOOK" => $request->get("DATE_BOOK")
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Đặt phòng thành công',
                'result' => $booking
            ], 200);
        }
    }

    public function paypal(Request $request,$id)
    {
        $check = $this->token($request->get('api_token'));
        if($check)
        {
            $booking = BookingModel::where("UUID_BOOKING",$id)->first();
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
                // $karaoke = BarKaraokeModel::where("UUID_BAR_KARAOKE",$booking->UUID_BAR_KARAOKE)->first();
                // $sid = 'ACcd6bb7cabf87808423aa180a9e1acc49';
                // $token = 'b4cfc1ed2a215abc88db477577001447';
                // $client = new Client($sid, $token);
                // $result  = $client->messages->create(
                //     // the number you'd like to send the message to
                //     '+84'.$karaoke->PHONE_BAR_KARAOKE,
                //     array(
                //         // A Twilio phone number you purchased at twilio.com/console
                //         'from' => '+17752009952',
                //         // the body of the text message you'd like to send
                //         'body' => 'KHACH HANG YEU CAU THANH TOAN!'
                //     )
                // );
            $date =  new DateTime();    
            
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
                "STATUS" => 2,
                'TIME_END' => $date->format('H:i:s'),
                'TOTAL_TIME' => $end - $start
            ]);
            $booking = BookingModel::where("UUID_BOOKING",$id)
                ->join('table_user','table_booking.UUID_USER','table_user.UUID_USER')
                ->join('table_bar_karaoke','table_booking.UUID_BAR_KARAOKE','table_bar_karaoke.UUID_BAR_KARAOKE')
                ->join("table_room_bar_karaoke","table_booking.UUID_ROOM_BAR_KARAOKE","table_room_bar_karaoke.UUID_ROOM_BAR_KARAOKE")
                ->select('table_booking.*','table_user.AVATAR', 'table_user.DISPLAY_NAME','table_user.GENDER', 
                'table_user.BIRTH_DAY','table_user.PHONE', 'table_user.EMAIL','table_user.RELIABILITY', 
                'table_user.NUMBER_BOOK','table_user.CANCLE_BOOK', 'table_user.EMAIL','table_user.RELIABILITY',
                'table_user.NUMBER_BOOK','table_bar_karaoke.NAME_BAR_KARAOKE', 
                'table_room_bar_karaoke.NAME_ROOM_BAR_KARAOKE', 'table_room_bar_karaoke.RENT_COST')
                ->first();
            $user_booking = UserModel::where("UUID_USER",$booking->UUID_USER)->first();
            UserModel::where("UUID_USER",$booking->UUID_USER)->update([
                'RELIABILITY' => $user_booking->RELIABILITY + 2,
                'NUMBER_BOOK' => $user_booking->NUMBER_BOOK + 1 
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật booking thành công!',
                'result' => $booking
            ], 200);
        }
    }
    public function checkmobile(Request $request)
    {
        $check = $this->token($request->get('api_token'));
        if($check)
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            $booking = BookingModel::where([
                ["UUID_USER", $user->UUID_USER],
                ["UUID_ROOM_BAR_KARAOKE",$request->get("UUID_ROOM_BAR_KARAOKE")]
            ])->orderBy("CREATED_AT","DESC")->first();
            if($booking)
            {   
                return response()->json($booking, 200);
            }
            return response()->json([
                'message' => 'Chưa có người đặt',
                'STATUS' => -1
            ], 200);
            
        }
        else {
            $booking = BookingModel::where("UUID_ROOM_BAR_KARAOKE",$request->get("UUID_ROOM_BAR_KARAOKE"))
            ->orderBy("CREATED_AT","DESC")->first();
            if($booking)
            {   
                return response()->json($booking, 200);
            }
            return response()->json([
                'message' => 'Chưa có người đặt',
                'STATUS' => -1
            ], 200);
        }
    }

    public function cancle(Request $request)
    {
        $check = $this->token($request->get('api_token'));
        if($check)
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            UserModel::where("USER_TOKEN",$request->get('api_token'))->update([
                "CANCLE_BOOK" => $user->CANCLE_BOOK + 1,
                "RELIABILITY" => $user->RELIABILITY - 1
            ]);
            $booking = BookingModel::where([
                ["UUID_USER",$user->UUID_USER],
                ["UUID_ROOM_BAR_KARAOKE",$request->get('UUID_ROOM_BAR_KARAOKE')],
                ["STATUS",0]
            ])->update([
                'STATUS' => 4
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Bạn vừa hủy đặt phòng',
                'result' => $booking
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Lỗi xác thực',
        ], 401);
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
