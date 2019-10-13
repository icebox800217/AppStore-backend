<?php

namespace App\Http\Controllers;

use App\Categories;
use Illuminate\Support\Facades\Storage;
use App\MemberImgs;
use Illuminate\Http\Request;
use App\Members;
use App\Apps;

class AdminController extends Controller
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

    //修改密碼(待討論是否可以修改Name)
    public function pwdChange(Members $member, Request $request, $id)
    {

        $this->validate($request, [
            'name' => 'required|string',
            'oldPwd' => ['required', 'regex:/[0-9A-Za-z]/', 'min:8', 'max:12'],
            'newPwd' => ['required', 'regex:/[0-9A-Za-z]/', 'min:8', 'max:12'],
            'pwdCheck' => ['required', 'same:newPwd'],
        ]);
        // $id = session::get('member_id');
        $oldPwd = md5($request->oldPwd);
        $count = Members::where([
            ['id', '=', $id], ['password', '=', $oldPwd]
        ])->count();
        if ($count === 1) {
            Members::where('id', '=', $id)->update(['password' => md5($request->newPwd)]);
            return response()->json(["isSuccess" => "True"]);
        } else return response()->json(["isSuccess" => "False"]);
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

    //新增分類
    public function addCategory(Request $request)
    {
        $this->validate($request, [
            'category' => 'required|string|unique:categories',
        ]);
        if (isset($request->category)) {
            $category = $request->category;
            Categories::insert(['category' => $category]);
            return response()->json(["isSuccess" => "True"]);
        } else return response()->json(["isSuccess" => "False"]);
    }


    //管理員新增會員頭像
    public function newIcon(Request $request)
    {
        $icon = $request->file('imgs');
        $extension = strtolower($icon->getClientOriginalExtension()); //副檔名轉小寫
        if (
            $extension === 'png' || $extension === 'jpeg' ||
            $extension === 'jpg' || $extension === 'gif'
        ) {
            $file_name = date('ymdHisu') . '.' . $extension;
            // $file_name = time(). '.' . $extension;
            $path = Storage::putFileAs('public/Member_icon', $icon, $file_name);
            if ($icon->isValid()) {
                MemberImgs::insert(
                    ['img' => $path,]
                );
            }
            return response()->json(["isSuccess" => "True"]);
        } else return response()->json(["isSuccess" => "False"]);
    }

    //列出全部未審核的app
    public function appCheck()
    {
        return Apps::where('apps.verify', '=', 3)
            ->join('members', 'members.id', '=', 'apps.memberId')
            ->select('apps.id', 'apps.appName', 'apps.summary', 'members.name', 'apps.created_at')
            ->get();
    }

    //管理者首頁 - 計算未審app數、未審開發人員數 及 列出下載量前五名的app
    //前端接口為appCount、devCount、top5
    public function countAll()
    {
        $unCheck_app_count = Apps::where('verify', '=', 3)->count();
        $unCheck_dev_Count = Members::where('verify', '=', 0)->count();
        $top5dowload = Apps::orderBy('downloadTimes', 'desc')->take(5)->pluck('appName');
        return response(['appCount' => $unCheck_app_count, 'devCount' => $unCheck_dev_Count, 'top5' => $top5dowload]);
    }

    //App審核通過 (並return剩餘未審核) 
    //回傳欄位名有修改請告知前端
    public function appCheckOk($id)
    {
        $count = Apps::where('id', '=', $id)->count();
        if ($count === 1) {
            Apps::where('id', '=', $id)->update(['verify' => 1]);
            return Apps::where('apps.verify', '=', 3)
                ->join('members', 'members.id', '=', 'apps.memberId')
                ->select('apps.id', 'apps.appName', 'apps.summary', 'members.name', 'apps.created_at')
                ->get();
        } else return response()->json(["isSuccess" => "False"]);
    }
    //App審核失敗-退回
    public function appGoBack($id)
    {
        $count = Apps::where('id', '=', $id)->count();
        if ($count === 1) {
            Apps::where('id', '=', $id)->update(['verify' => 2]);
            return Apps::where('apps.verify', '=', 3)
                ->join('members', 'members.id', '=', 'apps.memberId')
                ->select('apps.id', 'apps.appName', 'apps.summary', 'members.name', 'apps.created_at')
                ->get();
        } else return response()->json(["isSuccess" => "False"]);
    }

    //列出未審核之開發者申請
    public function devCheck()
    {
        return Members::where('verify', '=', 0)
            ->select('id', 'name', 'updated_at')
            ->get();
    }

    //開發者審核通過 (並return剩餘未審核) 
    //回傳欄位名有修改請告知前端
    public function devCheckOk($id)
    {
        $count = Members::where('id', '=', $id)->count();
        if ($count === 1) {
            Members::where('id', '=', $id)->update(['verify' => 1]);
            return Members::where('verify', '=', 0)
                ->select('id', 'name', 'updated_at')
                ->get();
        } else return response()->json(["isSuccess" => "False"]);
    }
    //開發者審核失敗-退回
    public function devGoBack($id)
    {
        $count = Members::where('id', '=', $id)->count();
        if ($count === 1) {
            Members::where('id', '=', $id)->update(['verify' => null]);
            return Members::where('verify', '=', 0)
                ->select('id', 'name', 'updated_at')
                ->get();
        } else return response()->json(["isSuccess" => "False"]);
    }
}
