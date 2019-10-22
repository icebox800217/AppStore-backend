<?php

namespace App\Http\Controllers;

use App\Members;
use App\Apps;
use App\AppImgs;
use App\categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Comments;


class MembersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //顯示所有會員
    public function getAllMember(Members $member)
    {
        return response()->json(Members::all(), 200);
    }
    //顯示特定會員
    public function getMember(Members $id)
    {
        return $id;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //創建新會員
    public function newMember(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'phone' => ['required', 'regex:/^09\d{8}$/', 'unique:members'],
            'email' => 'required|email|unique:members',
            'idNumber' => ['required', 'regex:/^[A-Z][1,2]\d{8}$/', 'unique:members'],
            'password' => ['required', 'regex:/[0-9A-Za-z]/', 'min:8', 'max:12'],
        ],['name.required'=>'請填寫姓名欄位'

        ]);
        Members::insert([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'idNumber' => $request->idNumber,
            'password' => md5($request->password),
        ]);
        return response()->json(["isSuccess" => "True"]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //會員修改
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'string',
            'phone' => ['regex:/^09\d{8}$/', 'unique:members'],
            'email' => 'email|unique:members',
            'idNumber' => ['regex:/^[A-Z][1,2]\d{8}$/', 'unique:members'],
            'password' => ['regex:/[0-9A-Za-z]/', 'min:8', 'max:12'],
        ]);

        Members::whereId($id)->update($request->all());
        return response()->json(["boolean" => "True"]);
    }

    //會員登入
    public function login(Request $request)
    {
        if (isset($request->email) && isset($request->password)) {
            $email = $request->email;
            $password = md5($request->password);
            $count = Members::where([
                ['email', '=', $email], ['password', '=', $password]
            ])->count();
            if ($count > 0) {
                $data = Members::where([
                    ['email', '=', $email], ['password', '=', $password]
                ])->join('member_imgs', 'members.imgId', '=', 'member_imgs.id');

                $permission = $data->firstOrFail()->permission; //確認是否被停權

                if ($permission === 1) {
                    $memberinfo = $data->select('members.id','name', 'level', 'img')->firstOrFail();
                    session::put('id', $memberinfo->id);
                    session::put('name', $memberinfo->name);
                    session::put('level', $memberinfo->level);
                    session::put('icon', $memberinfo->img);
                    return $memberinfo;
                } else {
                    return response()->json(["isSuccess" => "False"]);
                }
            } else return  response()->json(["isSuccess" => "False"]);
        }
    }
               
                       
    //會員登出
    public function logout()
    {
        Session::flush();
    }
    //傳送圖片路徑給前端 (尚未使用session 暫用id搜尋)
    public function iconDown(Request $request) 
    {
        $id = $request->input('id');
        $usericon = imgs::where('id', $id)->select('imgData')->get();
        return $usericon;
        
    }

    //取得各分類app
    public function appCategory(Request $request)
     {
         $categoryId = $request->categoryId;
         $all = Apps::where('apps.categoryId', '=', $categoryId)
             ->join('members', 'members.id', '=', 'apps.memberId')
             ->select('apps.id', 'apps.appName', 'apps.summary', 'members.name', 'apps.created_at')
             ->get();
         $count = Apps::count();
         for ($i = 0; $i < $count; $i++) {
             $isnull = Comments::where('appId', '=', $i + 1)->count();
             if ($isnull != 0) {
                 $test[$i] = Comments::where('appId', '=', $i + 1)->avg('star');
             } else {
                 $test[$i] = '尚無評論'; 
             }
         }
          return response()->json(["list" => $all, "star" => $test]);
     }
    //取得最新的app
     public function appLast()
     {
        $appLast = Apps::latest('created_at')->take(3)->get();
        return $appLast;
     }

    //列出最熱門的app
     public function appHot()
     {   
        $appHot= Apps::OrderBy('downloadTimes','desc')->take(3)->get();
        return $appHot;
        
     }
    //搜尋功能
    public function search(Request $request)
    {   
       $search = Apps::query()->join('members', 'members.id', '=', 'apps.memberId')
        ->where('appName', 'like', "%{$request->searchTerm}%") 
        ->orWhere('tags', 'like', "%{$request->searchTerm}%") 
        ->orWhere('members.name', 'like', "%{$request->searchTerm}%") 
        ->orderby('appName')
        ->orderby('tags')
        ->orderby('members.name')
        ->get();
        return $search;
    }
    
    //顯示特定App
    public function getApp(Apps $id)
    {
        return $id;
    }

    //顯示特定App截圖
    public function getAppimg(Request $request)
    {
        return AppImgs::where('appId', '=', $request->id)
        ->get();
    }

    //顯示評論
    public function getcomment(Apps $id, Comments $comments)
    {
        return Comments::where('appId', '=', $id->id)
        ->join('apps', 'apps.id', '=', 'Comments.appId')
        ->select('comment', 'star')
        ->get();
    }

    //評論功能
    public function comment(Request $request, $id)
    {
        $this->validate($request, [
            'comment' => 'required|string|max:255',
            'star' => 'required'
        ]);

        Comments::insert([
            'memberId' => $id,
            'appId' => $request->appId,
            'comment' => $request->comment,
            'star' => $request->star            
        ]);
        return response()->json(["isSuccess" => "True"]);
    }

    //修改評論
    public function upcomment(Request $request, $id)
    {
        $this->validate($request, [
            'comment' => 'string|max:255'
        ]);
        Comments::whereId($id)->update($request->all());
        return response()->json(["isSuccess" => "True"]);
    }

}
