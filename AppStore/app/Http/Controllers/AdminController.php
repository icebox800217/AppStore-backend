<?php

namespace App\Http\Controllers;
use App\Members;
use App\Apps;
use Illuminate\Support\Facades\Storage;
use App\MemberImgs;
use App\categories;
use Illuminate\Http\Request;

class AdminController extends Controller
{
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

    //顯示所有分類
    public function getAllCategory(categories $categorie)
    {
          return response()->json(categories::all(), 200);
    }

    //管理員新增App分類
    public function newCategory(Request $request)
    {
        $this->validate($request, [
            'category' => 'required|string|max:4|unique:categories',
        ]);
        categories::insert(['category' => $request->category]);
        return response()->json(["boolean" => "True"]);
    }
    

    //管理員修改App分類
    public function updateCategory(categories $category, Request $request, $id)
    {
        $this->validate($request, [
            'category' => 'string|max:4',
        ]);
        categories::whereId($id)->update($request->all());
        return response()->json(["boolean" => "True"]);
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
                    ['img' => Storage::url($path)]
                );
            }
            return response()->json(["boolean" => "True"]);
        } else return response()->json(["boolean" => "False"]);
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

    //列出全部未審核的app
    public function appCheck()
    {
        return Apps::where('apps.verify', '=', 3)
            ->join('members', 'members.id', '=', 'apps.memberId')
            ->select('apps.id', 'apps.appName', 'apps.summary', 'members.name', 'apps.created_at')
            ->get();
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
            Members::where('id', '=', $id)->update(['verify' => 1, 'level' => 2]);
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
    //會員管理
    public function memberManage()
    {
        $count = Members::count();
        $List = Members::where('level', '<', 3)->select('id', 'name', 'phone', 'email', 'level')->get();
        for ($i = 0; $i < $count; $i++) {
            if ($List[$i]->level === 2) //開發者
                $List[$i]->level = '是';
            else if ($List[$i]->level === 1)
                $List[$i]->level = '否';
        }
        return $List;
    }

     //會員停權
     public function stopMember($id)
     {
         $count = Members::where([['id', '=', $id], ['permission', '=', 1]])->count();
         if ($count === 1) {
             Members::where('id', '=', $id)->update(['permission' => 0]);
             $count = Members::count();
             $List = Members::where('level', '<', 3)->select('id', 'name', 'phone', 'email', 'level', 'permission')->get();
             for ($i = 0; $i < $count; $i++) {
                 if ($List[$i]->level === 2) //開發者
                     $List[$i]->level = '是';
                 else if ($List[$i]->level === 1)
                     $List[$i]->level = '否';
             }
             return $List;
         } else return response()->json(["isSuccess" => "False", "reason" => "Member not found"]);
     }

     //會員停權恢復
     public function restoreMember($id)
     {
         $count = Members::where([['id', '=', $id], ['permission', '=', 0]])->count();
         if ($count === 1) {
             Members::where('id', '=', $id)->update(['permission' => 1]);
             $count = Members::count();
             $List = Members::where('level', '<', 3)->select('id', 'name', 'phone', 'email', 'level', 'permission')->get();
             for ($i = 0; $i < $count; $i++) {
                 if ($List[$i]->level === 2) //開發者
                     $List[$i]->level = '是';
                 else if ($List[$i]->level === 1)
                     $List[$i]->level = '否';
             }
             return $List;
         } else return response()->json(["isSuccess" => "False", "reason" => "Member not found"]);
     }

     //App管理
    public function appManage()
    {
        return Apps::where('verify', '=', 1)->select('id', 'appName', 'summary', 'device', 'permission')
            ->get();
    }

    //App停權
    public function stopApp($id)
    {
        $count = Apps::where([['id', '=', $id], ['permission', '=', 1]])->count();
        if ($count === 1) {
            Apps::where('id', '=', $id)->update(['permission' => 0]);
            return Apps::select('id', 'appName', 'summary', 'device', 'permission')->get();
        } else return response()->json(["isSuccess" => "False", "reason" => "App not found"]);
    }
    //App停權恢復
    public function restoreApp($id)
    {
        $count = Apps::where([['id', '=', $id], ['permission', '=', 0]])->count();
        if ($count === 1) {
            Apps::where('id', '=', $id)->update(['permission' => 1]);
            return Apps::select('id', 'appName', 'summary', 'device', 'permission')->get();
        } else return response()->json(["isSuccess" => "False", "reason" => "App not found"]);
    }

    //新增開發者
    public function newDeveloper(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'phone' => ['required', 'regex:/^09\d{8}$/', 'unique:members'],
            'email' => 'required|email|unique:members',
            'idNumber' => ['required', 'regex:/^[A-Z][1,2]\d{8}$/', 'unique:members'],
            'password' => ['required', 'regex:/[0-9A-Za-z]/', 'min:8', 'max:12'],
        ]);
        Members::insert([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'idNumber' => $request->idNumber,
            'password' => md5($request->password),
            'level' => 2
        ]);
        return response()->json(["isSuccess" => "True"]);
    }
}
