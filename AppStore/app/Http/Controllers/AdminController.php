<?php

namespace App\Http\Controllers;

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
            Members::find($id)->update(['password' => md5($request->newPwd)]);
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

    public function addCategory(Request $request)
    {
        if (isset($request->category)) {
            $category = $request->category;
            $count = categorys::where( //查看資料庫內是否有相同分類
                'category',
                '=',
                $category
            )->count();
            if ($count === 0) {
                $categoryName = $request->category;
                categorys::insert(['category' => $categoryName]);
                return response()->json(["isSuccess" => "True"]);
            } else return response()->json(["isSuccess" => "False"]);
        }
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
            $path = Storage::putFileAs('public/icon', $icon, $file_name);
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
        $list = Apps::where('apps.verify', '=', 3)
            ->join('members', 'members.Id', '=', 'apps.memberId')
            ->select('apps.Name', 'apps.summary', 'members.name', 'apps.created_at')
            ->get();
        return $list;
    }
}
