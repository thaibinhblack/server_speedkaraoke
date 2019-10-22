<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\UserModel;
use App\model\HistoryModel;
use App\model\PromotionModel;
use App\model\DetailPromotionKaraokeModel;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;
class PromotionController extends Controller
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
                $promotion = PromotionModel::join('table_detail_promotion_karaoke','table_promotion.UUID_PROMOTION','table_detail_promotion_karaoke.UUID_PROMOTION')
                ->select('table_promotion.*','table_detail_promotion_karaoke.UUID_PROMOTION',DB::raw('count(*) as total'))
                ->groupBy('table_detail_promotion_karaoke.UUID_PROMOTION')->orderBy("total","DESC")
                ->get();
                return response()->json($promotion, 200);
            }
        }
        $promotion = PromotionModel::where([
            ["DATE_STARTED", "<=",Carbon::today()->toDateString()],
            ["DATE_END", ">=", Carbon::today()->toDateString()]
        ])
        ->orderBy("CREATED_AT","DESC")
       ->get();
        return response()->json($promotion, 200);
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
                $checks = $request->get('checks');
                $arrays = explode(',',$checks);
                $uuid =  Str::uuid();
                $promotion = PromotionModel::create([
                    "UUID_PROMOTION" => $uuid,
                    "BANNER_PROMOTION" => $path,
                    "NAME_PROMOTION" => $request->get("NAME_PROMOTION"),
                    "CONTENT_PROMOTION" => $request->get("CONTENT_PROMOTION"),
                    "VALUE_SAFE_OFF" => $request->get("VALUE_SAFE_OFF"),
                    "USER_CREATE" => $user->EMAIL,
                    "DATE_STARTED" => $request->get("DATE_STARTED"),
                    "DATE_END" => $request->get("DATE_END")
                ]);
                foreach ($arrays as $check) {
                    DetailPromotionKaraokeModel::create([
                        "UUID_PROMOTION" => $uuid,
                        "UUID_BAR_KARAOKE" => $check,
                    ]);
                }
                
                if($promotion)
                {
                    HistoryModel::create([
                        "UUID_HISTORY_ACTION" => Str::uuid(),
                        "UUID_USER" => $user->UUID_USER,
                        "NAME_HISTORY" => 'Khuyến mãi',
                        "CONTENT_ACTION" => $user->EMAIL.' tạo khuyến mãi '.$request->get("NAME_PROMOTION")
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Tạo khuyến mãi thành công',
                        'status' => 200,
                        'data' => $promotion
                    ], 200);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Tạo khuyến mãi thất bại',
                    'status' => 400,    
                ], 200);
            }
            return response()->json([
                'message' => 'Authorizon',
                'status' => 401
            ], 200);
        }
        return response()->json([
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
        $promotion = PromotionModel::where([
            ["DATE_STARTED", "<=",Carbon::today()->toDateString()],
            ["DATE_END", ">=", Carbon::today()->toDateString()],
            ["UUID_PROMOTION",$id]
        ])
        ->orderBy("CREATED_AT","DESC")
       ->get();
       return response()->json($promotion, 200);
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