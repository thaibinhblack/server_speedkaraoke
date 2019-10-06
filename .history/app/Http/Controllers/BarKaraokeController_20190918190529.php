<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\BarKaraokeModel;
use App\model\UserModel;
use App\model\HistoryModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BarKaraokeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       $file =  File::delete('/upload/Screenshot (290).png');
       return response()->json($file, 200);
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                $karaoke = BarKaraokeModel::where('USER_CREATE',$user->EMAIL)->orderBy('CREATED_AT', 'asc')->get();
                return response()->json($karaoke, 200);
            }
        }
        if($request->has('page'))
        {
            $karaokes = BarKaraokeModel::orderBy("CREATED_AT","DESC")->get();
            return response()->json($karaokes, 200);
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
                    $data = $request->all();
                    $file = $request->file('LOGO_BAR_KARAOKE');
                    $name = $file->getClientOriginalName();
                    $file->move(public_path().'/upload/logo/', $file->getClientOriginalName());
                    $path = '/upload/logo/'.$name;
                    $data["LOGO_BAR_KARAOKE"] = $path;
                    $karaoke = BarKaraokeModel::create([
                        "UUID_BAR_KARAOKE" => $data["UUID_BAR_KARAOKE"],
                        "ID_DISTRICT" => $data["ID_DISTRICT"],
                        "ID_PROVINCE" => $data["ID_PROVINCE"],
                        "LOGO_BAR_KARAOKE" => $data["LOGO_BAR_KARAOKE"],
                        "NAME_BAR_KARAOKE" => $data["NAME_BAR_KARAOKE"],
                        "ADDRESS_BAR_KARAOKE" => $data["ADDRESS_BAR_KARAOKE"],
                        "EMAIL_BAR_KARAOKE" => $data["EMAIL_BAR_KARAOKE"],
                        "PHONE_BAR_KARAOKE" => $data["PHONE_BAR_KARAOKE"],
                        "STAR_RATING" => $data["STAR_RATING"],
                        "NUMBER_REATED" => $data["NUMBER_REATED"],
                        "USER_CREATE" => $user->EMAIL,
                        "URL_SAFE" => $data["URL_SAFE"],
                        "CONTENT_BAR_KARAOKE" => $data["CONTENT_BAR_KARAOKE"]
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
        $karaoke = BarKaraokeModel::where("UUID_BAR_KARAOKE",$id)->first();
        return response()->json($karaoke, 200);
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
                $data = $request->all();
                if($request->has("LOGO_BAR_KARAOKE"))
                {
                    $file = $request->file('LOGO_BAR_KARAOKE');
                    $name = $file->getClientOriginalName();
                    $file->move(public_path().'/upload/logo/', $file->getClientOriginalName());
                    $path = '/upload/logo/'.$name;
                    BarKaraokeModel::where("UUID_BAR_KARAOKE",$id)->update([
                        "LOGO_BAR_KARAOKE" => $path
                    ]);
                }
                BarKaraokeModel::where("UUID_BAR_KARAOKE",$id)->update([
                        "ID_DISTRICT" => $data["ID_DISTRICT"],
                        "ID_PROVINCE" => $data["ID_PROVINCE"],
                        "NAME_BAR_KARAOKE" => $data["NAME_BAR_KARAOKE"],
                        "ADDRESS_BAR_KARAOKE" => $data["ADDRESS_BAR_KARAOKE"],
                        "EMAIL_BAR_KARAOKE" => $data["EMAIL_BAR_KARAOKE"],
                        "PHONE_BAR_KARAOKE" => $data["PHONE_BAR_KARAOKE"],
                        "STAR_RATING" => $data["STAR_RATING"],
                        "NUMBER_REATED" => $data["NUMBER_REATED"],
                        "URL_SAFE" => $data["URL_SAFE"],
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
