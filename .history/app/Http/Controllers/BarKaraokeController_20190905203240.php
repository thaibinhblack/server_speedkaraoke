<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\BarKaraokeModel;
class BarKaraokeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has('user_create'))
        $karaoke = BarKaraokeModel::where('USER_CREATE',$request->get('user_create'))->orderBy('CREATED_AT', 'asc')->get();
        return response()->json($karaoke, 200);
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
                $file = $request->file('LOGO_BAR_KARAOKE');
                $name = $file->getClientOriginalName();
                $file->move(public_path().'/upload/logo/', $file->getClientOriginalName());
                $path = '/upload/logo/'.$name;
                $data["LOGO_BAR_KARAOKE"] = $path;
                $karaoke = BarKaraokeModel::create($data);
                return response()->json($karaoke, 200);
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
