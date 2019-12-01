<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\BillModel;
use App\model\BookingModel;
use App\model\HistoryModel;
use App\model\BarKaraokeModel;
use App\model\RoomBarKaraokeModel;
use Illuminate\Support\Str;
use App\model\UserModel;
use Twilio\Rest\Client;
class BillCotroller extends Controller
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
                $bills = BillModel::join('table_bar_karaoke','table_bill.UUID_BAR_KARAOKE','table_bar_karaoke.UUID_BAR_KARAOKE')
                ->where("UUID_USER",$user->UUID_USER)
                ->select('table_bill.*','table_bar_karaoke.NAME_BAR_KARAOKE','table_bar_karaoke.LOGO_BAR_KARAOKE')
                ->orderBy('table_bill.CREATED_AT','DESC')
                ->get();
                return response()->json([
                    'success' => true,
                    'message' => 'Danh sách hóa đơn',
                    'results' => $bills
                ], 200);
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
            
            $sid = 'AC4db4f33d4ee7ca06e6dd7b06f9c72274';
            $token = 'c98c668873c3404720c2b4228e302d6c';
            $client = new Client($sid, $token);
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {    $karaoke = RoomBarKaraokeModel::join('table_bar_karaoke','table_room_bar_karaoke.UUID_BAR_KARAOKE','table_bar_karaoke.UUID_BAR_KARAOKE')
                    ->join('table_detail_manager_bar_karaoke','table_bar_karaoke.UUID_BAR_KARAOKE','table_detail_manager_bar_karaoke.UUID_BAR_KARAOKE')
                    ->where("table_room_bar_karaoke.UUID_ROOM_BAR_KARAOKE", $request->get("UUID_ROOM_BAR_KARAOKE"))
                    ->select('table_room_bar_karaoke.NAME_ROOM_BAR_KARAOKE','table_bar_karaoke.PHONE_BAR_KARAOKE'
                    ,'table_detail_manager_bar_karaoke.UUID_USER')
                    ->first();
                    // return response()->json($karaoke, 200);
                if($request->get("PAYPAL") == 1)
                {
                    // $bill = BillModel::create([
                    //     "UUID_BILL" => Str::uuid(),
                    //     "UUID_BAR_KARAOKE" => $request->get("UUID_BAR_KARAOKE"),
                    //     "UUID_USER" => $user->UUID_USER,
                    //     "PRICE_BILL" => $request->get("PRICE_BILL"),
                    //     "TOTAL_TIME" => $request->get("TOTAL_TIME"),
                    //     "RENT_COST" => $request->get("RENT_COST"),
                    //     "CODE_PROMOTION" => $request->get("CODE_PROMOTION"),
                    //     "PAYPAL" => $request->get("PAYPAL")
                    // ]);
                   
                    $result  = $client->messages->create(
                        // the number you'd like to send the message to
                        '+84'.$karaoke->PHONE_BAR_KARAOKE,
                        array(
                            // A Twilio phone number you purchased at twilio.com/console
                            'from' => '+12055189442',
                            // the body of the text message you'd like to send
                            'body' => 'KHACH HANG YEU CAU THANH TOAN TRUC TIEP TAI PHONG '.$karaoke->NAME_ROOM_BAR_KARAOKE
                        )
                    );
                    return response()->json([
                        'success' => true,
                        'message' => 'Thanh toán thành công!',
                        'result' => ''
                    ], 200);
                }
                else {
                    if($user->SPEED_COIN > $request->get("PRICE_BILL"))
                    {
                        UserModel::where("UUID_USER",$user->UUID_USER)->update([
                            'SPEED_COIN' => $user->SPEED_COIN -  $request->get("PRICE_BILL")
                        ]);
                        $user_manager = UserModel::where("UUID_USER",$karaoke->UUID_USER)->first();
                        UserModel::where("UUID_USER",$karaoke->UUID_USER)->update([
                            "SPEED_COIN" => $user_manager->SPEED_COIN + $request->get("PRICE_BILL")
                        ]);
                        $coin = $user->SPEED_COIN - $request->get("PRICE_BILL");
                        $result  = $client->messages->create(
                            // the number you'd like to send the message to
                            '+84'.$karaoke->PHONE_BAR_KARAOKE,
                            array(
                                // A Twilio phone number you purchased at twilio.com/console
                                'from' => '+12055189442',
                                // the body of the text message you'd like to send
                                'body' => 'TK SPEED COIN CUA BAN DUOC THEM '.$request->get("PRICE_BILL").' DO KHACH HANG DUNG SPPED COIN THANH TOAN'
                            )
                        );
                        $result  = $client->messages->create(
                            // the number you'd like to send the message to
                            '+84'.$user->PHONE,
                            array(
                                // A Twilio phone number you purchased at twilio.com/console
                                'from' => '+12055189442',
                                // the body of the text message you'd like to send
                                'body' => 'BAN DA SU DUNG '.$request->get('PRICE_BILL').' SPEED COIN DE THANH TOAN, SPEED COIN CON LAI '.$coin
                            )
                        );
                        $bill = BillModel::create([
                            "UUID_BILL" => Str::uuid(),
                            "UUID_BAR_KARAOKE" => $request->get("UUID_BAR_KARAOKE"),
                            "UUID_USER" => $user->UUID_USER,
                            "PRICE_BILL" => $request->get("PRICE_BILL"),
                            "TOTAL_TIME" => $request->get("TOTAL_TIME"),
                            "RENT_COST" => $request->get("RENT_COST"),
                            "CODE_PROMOTION" => $request->get("CODE_PROMOTION"),
                            "PAYPAL" => $request->get("PAYPAL")
                        ]);
                        $booking = BookingModel::where("UUID_BOOKING",$request->get("UUID_BOOKING"))->update([
                                'STATUS' => 3
                            ]);
                        return response()->json([
                            'success' => true,
                            'message' => 'Thanh toán thành công!',
                            'result' => $bill,
                            'booking' => $booking
                        ], 200);
                    }
                    else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Speed coin không đủ để thanh toán',
                            'result' => null
                        ], 200);
                    }
                }
            }
        }
        
    }

    public function thanhtoan(Request $request)
    {
        if($request->has("api_token"))
        {
            $user = UserModel::where("USER_TOKEN",$request->get("api_token"))->first();
            if($user)
            {
                $UUID_BILL = Str::uuid();
                $bill = BillModel::create([
                    "UUID_BILL" => $UUID_BILL,
                    "UUID_BAR_KARAOKE" => $request->get("UUID_BAR_KARAOKE"),
                    "UUID_USER" => $request->get("UUID_USER"),
                    "PRICE_BILL" => $request->get("PRICE_BILL"),
                    "TOTAL_TIME" => $request->get("TOTAL_TIME"),
                    "RENT_COST" => $request->get("RENT_COST"),
                    "CODE_PROMOTION" => $request->get("CODE_PROMOTION"),
                    "PAYPAL" => $request->get("PAYPAL"),
                    "UUID_BOOKING" => $request->get("UUID_BOOKING")
                ]);
                BookingModel::where("UUID_BOOKING", $request->get("UUID_BOOKING"))->update([
                    "TIME_END" => $request->get("TIME_END"),
                    "STATUS" => 3,
                    "TOTAL_TIME" => $request->get("TOTAL_TIME"),
                    "UUID_BILL" => $UUID_BILL
                ]);
                $user_booking = UserModel::where("UUID_USER",$request->get("UUID_USER"))->first();
                UserModel::where("UUID_USER",$request->get("UUID_USER"))->update([
                    "RELIABILITY" => $user_booking->RELIABILITY + 2
                ]);
                HistoryModel::create([
                    "UUID_HISTORY_ACTION" => Str::uuid(),
                    "UUID_USER" => $user->UUID_USER,
                    "NAME_HISTORY" => 'Thanh toán',
                    "CONTENT_ACTION" => $user->EMAIL.' đã thanh toán cho phòng '.$request->get("NAME_ROOM_BAR_KARAOKE"). ' của chi nhánh '.$request->get("NAME_BAR_KARAOKE")
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Thanh toán thành công',
                    'status' => 200
                ], 200);
            }
        }
        // return response()->json($bill, 200);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bill = BillModel::join("table_bar_karaoke","table_booking.UUID_BAR_KARAOKE","table_bar_karaoke.UUID_BAR_KARAOKE")

        ->join("table_user","table_bill.UUID_USER","table_user.UUID_USER")
        ->where("UUID_BILL",$id)->select("table_bar_karaoke.*","table_room_bar_karaoke.RENT_COST","table_room_bar_karaoke.NAME_ROOM_BAR_KARAOKE","table_bill.*","table_user.*")->first();
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
