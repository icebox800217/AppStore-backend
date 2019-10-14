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
    //上傳App
    public function appUp(Request $request)
    {
        if ($request->hasFile('file') && $request->hasFile('icon') && $request->hasFile('img1') && $request->hasFile('img2')) {
            $icon = $request->file('icon');   //icon
            $icon_extension = strtolower($icon->getClientOriginalExtension());
            if (
                $icon_extension === 'png' || $icon_extension === 'jpeg' ||
                $icon_extension === 'jpg' || $icon_extension === 'gif'
            ) {
                $icon_name = time() . rand(100000, 999999) . '.' . $icon_extension;
                $icon_path = Storage::putFileAs('public/icon', $icon, $icon_name);
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
                    $filepath = Storage::putFileAs('public/file/android', $file, $file_name);
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
                    $filepath = Storage::putFileAs('public/file/ios', $file, $file_name);
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
                $img1path = Storage::putFileAs('public/screen', $img1, $img1_name);
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
                $img2path = Storage::putFileAs('public/screen', $img2, $img2_name);
                AppImgs::insert(
                    [
                        'appId' => $app->id, 'screenShot' =>  $img2path,
                    ]
                );
            } else return response()->json(["isSuccess" => "False", "reason" => "img2 extension error"]);
            return response()->json(["isSuccess" => "True"]);
        } else return response()->json(["isSuccess" => "False", "reason" => "one of the upload is empty"]);
    }

    //自己的App排行
    public function appRank($id)
    {
        return Apps::where('memberId', '=', $id)->orderBy('downloadTimes', 'desc')
            ->select('id', 'appName', 'summary', 'downloadTimes', 'appIcon')->get();
    }

    //自己開發的所有App列表
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
}
