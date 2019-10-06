<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\AttributeRoomModel;
use App\model\DetailAttributeRoomModel;
class AttributeRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has("UUID_ROOM_BAR_KARAOKE"))
        {
            $attribute = AttributeRoomModel::join("TABLE_DETAIL_ATTRIBUTE_ROOM", "TABLE_ATTRIBUTE_ROOM.UUID_ATTRIBUTE_ROOM", "TABLE_DETAIL_ATTRIBUTE_ROOM.UUID_ATTRIBUTE_ROOM")
        ->where('UUID_ROOM_BAR_KARAOKE',$request->get("UUID_ROOM_BAR_KARAOKE"))->orderBy('TABLE_ATTRIBUTE_ROOM.CREATED_AT','asc')->get();
        return response()->json($attribute, 200);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attribute = AttributeRoomModel::create($request->all());
        $detail = DetailAttributeRoomModel::create($request->all());
        return response()->json($attribute, 200);
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
