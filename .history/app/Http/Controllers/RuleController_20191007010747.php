<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\RuleModel;
use Illuminate\Support\Str;
use App\model\UserModel;
use App\model\HistoryModel;
class RuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has("user_create"))
        {
            $rules = RuleModel::where("USER_CREATE",$request->get("user_create"))->orderBy("CREATED_AT","DESC")->get();
            return response()->json($rules, 200);
        }
        return response()->json(RuleModel::all(), 200);
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
                $rule = RuleModel::create([
                    "UUID_RULE" => Str::uuid(),
                    "NAME_RULE" => $request->get("NAME_RULE"),
                    "USER_CREATE" => $user->EMAIL
                ]);
                HistoryModel::create([
                    "UUID_HISTORY" => Str::uuid(),
                    "UUID_USER" => $user->UUID_USER,
                    "NAME_HISTORY" => "Chức năng",
                    "CONTENT_HISTORY" => $user->EMAIL.' tạo chức năng '.$request->get("NAME_RULE")
                ]);
                return response()->json($rule, 200);
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
