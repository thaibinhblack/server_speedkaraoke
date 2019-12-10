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
use DateTime;
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
                ->join('table_detail_manager_bar_karaoke','table_detail_promotion_karaoke.UUID_BAR_KARAOKE','table_detail_manager_bar_karaoke.UUID_BAR_KARAOKE')
                ->where('table_detail_manager_bar_karaoke.UUID_USER',$user->UUID_USER)
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
                    "CODE_PROMOTION" => $request->get("CODE_PROMOTION"),
                    "NUMBER_PROMOTION" => $request->get("NUMBER_PROMOTION"),
                    "USE_PROMOTION" => 0,
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
       ->first();
       return response()->json($promotion, 200);
    }

    public function karaoke($id)
    {
        $karaoke = DetailPromotionKaraokeModel::join('table_bar_karaoke','table_detail_promotion_karaoke.UUID_BAR_KARAOKE', 'table_bar_karaoke.UUID_BAR_KARAOKE')
        ->join("table_province","table_bar_karaoke.ID_PROVINCE","table_province.ID_PROVINCE")
        ->join("table_district","table_bar_karaoke.ID_DISTRICT","table_district.ID_DISTRICT")
        ->where("table_detail_promotion_karaoke.UUID_PROMOTION",$id)
        ->select('table_bar_karaoke.*',"table_province.NAME_PROVINCE","table_district.NAME_DISTRICT")
        ->orderBy("NUMBER_REATED","DESC","STAR_RATING","DESC")
        ->get();
        return response()->json($karaoke, 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function check_promotion(Request $request,$id)
    {
        $date =  new DateTime();
        $code  = DetailPromotionKaraokeModel::join("table_promotion","table_detail_promotion_karaoke.UUID_PROMOTION","table_promotion.UUID_PROMOTION")
        ->where([
            ["table_detail_promotion_karaoke.UUID_BAR_KARAOKE",$id],
            ["table_promotion.CODE_PROMOTION",$request->get("CODE_PROMOTION")],
            ["table_promotion.USE_PROMOTION", ">=", 1],
            ["DATE_STARTED", "<=",Carbon::today()->toDateString()],
            ["DATE_END", ">=", Carbon::today()->toDateString()]
        ])->first();
        if($code)
        {
            return response()->json([
                'success' => true,
                'message' => 'Code hợp lệ',
                'result' => $code
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Code không hợp lệ',
            'result' => null
        ], 200);
    }

    public function use_promotion(Request $request,$id)
    {

    }
}
