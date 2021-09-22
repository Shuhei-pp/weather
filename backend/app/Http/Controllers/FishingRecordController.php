<?php

namespace App\Http\Controllers;

//Auth
use Illuminate\Support\Facades\Auth;

//Models
use App\Models\FishingRecord;
use App\Models\Area;

use Illuminate\Http\Request;

class FishingRecordController extends Controller
{
    /**
     * 釣果登録ページに遷移
     * 
     * @param $fresult_id
     * 
     */
    public function toEditPage($fresult_id){
        //非ログインはリダイレクト
        if(!Auth::check()){
            return redirect('login')->with('flash_message','ログインしてください');
        }

        $fresult = FishingRecord::where('id', $fresult_id)->first();
        $areas = Area::all();

        if(Auth::user()->id != $fresult->user_id){
            return redirect('login')->with('flash_message','投稿を出しているユーザーとは違うユーザーです。ログインし直してください');
        }

        return view('fresult.p_edit',compact('fresult','areas'));

    }


    /**
     * 釣果を登録
     * 
     * Illuminate\Http\Request $request
     */
    public function create(Request $request){

        //ログインしていない場合ログインページへ遷移
        if (!(Auth::check()))
        {
            return redirect('login')->with('flash_message','釣果登録システムを利用する場合はログインしてください');
        }

        //バリデーションルール
        $rules = [
            'content' => 'required|string|max:256',
            'picture' => 'required|file|image|mimes:jpeg,jpg,png|max:2048',
            'time' => 'required|before:"now"'
        ];

        $this->validate($request,$rules);

        $image_path = $request->file('picture')->store('public/result_images/');

        $frecord = new FishingRecord();
        $frecord->user_id = Auth::user()->id;
        $frecord->content = $request->content;
        $frecord->area_id = $request->area_id;
        $frecord->image_name = basename($image_path);
        $frecord->time = $request->time;

        $frecord->save();

        return redirect('/')->with('flash_message','釣果登録が完了しました');
    }
}
