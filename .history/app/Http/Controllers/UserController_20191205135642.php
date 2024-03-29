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
                    "PASSWORD" => Hash::make($request->get("PASSWORD")),
                    "PHONE" => $request->get("PHONE"),
                    "UUID_RULE" => $request->get("UUID_RULE"),
                    "DISPLAY_NAME" => $request->get("DISPLAY_NAME"),
                    "BIRTH_DAY" => $request->get('BIRTH_DAY'),
                    "ADDRESS" => $request->get("ADDRESS"),
                    "GENDER" => $request->get("GENDER"),
                    "AVATAR" => $path,
                    "USER_CREATE" => $user->EMAIL
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

                return response()->json($request->all(), 200);
            }
        }
    }

    public function checkToken(Request $request)
    {
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get("api_token"))->first();
            if($user)
            {
                return response()->json($user, 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'Authorizon',
                'status' => 404
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Authorizon',
            'status' => 401
        ], 200);
        
    }


    public function resignter(Request $request)
    {
        $check = UserModel::where("EMAIL",$request->get("EMAIL"))->first();
        if($check)
        {
            return response()->json([
                "success" => false,
                "message" => "Email đã tồn tại!"
            ], 200);
        }
        $user = UserModel::create([
            "UUID_USER" => Str::uuid(),
            "UUID_RULE" => 'user-2019',
            "EMAIL" => $request->get("EMAIL"),
            "PASSWORD" => Hash::make($request->get("PASSWORD")),
            "PHONE" => $request->get("PHONE")
        ]);
        if($user)
        {
            $token = JWTAuth::fromUser($user);
            UserModel::where("EMAIL",$user->EMAIL)->update([
                "USER_TOKEN" => $token
            ]);
            return response()->json([
                "success" => true,
                "message" => "Đăng ký tài khoản thành công",
                "data" => $token
            ], 200);
        }
        else {
            return response()->json([
                "success" => false,
                "message" => "Đăng ký thất bại!",
            ], 200);
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
                return response()->json([
                    "success" => true,
                    "message" => "Đăng nhập thành công!",
                    "data" => $token,
                    "status" => 200
                ], 200);
            }
            return response()->json([
                "success" => false,
                "message" => "Mật khẩu sai!",
                "status" => 400
            ], 200);
        }
        return response()->json([
            "success" => false,
            "message" => "Email sai!"
        ], 200);
        
    }

    public function facebook(Request $request)
    {
        $user = UserModel::where([
            ["EMAIL",$request->get("EMAIL")],
        ])->first();
        if($user)
        {
            UserModel::where([
                ["EMAIL",$request->get("EMAIL")],
            ])->update([
                "USER_TOKEN" => $request->get("TOKEN")
            ]);
            return response()->json($request->get("TOKEN"), 200);
        }
        UserModel::create([
            "UUID_USER" => Str::uuid(),
            "UUID_RULE" => 'user-2019',
            "EMAIL" => $request->get("EMAIL"),
            "DISPLAY_NAME" => $request->get("DISPLAY_NAME"),
            "AVATAR" => $request->get("AVATAR"),
            "USER_TOKEN" => $request->get("TOKEN"),
            "TYPE_USER" => "FACEBOOK"
        ]);
        return response()->json($request->get("TOKEN"), 200);
    }
    public function google(Request $request)
    {
        $user = UserModel::where([
            ["EMAIL",$request->get("EMAIL")],
        ])->first();
        if($user)
        {
            UserModel::where([
                ["EMAIL",$request->get("EMAIL")],
            ])->update([
                "USER_TOKEN" => $request->get("TOKEN")
            ]);
            return response()->json($request->get("TOKEN"), 200);
        }
        UserModel::create([
            "UUID_USER" => Str::uuid(),
            "UUID_RULE" => 'user-2019',
            "EMAIL" => $request->get("EMAIL"),
            "DISPLAY_NAME" => $request->get("DISPLAY_NAME"),
            "AVATAR" => $request->get("AVATAR"),
            "USER_TOKEN" => $request->get("TOKEN"),
            "TYPE_USER" => "GOOGLE"
        ]);
        return response()->json($request->get("TOKEN"), 200);
    }

    public function manager(Request $request,$id)
    {
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                UserModel::where([
                    ["UUID_USER",$id],
                    ["USER_TOKEN",$request->get("api_token")]])->update([
                        "UUID_RULE" => 'manager-2019'
                    ]);
                return response()->json([
                    "success" => true,
                    "message" => "Đăng ký trờ thành chủ quán thành công!"
                ], 200);
            }
        }
    }
    
    public function change_password(Request $request)
    {
        if($request->has("api_token"))
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                $new_password = Hash::make($request->get('new_password'));
                $result = UserModel::where("UUID_USER",$user->UUID_USER)->update([
                    "PASSWORD" => $new_password
                ]);
                if($result == 1)
                {
                    return response()->json('Thay đổi mật khẩu thành công!', 200);
                }
                return response()->json('Cập nhật mật khẩu thất bại!', 200);
            }
        }
    }

    public function user_manageer(Request $request)
    {
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {  
                $file = $request->file('AVATAR');
                $uuid = Str::uuid();
                $name = $file->getClientOriginalName();
                $file->move(public_path().'/upload/avatars/', $file->getClientOriginalName());
                $path = '/upload/avatars/'.$name;
                $user_create = UserModel::create([
                    'UUID_USER' => $uuid,
                    "EMAIL" => $request->get('EMAIL'),
                    "PASSWORD" => Hash::make( $request->get("PASSWORD")),
                    'AVATAR' => $path,
                    "DISPLAY_NAME" => $request->get('DISPLAY_NAME'),
                    'PHONE' => $request->get("PHONE"),
                    'ADDRESS' => $request->get("ADDRESS"),
                    'BIRTH_DAY' => $request->get('BIRTH_DAY'),
                    'GENDER' => $request->get('GENDER'),
                    'UUID_RULE' => $request->get('UUID_RULE'),
                ]);
                ManagerKaraoke::create([
                    "UUID_USER" => $uuid,
                    "UUID_BAR_KARAOKE" => $request->get("UUID_BAR_KARAOKE"),
                    "USER_CREATE" => $user->EMAIL
                ]);
                return response()->json($user_create, 200);
            }
        }
    }

    public function show($id,Request $request)
    {
       if($request->has('api_token'))
       {
           $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
           if($user)
           {
                $profile = UserModel::where("UUID_USER",$id)->first();
                return response()->json($profile, 200);
           }
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
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                $data = $request->all();
                if($request->has("AVATAR"))
                {
                    $file = $request->file('AVATAR');
                    $name = $file->getClientOriginalName();
                    $file->move(public_path().'/upload/avatars/', $file->getClientOriginalName());
                    $path = '/upload/avatars/'.$name;
                    $data["AVATAR"] = $path;
                    UserModel::where("UUID_USER",$id)->update([
                        "AVATAR" =>  $data["AVATAR"]
                    ]);
                }
                UserModel::where("UUID_USER",$id)->update([
                    "DISPLAY_NAME" => $request->get("DISPLAY_NAME"),
                    "PHONE" => $request->get("PHONE"),
                    "ADDRESS" => $request->get("ADDRESS"),
                    "BIRTH_DAY" => $request->get("BIRTH_DAY")
                ]);
                return response()->json([
                    "success" => true,
                    "message" => "Cập nhật thành công"
                ], 200);
            }
            return response()->json([
                "success" => false,
                "message" => "Authorizon"
            ], 200);
        }
        return response()->json([
            "success" => false,
            "message" => "Authorizon"
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
