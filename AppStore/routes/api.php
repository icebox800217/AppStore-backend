<?php

use App\Http\Controllers\AdminController;
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
Route::POST('/login', 'MembersController@login');
//登入會員
Route::POST('/logout', 'MembersController@logout');
//登出會員
route::POST('/Admin/newIcon', 'AdminController@newIcon');
//管理員新增會員頭像


//林林的管理者畫面
//修改密碼
Route::PUT('/Admin/{id}','AdminController@pwdChange');
//列出未審核app
Route::GET('/Admin/appCheck','AdminController@appCheck');
//計算未審app數、未審開發人員數 及 列出下載量前五名的app
Route::GET('/Admin/countAll','AdminController@countAll');
//未審核app-開發者搜尋
Route::POST('/Admin/searchMember','AdminController@searchMember');
