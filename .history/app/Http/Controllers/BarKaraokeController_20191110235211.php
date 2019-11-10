<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\model\BarKaraokeModel;
use App\model\UserModel;
use App\model\HistoryModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\model\ManagerKaraoke;
use App\model\RatingLikeModel;
use DB;
class BarKaraokeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    //    $file =  File::delete(public_path().'/upload/Screenshot (301).png');
    //    return response()->json($file, 200);
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                $karaoke = BarKaraokeModel::join("table_province","table_bar_karaoke.ID_PROVINCE","table_province.ID_PROVINCE")
                ->join("table_district","table_bar_karaoke.ID_DISTRICT","table_district.ID_DISTRICT")
                ->where('USER_CREATE',$user->EMAIL)
                ->select("table_bar_karaoke.*","table_province.NAME_PROVINCE","table_district.NAME_DISTRICT")
                ->orderBy('CREATED_AT', 'asc')
                ->get();
                return response()->json($karaoke, 200);
            }
        }
        if($request->has('page'))
        {
            $karaoke = BarKaraokeModel::join("table_province","table_bar_karaoke.ID_PROVINCE","table_province.ID_PROVINCE")
            ->join("table_district","table_bar_karaoke.ID_DISTRICT","table_district.ID_DISTRICT")
            ->select("table_bar_karaoke.*","table_province.NAME_PROVINCE","table_district.NAME_DISTRICT")
            ->orderBy('CREATED_AT', 'DESC')
            ->limit(10)
            ->get();
            return response()->json($karaoke, 200);
            
        }
        if($request->has('safe_url'))
        {
            $karaoke = BarKaraokeModel::join("table_province","table_bar_karaoke.ID_PROVINCE","table_province.ID_PROVINCE")
            ->join("table_district","table_bar_karaoke.ID_DISTRICT","table_district.ID_DISTRICT")
            ->where("URL_SAFE",$request->get('safe_url'))
            ->select("table_bar_karaoke.*","table_province.NAME_PROVINCE","table_district.NAME_DISTRICT")
            ->orderBy('CREATED_AT', 'DESC')
            ->first();
            return response()->json($karaoke, 200);
        }
        if($request->has("ID_DISTRICT"))
        {
            $karaoke = BarKaraokeModel::join("table_province","table_bar_karaoke.ID_PROVINCE","table_province.ID_PROVINCE")
            ->join("table_district","table_bar_karaoke.ID_DISTRICT","table_district.ID_DISTRICT")
            ->where("table_bar_karaoke.ID_DISTRICT",$request->get("ID_DISTRICT"))
            ->select("table_bar_karaoke.*","table_province.NAME_PROVINCE","table_district.NAME_DISTRICT")
            ->orderBy('CREATED_AT', 'DESC')
            ->get();
            return response()->json($karaoke, 200);
        }
        if($request->has("ID_PROVINCE"))
        {
            $karaoke = BarKaraokeModel::join("table_province","table_bar_karaoke.ID_PROVINCE","table_province.ID_PROVINCE")
            ->join("table_district","table_bar_karaoke.ID_DISTRICT","table_district.ID_DISTRICT")
            ->where("table_bar_karaoke.ID_PROVINCE",$request->get("ID_PROVINCE"))
            ->select("table_bar_karaoke.*","table_province.NAME_PROVINCE","table_district.NAME_DISTRICT")
            ->orderBy('CREATED_AT', 'DESC')
            ->get();
            return response()->json($karaoke, 200);
        }
        if($request->has('sort'))
        {
            if($request->get('sort') == 'star')
            {
                $karaokes = BarKaraokeModel::join("table_province","table_bar_karaoke.ID_PROVINCE","table_province.ID_PROVINCE")
                ->join("table_district","table_bar_karaoke.ID_DISTRICT","table_district.ID_DISTRICT")
                ->where([
                    ["table_bar_karaoke.UUID_BAR_KARAOKE",'<>',$request->get("UUID_BAR_KARAOKE")]
                ])
                ->select("table_bar_karaoke.*","table_province.NAME_PROVINCE","table_district.NAME_DISTRICT")
                ->orderBy("NUMBER_REATED","DESC","STAR_RATING","DESC")
                ->limit($request->get('limit'))->get();
                return response()->json($karaokes, 200);
            }
            else if($request->get('sort') == 'groupby')
            {
                $karaokes = BarKaraokeModel::join('table_province','table_bar_karaoke.ID_PROVINCE','table_province.ID_PROVINCE')
                ->select('table_province.NAME_PROVINCE','table_province.IMAGE_PROVINCE',DB::raw('count(table_bar_karaoke.ID_PROVINCE) as total'))
                ->groupBy('table_bar_karaoke.ID_PROVINCE')->orderBy("total","DESC")->get();
                return response()->json($karaokes, 200);
                // $karaokes = DB::table("table_bar_karaoke")

                
      
                // ->join("table_province","table_bar_karaoke.ID_PROVINCE","=","table_province.ID_PROVINCE")
                // ->select("table_province.NAME_PROVINCE",DB::raw("count(table_bar_karaoke.ID_PROVINCE)"))
                // ->groupBy("table_bar_karaoke.ID_PROVINCE")
      
                // ->get();
                // return response()->json($karaokes, 200);
            }
        }
        $karaoke = BarKaraokeModel::join("table_province","table_bar_karaoke.ID_PROVINCE","table_province.ID_PROVINCE")
            ->join("table_district","table_bar_karaoke.ID_DISTRICT","table_district.ID_DISTRICT")
            ->select("table_bar_karaoke.*","table_province.NAME_PROVINCE","table_district.NAME_DISTRICT")
            ->orderBy("NUMBER_REATED","DESC","STAR_RATING","DESC","VIEW","DESC")
            ->limit(10)
            ->get();
        return response()->json($karaoke, 200);
    }

    public function map(Request $request)
    {
        $karaoke = BarKaraokeModel::where("OBJECTID","<>","null")->orderBy("CREATED_AT","DESC")
        ->join("table_province","table_bar_karaoke.ID_PROVINCE","table_province.ID_PROVINCE")
        ->join("table_district","table_bar_karaoke.ID_DISTRICT","table_district.ID_DISTRICT")
        ->select("table_bar_karaoke.*","table_province.NAME_PROVINCE","table_district.NAME_DISTRICT")
        ->orderBy("CREATED_AT","DESC")
        
        ->get();
        return response()->json($karaoke, 200);
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
                    $data = $request->all();
                    // $file = $request->file('LOGO_BAR_KARAOKE');
                    // $name = $file->getClientOriginalName();
                    // $file->move(public_path().'/upload/logo/', $file->getClientOriginalName());
                    // $path = '/upload/logo/'.$name;
                    // $data["LOGO_BAR_KARAOKE"] = $path;
                    // $banner = $request->file("BANNER_BAR_KARAOKE");
                    // $banner->move(public_path().'/upload/banner/', $file->getClientOriginalName());
                    // $data["BANNER_BAR_KARAOKE"] = '/upload/banner/'.$file->getClientOriginalName();
                    $karaoke = BarKaraokeModel::create([
                        "UUID_BAR_KARAOKE" => $data["UUID_BAR_KARAOKE"],
                        "ID_DISTRICT" => $data["ID_DISTRICT"],
                        "ID_PROVINCE" => $data["ID_PROVINCE"],
                        "LOGO_BAR_KARAOKE" => $data["LOGO_BAR_KARAOKE"],
                        "BANNER_BAR_KARAOKE" => $data["BANNER_BAR_KARAOKE"],
                        "NAME_BAR_KARAOKE" => $data["NAME_BAR_KARAOKE"],
                        "ADDRESS_BAR_KARAOKE" => $data["ADDRESS_BAR_KARAOKE"],
                        "EMAIL_BAR_KARAOKE" => $data["EMAIL_BAR_KARAOKE"],
                        "PHONE_BAR_KARAOKE" => $data["PHONE_BAR_KARAOKE"],
                        "RENT_COST_MIN" => $data["RENT_COST_MIN"],
                        "RENT_COST_MAX" => $data["RENT_COST_MAX"],
                        "STAR_RATING" => 5,
                        "NUMBER_REATED" => 1,
                        "USER_CREATE" => $user->EMAIL,
                        "URL_SAFE" => $data["URL_SAFE"],
                        "CONTENT_BAR_KARAOKE" => $data["CONTENT_BAR_KARAOKE"]
                    ]);
                    ManagerKaraoke::create([
                        "UUID_USER" => $user->UUID_USER,
                        "UUID_BAR_KARAOKE" =>  $data["UUID_BAR_KARAOKE"],
                        "USER_CRAETE" => $user->EMAIL
                    ]);
                    HistoryModel::create([
                        "UUID_USER" => $user->UUID_USER,
                        "UUID_HISTORY_ACTION" => Str::uuid(),
                        "NAME_HISTORY" => "create karaoke",
                        "CONTENT_ACTION" => $user->EMAIL.' tạo quán karaoke '.$data["NAME_BAR_KARAOKE"]
                    ]);
                    return response()->json($request->all(), 200);
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
        $karaoke = BarKaraokeModel::join('table_province','table_bar_karaoke.ID_PROVINCE','table_province.ID_PROVINCE')
        ->join('table_district','table_bar_karaoke.ID_DISTRICT','table_district.ID_DISTRICT')
        ->where("UUID_BAR_KARAOKE",$id)->select('table_bar_karaoke.*','table_province.NAME_PROVINCE','table_district.NAME_DISTRICT')->first();
        return response()->json($karaoke, 200);
    }

    public function rating(Request $request,$id)
    {
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get("api_token"))->first();
            if($user)
            {   
                $rating = RatingLikeModel::where([
                    ["UUID_BAR_kARAOKE",$id],
                    ["UUID_USER",$user->UUID_USER],
                    ["TYPE",1]
                ])->first();
                if($request->has('rating'))
                {
                    
                    if(!$rating)
                    {
                        $karaoke = BarkaraokeModel::where("UUID_BAR_KARAOKE",$id)->first();
                        $rating = $karaoke->STAR_RATING * $karaoke->NUMBER_REATED;
                        $rating = $rating + (int)$request->get('rating');
                        $rating = $rating / ($karaoke->NUMBER_REATED + 1);
                        BarkaraokeModel::where("UUID_BAR_KARAOKE",$id)->update([
                            'STAR_RATING' => $rating,
                            'NUMBER_REATED' => $karaoke->NUMBER_REATED + 1
                        ]);
                        RatingLikeModel::create([
                            "UUID_RATING_LIKE" => Str::uuid(),
                            "UUID_USER" => $user->UUID_USER,
                            "UUID_BAR_KARAOKE" => $karaoke->UUID_BAR_KARAOKE
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
            $user = UserModel::where("USER_TOKEN",$request->get("api_token"))->first();
            if($user)
            {
                if($request->has("OBJECTID"))
                {
                    BarkaraokeModel::where("UUID_BAR_KARAOKE",$id)->update([
                        "OBJECTID" => $request->get('OBJECTID')
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Thêm địa chỉ chi nhánh lên bản đồ thành công!'
                    ], 200);
                }
                $data = $request->all();
                if($request->has("LOGO_BAR_KARAOKE"))
                {
                    // $file = $request->file('LOGO_BAR_KARAOKE');
                    // $name = $file->getClientOriginalName();
                    // $file->move(public_path().'/upload/logo/', $file->getClientOriginalName());
                    // $path = '/upload/logo/'.$name;
                    BarKaraokeModel::where("UUID_BAR_KARAOKE",$id)->update([
                        "LOGO_BAR_KARAOKE" => $request->file('LOGO_BAR_KARAOKE')
                    ]);
                }
                if($request->has("BANNER_BAR_KARAOKE"))
                {
                    // $file = $request->file("BANNER_BAR_KARAOKE");
                    // $file->move(public_path().'/upload/banner/', $file->getClientOriginalName());
                    BarKaraokeModel::where("UUID_BAR_KARAOKE",$id)->update([
                        "BANNER_BAR_KARAOKE" => $request->file("BANNER_BAR_KARAOKE")
                    ]);
                    
                }
                BarKaraokeModel::where("UUID_BAR_KARAOKE",$id)->update([
                        "ID_DISTRICT" => $data["ID_DISTRICT"],
                        "ID_PROVINCE" => $data["ID_PROVINCE"],
                        "NAME_BAR_KARAOKE" => $data["NAME_BAR_KARAOKE"],
                        "ADDRESS_BAR_KARAOKE" => $data["ADDRESS_BAR_KARAOKE"],
                        "EMAIL_BAR_KARAOKE" => $data["EMAIL_BAR_KARAOKE"],
                        "PHONE_BAR_KARAOKE" => $data["PHONE_BAR_KARAOKE"],
                        "RENT_COST_MIN" => $data["RENT_COST_MIN"],
                        "RENT_COST_MAX" => $data["RENT_COST_MAX"],
                        "CONTENT_BAR_KARAOKE" => $data["CONTENT_BAR_KARAOKE"]
                ]);
                HistoryModel::create([
                    "UUID_USER" => $user->UUID_USER,
                    "UUID_HISTORY_ACTION" => Str::uuid(),
                    "NAME_HISTORY" => "update karaoke",
                    "CONTENT_ACTION" => $user->EMAIL.' cập nhật thông tin quán karaoke '.$data["NAME_BAR_KARAOKE"]
                ]);
                return response()->json($data, 200);
            }
        }
        $karaoke = BarKaraokeModel::where("UUID_BAR_KARAOKE",$id)->update($request->all());
        return response()->json($karaoke, 200);
    }

    public function view(Request $request,$id)
    {
        $view = BarkaraokeModel::where("UUID_BAR_KARAOKE",$id)->first();
        BarkaraokeModel::where("UUID_BAR_KARAOKE",$id)->update([
            "VIEW" => $view->VIEW+1
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật view thành công'
        ], 200);
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
