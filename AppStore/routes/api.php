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


//////////////////////////////////////////////////////////////////
//砲台哥的開發者畫面
//上傳App(Android)
Route::POST('/develop/Android', 'DevelopController@ApkUp');
//上傳App(Ios)
Route::POST('/develop/Ios', 'DevelopController@IpaUp');
//列出該開發者自己的App download Rank
Route::GET('/develop/appRank/{id}', 'DevelopController@appRank');
//自己開發的所有App列表(含審核狀態)
Route::GET('/develop/appList/{id}', 'DevelopController@appList');
//上傳檔案畫面 - 分類列表
Route::GET('/develop/categories', 'DevelopController@categoryList');
//////////////////////////////////////////////////////////////////
//林林的管理者畫面
//計算未審app數、未審開發人員數 及 列出下載量前五名的app
Route::GET('/Admin/countAll', 'AdminController@countAll');
//修改密碼
Route::PUT('/Admin/{id}', 'AdminController@pwdChange');
//列出未審核app
Route::GET('/Admin/appCheck', 'AdminController@appCheck');
//App審核通過 
Route::PUT('/Admin/appCheckOk/{id}', 'AdminController@appCheckOk');
//App審核失敗-退回 
Route::PUT('/Admin/appGoBack/{id}', 'AdminController@appGoBack');

//列出未審核之開發者申請
Route::GET('/Admin/devCheck', 'AdminController@devCheck');
//開發者審核通過 
Route::PUT('/Admin/devCheckOk/{id}', 'AdminController@devCheckOk');
//開發者審核失敗-退回 
Route::PUT('/Admin/devGoBack/{id}', 'AdminController@devGoBack');

//App管理
Route::GET('/Admin/appManage', 'AdminController@appManage');
//會員管理
Route::GET('/Admin/memberManage', 'AdminController@memberManage');
//新增分類
route::POST('/Admin/category', 'AdminController@addCategory');

//會員停權
Route::PUT('/Admin/stopMember/{id}', 'AdminController@stopMember');
//會員停權恢復
Route::PUT('/Admin/restoreMember/{id}', 'AdminController@restoreMember');
//App停權
Route::PUT('/Admin/stopApp/{id}', 'AdminController@stopApp');
//App停權恢復
Route::PUT('/Admin/restoreApp/{id}', 'AdminController@restoreApp');

//新增開發者
Route::POST('/Admin/newDeveloper', 'AdminController@newDeveloper');
//類別名稱及該類別APP數量
Route::GET('/Admin/countCategory', 'AdminController@countCategory');

route::POST('/Admin/newIcon', 'AdminController@newIcon');
//管理員新增會員頭像

route::POST('/Admin/appCategory', 'AdminController@appCategory');