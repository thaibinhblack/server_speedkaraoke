<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\AttributeRoomModel;
use App\model\DetailAttributeRoomModel;
use Illuminate\Support\Str;
use App\model\HistoryModel;
use App\model\UserModel;
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
            $attribute = AttributeRoomModel::join("table_detail_attribute_room", "table_attribute_room.UUID_ATTRIBUTE_ROOM", "table_detail_attribute_room.UUID_ATTRIBUTE_ROOM")
            ->where('UUID_ROOM_BAR_KARAOKE',$request->get("UUID_ROOM_BAR_KARAOKE"))->orderBy('table_detail_attribute_room.CREATED_AT','asc')->get();
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
        if($request->has("api_token"))
        {
            $user = UserModel::where("USER_TOKEN",$request->get('api_token'))->first();
            if($user)
            {
                $uuid = Str::uuid();
                $attribute = AttributeRoomModel::create([
                    "UUID_ATTRIBUTE_ROOM" => $uuid,
                    "NAME_ATTRIBUTE" => $request->get("NAME_ATTRIBUTE"),
                    "CONTENT_ATTRIBUTE" => $request->get("CONTENT_ATTRIBUTE"),
                    "USER_CREATE" => $user->EMAIL
                ]);
                DetailAttributeRoomModel::create([
                    "UUID_ROOM_BAR_KARAOKE" => $request->get("UUID_ROOM_BAR_KARAOKE"),
                    "UUID_ATTRIBUTE_ROOM" => $uuid
                ]);
                HistoryModel::create([
                    "UUID_HISTORY_ACTION" => Str::uuid(),
                    "UUID_USER" => $user->UUID_USER,
                    "NAME_HISTORY" => "Thuộc tính phòng",
                    "CONTENT_ACTION" => $user->EMAIL.' thêm thuộc tính '.$request->get("NAME_ATTRIBUTE").' với giá trị là'.$request->get("CONTENT_ATTRIBUTE")
                ]);
                return response()->json('success', 200);
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'Authorizon'
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
        // $data = $request->all();
        if($request->has('api_token'))
        {
            $user = UserModel::where("USER_TOKEN",$request->get("api_token"));
            AttributeRoomModel::where("UUID_ATTRIBUTE_ROOM",$id)->update([
                "NAME_ATTRIBUTE" => $request->get("NAME_ATTRIBUTE"),
                "CONTENT_ATTRIBUTE" => $request->get("CONTENT_ATTRIBUTE")
            ]);
            HistoryModel::create([
                "UUID_HISTORY_ACTION" => Str::uuid(),
                "UUID_USER" => $user->UUID_USER,
                "NAME_HISTORY" => 'Cập nhật thuộc tính phòng',
                "CONTENT_ACTION" => $user->EMAIL.' đã cập nhật thuộc tính phòng '
            ])
        }
        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DetailAttributeRoomModel::where("UUID_ATTRIBUTE_ROOM",$id)->delete();
        $attribute = AttributeRoomModel::where("UUID_ATTRIBUTE_ROOM", $id)->delete();
        return response()->json($attribute, 200);
    }
}
