<?php

namespace App\Http\Controllers;

//Models
use App\Models\Bigarea;
use App\Models\Area;

//Services
use App\Services\GetApiContents;
use App\Services\GetWeather;

//Auth
use Illuminate\Support\Facades\Auth;

//Request
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;

class AreaController extends Controller
{

    /**
     * データをチェックしてAreasテーブルにデータをプッシュ
     * 
     * @param Request $request
     * return redirect
     */
    public function addArea(Request $request)
    {
        $rules = [
            'harbor_code' => 'required|integer|between:1:1000',
            'area_name' => 'required|string|max:10',
            'area_zip' => 'required|string|max:8'
        ];
        
        $this->validate($request, $rules);
        
        //天気Apiチェック
        $apiid = config('app.weather_app_id');
        $url = "http://api.openweathermap.org/data/2.5/forecast?lang=ja&zip=".$request->area_zip."&units=metric&APPID=".$apiid;
        if($data = GetApiContents::tryGetContents($url)){
            if($data->cod != '200'){
                return redirect('area/edit')->with('flash_message','住所がopenweatherapiに非対応のもの、または不正です。別の住所で試してください');
            }
        } else {
            abort(404);
        }

        //TideApiチェック
        $url = "https://api.tide736.net/get_tide.php?pc=".$request->prefecture."&hc=".$request->harbor_code."&yr=2021&mn=10&dy=10&rg=week";
        if($data = GetApiContents::tryGetContents($url)){
            if($data->status != '1'){
                return redirect('area/edit')->with('flash_message','県、港コードが正しくありません。tide736で再確認してください');
            }
        } else {
            abort(404);
        }

        $area = new Area;

        $area->bigarea_id = $request->prefecture;
        $area->harbor_id = $request->harbor_code;
        $area->area_name = $request->area_name;
        $area->area_zip = $request->area_zip.",jp";

        $area->save();

        return redirect('area/edit');
    }

    /**
     * 管理者だけeditpageに遷移
     * 
     * return view
     */
    public function toEditAreaPage()
    {
        //管理者じゃなかった場合はエラーページを返す
        if(Auth::check() && Auth::user()->admin){
            $bigareas = Bigarea::all();
            return view('area.edit',compact('bigareas'));
        }
        return view('error.admin');
    }

    /**
     * エリア別のページを表示
     *
     * @param interger $area_id
     * return view
     */
    public function showArea($area_id){

        $weather = new GetWeather($area_id);

        $areas = Area::all();
        
        $info = $weather->setInfo();

        $area_name = Area::find($area_id)->area_name;

        return view('area',compact('info','areas','area_id','area_name'));
    }


    public function index()
    {
        $areas = Area::all();
        $bigareas = Bigarea::all();
        return view('home',compact('areas','bigareas'));
    }
}
