<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Response;
use App\model\HistoryModel;
use Illuminate\Support\Str;
use App\model\ManagerKaraoke;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has('check_email'))
        {
            $user = UserModel::where('EMAIL', $request->get('check_email'))->first();
            if($user)
            {
                return response()->json(true, 200);
            }
            else {
                return response()->json(false, 200);
            }
        }
        if($request->has('api_token')){
            $user = UserModel::where("USER_TOKEN",$request->get("api_token"))->first();
            if($user)
            {
                $users = UserModel::where("USER_CREATE",$user->EMAIL)->get();
                return response()->json($users, 200);
            }
            return response()->json(false, 401);
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
        if($request->has("api_token"))
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                $uuid =  Str::uuid();
                $file = $request->file('AVATAR');
                $name = $file->getClientOriginalName();
                $file->move(public_path().'/upload/avatars/', $file->getClientOriginalName());
                $path = '/upload/avatars/'.$name;
                UserModel::create([
                    "UUID_USER" => $uuid,
                    "EMAIL" => $request->get("EMAIL"),
                    "PASSWPORD" => Hash::make($request->get("PASSWORD")),
                    "PHONE" => $request->get("PHONE"),
                    "UUID_RULE" => $request->get("UUID_RULE"),
                    "DISPLAY_NAME" => $request->get("DISPLAY_NAME"),
                    "BIRTH_DAY" => $request->get('BIRTH_DAY'),
                    "ADDRESS" => $request->get("ADDRESS"),
                    "GENDER" => $request->get("GENDER"),
                    "AVATAR" => $path
                ]);
                
                ManagerKaraoke::create([
                    "UUID_USER" => $uuid,
                    "UUID_BAR_KARAOKE" => $request->get("UUID_BAR_KARAOKE"),
                    "USER_CREATE" => $user->EMAIL
                ]);

                HistoryModel::create([
                    "UUID_USER" => $user->UUID_USER,
                    "UUID_HISTORY_ACTION" => Str::uuid(),
                    "NAME_HISTORY" => 'create',
                    "CONTENT_ACTION" => $user->EMAIL.' tạo user '.$request->get("EMAIL")
                ]);

                return response()->json('success', 200);
            }
        }
    }

    public function checkToken(Request $request)
    {
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get("api_token"))->first();
            return response()->json($user, 200);
        }
        
    }

    public function login(Request $request)
    {
        // $data = $request->all();
        $user = UserModel::where("EMAIL",$request->get('EMAIL'))->first();
        if($user)
        {
            if(Hash::check($request->get("PASSWORD"), $user->PASSWORD))
            {
                $token = JWTAuth::fromUser($user);
                $user = UserModel::where("EMAIL",$request->get('EMAIL'))->update([
                    "USER_TOKEN" => $token
                ]);
                return response()->json($token, 200);
            }
            return response()->json(false, 401);
        }
        return response()->json(false, 404);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       

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
