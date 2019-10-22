<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\UserModel;
use App\model\HistoryModel;
use App\model\PromotionModel;
use Illuminate\Support\Str;
class PromotionController extends Controller
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
                $file = $request->file('BANNER_PROMOTION');
                $name = $file->getClientOriginalName();
                $file->move(public_path().'/upload/promotions/', $file->getClientOriginalName());
                $path = '/upload/promotions/'.$name;
                $promotion = PromotionModel::create([
                    "UUID_PROMOTION" => Str::uuid(),
                    "BANNER_PROMOTION" => $path,
                    "NAME_PROMOTION" => $request->get("NAME_PROMOTION"),
                    "CONTENT_PROMOTION" => $request->get("CONTENT_PROMOTION"),
                    "VALUE_SAFE_OFF" => $request->get("VALUE_SAFE_OFF"),
                    "USER_CREATE" => $user->EMAIL,
                    "DATE_STARTED" => $request->get("DATE_STARTED"),
                    "DATE_END" => $request->get("DATE_END")
                ]);
                if($promotion)
                {
                    HistoryModel::create([
                        "UUID_HISTORY_ACTION" => Str::uuid(),
                        "UUID_USER" => $user->UUID_USER,
                        "NAME_HISTORY" => 'Khuyến mãi',
                        "CONTENT_ACTION" => $user->EMAIL.' tạo khuyến mãi '.$request->get("NAME_PROMOTION")
                    ])
                }
            }
            return response()->json([
                'message' => 'Authorizon',
                'status' => 401
            ], 200);
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
