<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\ProvinceModel;
use App\model\DistrictModel;
use App\model\BarKaraokeModel;
class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $province = ProvinceModel::all();
        return response()->json($province, 200);
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
        
    }

    public function search(Request $request)
    {
        if($request->has('search'))
        {
            $province = DistrictModel::where("NAME_DISTRICT","like",$request->get("search").'%')->first();
            
        }
        else
        {
            $province = ProvinceModel::where("NAME_PROVINCE","like",$request->get("NAME_PROVINCE").'%')->first();
        }
        if($province)
        {
            $ID_PROVINCE = $province->ID_PROVINCE;
        }
        else {
            $ID_PROVINCE = 12;
        }
        $karaoke = BarKaraokeModel::join('table_province','table_bar_karaoke.ID_PROVINCE','table_province.ID_PROVINCE')
            ->join('table_district','table_bar_karaoke.ID_DISTRICT','table_district.ID_DISTRICT')
            ->where("table_bar_karaoke.ID_PROVINCE",$ID_PROVINCE)
            ->select('table_bar_karaoke.*','table_province.NAME_PROVINCE','table_district.NAME_DISTRICT')
            ->orderBy("NUMBER_REATED","desc","STAR_RATING","desc")
            ->get();
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
        $provinces = ProvinceModel::where("ID_PROVINCE",$id)->first();
        return response()->json($provinces, 200);
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
