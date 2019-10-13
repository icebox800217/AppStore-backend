<?php

namespace App\Http\Controllers;
use App\MemberImgs;
use App\Members;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


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
                ])->join('member_imgs', 'members.imgId', '=', 'member_imgs.Id');

                $right = $data->firstOrFail()->right; //確認是否被停權
                if ($right === 1) {
                    $memberinfo =  $data->select('members.id', 'name', 'level', 'img')->firstOrFail();
                    session::put('memberId', $memberinfo->id);
                    session::put('name', $memberinfo->name);
                    session::put('level', $memberinfo->level);
                    session::put('icon', $memberinfo->url);
                    
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
}
