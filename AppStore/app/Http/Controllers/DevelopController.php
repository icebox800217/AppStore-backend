<?php

namespace App\Http\Controllers;

use App\Apps;
use App\AppImgs;
use App\Categories;
use App\Members;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class DevelopController extends Controller
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
    
    
   //開發者上傳App
   public function appUp(Request $request)
   {

    //return response()->json($request->file('file')->getClientOriginalExtension());

       if ($request->hasFile('file') && $request->hasFile('icon') && $request->hasFile('img1') && $request->hasFile('img2')) {
           $icon = $request->file('icon');   //icon
           $icon_extension = strtolower($icon->getClientOriginalExtension());
           if (
               $icon_extension === 'png' || $icon_extension === 'jpeg' ||
               $icon_extension === 'jpg' || $icon_extension === 'gif'
           ) {
               $icon_name = time() . rand(100000, 999999) . '.' . $icon_extension;
               $icon_path = Storage::putFileAs('icon', $icon, $icon_name);
           } else return response()->json(["isSuccess" => "False", "reason" => "icon extension error"]);
           $file = $request->file('file');
           $file_extension = $file->getClientOriginalExtension();
           $version = str_replace('.', '_', $request->version); //將版本的點換成底線
           $file_name = time() . rand(100000, 999999) . $version . '.' . $file_extension;
           if ($file->isValid()) {
               $this->validate($request, [
                   'appName' => 'required|string|max:50',
                   'summary' => 'required|string|max:50',  //簡短介紹
                   'introduction' => 'required|string',   //說明
                   'tags' => 'required|string|max:20',
                   'version' => ['required', 'string', 'max:20', 'regex:/^[0-1]\.[0-9]*\.[0-9]$/'],
                   'changelog' => 'required|string',  //更新異動
               ]);
               if ($file_extension === 'apk') {
                   $filepath = Storage::putFileAs('file/android', $file, $file_name);
                   apps::insert([
                       'appName' => $request->appName,
                       'memberId' => $request->memberId,
                       'summary' => $request->summary,
                       'introduction' => $request->introduction,
                       'appIcon' => $icon_path,
                       'categoryId' => $request->categoryId,
                       'tags' => $request->tags,
                       'device' => 'android',
                       'version' => $request->version,
                       'changelog' => $request->changelog,
                       'fileURL' => $filepath,
                   ]);
               } else if ($file_extension === 'ipa') {
                   $filepath = Storage::putFileAs('file/ios', $file, $file_name);
                   apps::insert([
                       'appName' => $request->appName,
                       'memberId' => $request->memberId,
                       'summary' => $request->summary,
                       'introduction' => $request->introduction,
                       'appIcon' => $icon_path,
                       'categoryId' => $request->categoryId,
                       'tags' => $request->tags,
                       'device' => 'ios',
                       'version' => $request->version,
                       'changelog' => $request->changelog,
                       'fileURL' => $filepath,
                   ]);
               } else return response()->json(["isSuccess" => "False2"]);
           } else return response()->json(["isSuccess" => "False", "reason" => "file is unvalid"]);
           $app = Apps::where('fileURL', $filepath)->firstOrFail();
           $img1 = $request->file('img1'); //截圖1
           $img1_extension = strtolower($img1->getClientOriginalExtension());
           if (
               $img1_extension === 'png' || $img1_extension === 'jpeg' ||
               $img1_extension === 'jpg' || $img1_extension === 'gif'
           ) {
               $img1_name = time() . rand(100000, 999999) . '.' . $img1_extension;
               $img1path = Storage::putFileAs('screen', $img1, $img1_name);
               AppImgs::insert(
                   [
                       'appId' => $app->id, 'screenShot' =>  $img1path,
                   ]
               );
           } else return response()->json(["isSuccess" => "False", "reason" => "img1 extension error"]);
           $img2 = $request->file('img2'); //截圖2
           $img2_extension = strtolower($img2->getClientOriginalExtension());
           if (
               $img2_extension === 'png' || $img2_extension === 'jpeg' ||
               $img2_extension === 'jpg' || $img2_extension === 'gif'
           ) {
               $img2_name = time() . rand(100000, 999999) . '.' . $img2_extension;
               $img2path = Storage::putFileAs('screen', $img2, $img2_name);
               AppImgs::insert(
                   [
                       'appId' => $app->id, 'screenShot' =>  $img2path,
                   ]
               );
           } else return response()->json(["isSuccess" => "False", "reason" => "img2 extension error"]);
           return response()->json(["isSuccess" => "True"]);
       } else return response()->json(["isSuccess" => "False", "reason" => "one of the upload is empty"]);
   }

    //開發者的App下載次數
    public function appRank($id)
    {
        // $contents = Storage::get('file.jpg');
        $count = Apps::where('memberId', '=', $id)->count();
        $List = Apps::where('memberId', '=', $id)->orderBy('downloadTimes', 'desc')
            ->select('id', 'appName', 'summary', 'downloadTimes', 'appIcon')->get();
        for ($i = 0; $i < $count; $i++) {
        $List[$i]->appIcon = asset($List[$i]->appIcon);
        }
        return $List;
    }
    //開發者的所有App列表(含審核狀態)
    public function appList($id)
    {
        $count = Apps::where('memberId', '=', $id)->count();
        $appList = Apps::where('memberId', '=', $id)->select('id', 'appName', 'summary', 'created_at', 'verify')->get();
        for ($i = 0; $i < $count; $i++) {
            if ($appList[$i]->verify === 3)
                $appList[$i]->verify = '待審核';
            else if ($appList[$i]->verify === 2)
                $appList[$i]->verify = '退回';
            else if ($appList[$i]->verify === 1)
                $appList[$i]->verify = '審核通過';
        }
        return $appList;
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $version = str_replace('.', '_', $request->version); //將版本的點換成底線
            $file_name = date('ymdHisu') . $version . '.' . $extension;
            // return ($file_name);
            if ($file->isValid()) {
                $this->validate($request, [
                    'appName' => 'required|string|max:50',
                    'summary' => 'required|string|max:50',  //簡短介紹
                    'introduction' => 'required|string',   //說明
                    'tags' => 'required|string|max:20',
                    'version' => ['required', 'string', 'max:20', 'regex:/^[0-1]\.[0-9]*\.[0-9]$/'],
                    'changelog' => 'required|string',  //更新異動
                ]);

                if ($extension == 'apk') {
                    $path = Storage::putFileAs('public/file/android', $file, $file_name);
                    apps::insert([
                        'appName' => $request->appName,
                        'memberId' => '1',
                        'summary' => $request->summary,
                        'introduction' => $request->introduction,
                        'imgId' => '1',
                        'categoryId' => '1',
                        'tags' => $request->tags,
                        'device' => 'android',
                        'version' => $request->version,
                        'changelog' => $request->changelog,
                        'fileURL' => $path,
                    ]);
                    return response()->json(["boolean" => "True"]);
                } else if ($extension == 'ipa') {
                    $path = Storage::putFileAs('public/file/ios', $file, $file_name);
                    apps::insert([
                        'appName' => $request->appName,
                        'memberId' => '1',
                        'summary' => $request->summary,
                        'introduction' => $request->introduction,
                        'imgId' => '1',
                        'categoryId' => '1',
                        'tags' => $request->tags,
                        'device' => 'ios',
                        'version' => $request->version,
                        'changelog' => $request->changelog,
                        'fileURL' => $path,
                    ]);
                    return response()->json(["boolean" => "True"]);
                } else return response()->json(["boolean" => "False"]);
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
