<?php

use Illuminate\Http\Request;
use App\Member;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::GET('/member', 'MembersController@getAllMember');
//顯示所有會員
Route::GET('/member/{id}', 'MembersController@getMember');
//顯示特定會員
route::POST('/member', 'MembersController@newMember');
//創建新會員
route::PUT('/member/{id}', 'MembersController@update');
//修改會員資料
Route::POST('/login','MembersController@login');
//登入會員
Route::POST('/logout','MembersController@logout');
//登出會員
Route::POST('/appCategory','MembersController@appCategory');
//取得各分類app
Route::GET('/appLast','MembersController@appLast');
//取得最新的app
Route::GET('/appHot','MembersController@appHot');
//取得最熱門的app


route::PUT('/pwdChange/{id}', 'AdminController@pwdChange');
//會員修改密碼(需驗證新舊密碼)

route::GET('/getAllCategory', 'AdminController@getAllCategory');
//顯示所有App分類
route::POST('/newCategory', 'AdminController@newCategory');
//管理員新增App分類
route::PUT('/updateCategory/{id}', 'AdminController@updateCategory');
//管理員修改App分類


route::POST('/Admin/newIcon', 'AdminController@newIcon');
//管理員新增會員頭像

Route::POST('/develop/upload', 'DevelopController@appUp');
//開發者新增App
