<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\UserModel;
use Illuminate\Support\Facades\Hash;
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
        $user = UserModel::all();
        return response()->json($user, 200);
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
        if($request->has('login_social'))
        {
            if($request->get('login_social') == 'gmail')
            {
                $user = UserModel::where("EMAIL",$request->get("EMAIL"))->first();
                if($user)
                {
                    return response()->json('success', 200, $headers);
                }
                else {
                    $user_create = UserModel::create([
                        'UUID_USER' => $request->get('UUID_USER'),
                        'UUID_RULE' => $request->get('UUID_RULE'),
                        'DISPLAY_NAME' => $request->get('DISPLAY_NAME'),
                        'EMAIL' => $request->get('EMAIL'),
                        'PHONE' => $request->get('PHONE'),
                        'AVATAR' => $request->get('AVATAR'),
                        'TYPE_USER' => $request->get('login_social')
                    ]);
                    return response()->json($user_create, 200);
                }
                
            }
        }
        else
        {
            $data = $request->all();
            $data["PASSWORD"] = Hash::make($data["PASSWORD"]);
            // $user = UserModel::create($data);
            return response()->json($data, 200);
        }
        return response()->json($request->all(), 200);
    }

    public function login(Request $request)
    {
        // $data = $request->all();
        $credentials = $request->only('EMAIL', 'PASSWORD');
        $user = UserModel::where('EMAIL', $request->get('EMAIL'))->first();
        $data = $request->all();
        if(Hash::check($data["PASSWORD"], $user["PASSWORD"]))
        {
            return response()->json($user, 200);
        }
        else {
            return response()->json(false, 200);
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
