<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\BillModel;
use App\model\BookingModel;
use Illuminate\Support\Str;
class BillCotroller extends Controller
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
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                if($request->get("PAYPAL") == 1)
                {
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
                    return response()->json([
                        'success' => true,
                        'message' => 'Thanh toán thành công!',
                        'result' => $bill
                    ], 200);
                }
                else {
                    if($user->SPEED_KARAOKE > $request->get("PRICE_BILL"))
                    {
                        UserModel::where("UUID_USER",$user->UUID_USER)->update([
                            'SPEED_KARAOKE' => $user->SPEED_KARAOKE -  $request->get("PRICE_BILL")
                        ]);
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
                        return response()->json([
                            'success' => true,
                            'message' => 'Thanh toán thành công!',
                            'result' => $bill
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
