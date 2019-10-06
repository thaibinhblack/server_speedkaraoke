<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\UserModel;
use App\model\ManagerKaraoke;
class ManagerKaraokeController extends Controller
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
           $user = UserModel::where("USER_TOKEN",$request->get("api_token"))->first();
           if($user)
           {
               $managers = UserModel::join("table_detail_manager_bar_karaoke","table_user.UUID_USER","table_detail_manager_bar_karaoke.UUID_USER")
               ->where("table_user.USER_CREATE",$user->EMAIL)
               ->get();
               return response()->json($managers, 200);
           }
       }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $managers = ManagerKaraoke::join('table_user','table_detail_manager_bar_karaoke.UUID_USER','table_user.UUID_USER')->where("UUID_BAR_KARAOKE",$id)->get();
        return response()->json($managers, 200);
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
