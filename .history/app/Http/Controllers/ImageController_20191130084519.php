<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\ImageModel;
use Illuminate\Support\Str;
class ImageController extends Controller
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
        $data = $request->all();
        $file = $request->file('URL_IMAGE');
        $name = $file->getClientOriginalName();
        $file->move(public_path().'/upload/'.$request->get('type_image').'/', $file->getClientOriginalName());
        $path = '/upload/'.$request->get('type_image').'/'.$name;
        $data["URL_IMAGE"] = $path;
        $image = ImageModel::create($data);
        return response()->json($image, 200);
    }
    public function upload(Request $request)
    {
        // return response()->json($request->file('IMAGES'), 200);
        if($request->has('IMAGES'))
        {
            $UUID_BAR_KARAOKE = null;
            if($request->has('UUID_BAR_KARAOKE')){
                $UUID_BAR_KARAOKE = $request->get("UUID_BAR_KARAOKE");
            }
            $UUID_ROOM_BAR_KARAOKE = null;
            if($request->has('UUID_ROOM_BAR_KARAOKE')){
                $UUID_ROOM_BAR_KARAOKE = $request->get("UUID_ROOM_BAR_KARAOKE");
            }
            $file = $request->file('IMAGES');
            $file->move(public_path().'/upload/camera/', $file->getClientOriginalName());
            $path = '/upload/camera/'.$file->getClientOriginalName();
            
            $image = ImageModel::create([
                "UUID_IMAGE" => Str::uuid(),
                "UUID_BAR_KARAOKE" => $UUID_BAR_KARAOKE,
                "UUID_ROOM_BAR_KARAOKE" => $UUID_ROOM_BAR_KARAOKE,
                "URL_IMAGE" => $path
            ]);
            return response()->json($image, 200);   
        }
        if($request->has('firebase'))
        {
            $UUID_BAR_KARAOKE = null;
            if($request->has('UUID_BAR_KARAOKE')){
                $UUID_BAR_KARAOKE = $request->get("UUID_BAR_KARAOKE");
            }
            $UUID_ROOM_BAR_KARAOKE = null;
            if($request->has('UUID_ROOM_BAR_KARAOKE')){
                $UUID_ROOM_BAR_KARAOKE = $request->get("UUID_ROOM_BAR_KARAOKE");
            }
            $image = ImageModel::create([
                "UUID_IMAGE" => Str::uuid(),
                "UUID_BAR_KARAOKE" => $UUID_BAR_KARAOKE,
                "UUID_ROOM_BAR_KARAOKE" => $UUID_ROOM_BAR_KARAOKE,
                'TYPE' => 'firebase',
                "URL_IMAGE" => $request->get("URL_IMAGE")
            ]);
            return response()->json($image, 200);  
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        if($request->has('type'))
        {
            $images = ImageModel::where($request->get('type'),$id)->get();
            return response()->json($images, 200);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if($request->has("UUID_IMAGE"))
        {
            $image =ImageModel::where("UUID_IMAGE",$request->get("UUID_IMAGE"))->delete();
            return response()->json($image, 200);
        }
    }
}