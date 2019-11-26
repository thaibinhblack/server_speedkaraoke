<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\UserModel;
use App\model\DetailFunction;
use Illuminate\Support\Str;

class DetailFunctionController extends Controller
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
                
               $check_rule = DetailFunction::where([
                   ["UUID_RULE",$request->get("UUID_RULE")],
                   ["UUID_FUNCTION", $request->get("UUID_FUNCTION")]
               ])->first();
                $p_id_rule = $request->get("UUID_RULE");
                $p_id_cn = $request->get("UUID_FUNCTION");
                $FUNCTIONS = $request->get("FUNCTIONS");
                
                $view = $create = $edit = $delete = $export = $p_id_cn.'0';
                $array = explode(',', $FUNCTIONS);
                foreach ($array as $value) {
                    if($value == $p_id_cn.'.'.'1')
                    {
                        $view = $value;
                    }
                    else if($value == $p_id_cn.'.'.'2')
                    {
                        $create = $value;
                    }
                    else if($value == $p_id_cn.'.'.'3')
                    {
                        $edit = $value;
                    }
                    else if($value == $p_id_cn.'.'.'4')
                    {
                        $delete = $value;
                    }
                    else if($value == $p_id_cn.'.'.'5')
                    {
                        $export = $value;
                    }
                }
                
                if(!$check_rule)
                {
                    DetailFunction::create([
                        'UUID_RULE' => $request->get("UUID_RULE"),
                        "UUID_FUNCTION" => $request->get("UUID_FUNCTION"),
                        "FUNCTION_VIEW" => $view,
                        "FUNCTION_CREATE" => $create.
                        "FUNCTION_EDIT" => $edit,
                        "FUNCTION_DELETE" => $delete
                    ]);
                    
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
