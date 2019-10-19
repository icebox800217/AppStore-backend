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

//會員api
//////////////////////////////////////////////////////////////
Route::GET('/member', 'MembersController@getAllMember');
//顯示所有會員
Route::GET('/member/{id}', 'MembersController@getMember');
//顯示特定會員
route::POST('/member', 'MembersController@newMember');
//創建新會員
route::PUT('/member/{id}', 'MembersController@update');
//修改會員資料
Route::POST('/member/login','MembersController@login');
//登入會員
Route::POST('/member/logout','MembersController@logout');
//登出會員
Route::POST('/member/appCategory','MembersController@appCategory');
//取得各分類app
Route::GET('/appLast','MembersController@appLast');
//取得最新的app
Route::GET('/appHot','MembersController@appHot');
//取得最熱門的app
Route::POST('/member/search','MembersController@search');
//搜尋功能


//開發者api
//////////////////////////////////////////////////////////////
Route::POST('/develop/upload', 'DevelopController@appUp');
//開發者上傳App
Route::GET('/develop/appRank/{id}', 'DevelopController@appRank');
//開發者的App下載次數
Route::GET('/develop/appList/{id}', 'DevelopController@appList');
//開發者的所有App列表(含審核狀態)
Route::GET('/develop/categories', 'DevelopController@categoryList');
//上傳檔案畫面 - 分類列表

//管理者api
//////////////////////////////////////////////////////////////
route::PUT('/Admin/pwdChange/{id}', 'AdminController@pwdChange');
//會員修改密碼(需驗證新舊密碼)
route::GET('/getAllCategory', 'AdminController@getAllCategory');
//顯示所有App分類
route::POST('/Admin/newCategory', 'AdminController@newCategory');
//管理員新增App分類
route::PUT('/Admin/updateCategory/{id}', 'AdminController@updateCategory');
//管理員修改App分類
route::POST('/Admin/newIcon', 'AdminController@newIcon');
//管理員新增會員頭像
Route::GET('/Admin/countAll', 'AdminController@countAll');
//計算未審app數、未審開發人員數 及 列出下載量前五名的app
Route::GET('/Admin/appCheck', 'AdminController@appCheck');
//列出未審核app
Route::PUT('/Admin/appCheckOk/{id}', 'AdminController@appCheckOk');
//App審核通過 
Route::PUT('/Admin/appGoBack/{id}', 'AdminController@appGoBack');
//App審核失敗-退回 
Route::GET('/Admin/devCheck', 'AdminController@devCheck');
//列出未審核之開發者申請
Route::PUT('/Admin/devCheckOk/{id}', 'AdminController@devCheckOk');
//開發者審核通過 
Route::PUT('/Admin/devGoBack/{id}', 'AdminController@devGoBack');
//開發者審核失敗-退回 
Route::GET('/Admin/memberManage', 'AdminController@memberManage');
//會員管理
Route::PUT('/Admin/stopMember/{id}', 'AdminController@stopMember');
//會員停權
Route::PUT('/Admin/restoreMember/{id}', 'AdminController@restoreMember');
//會員停權恢復
Route::GET('/Admin/appManage', 'AdminController@appManage');
//App管理
Route::PUT('/Admin/stopApp/{id}', 'AdminController@stopApp');
//App停權
Route::PUT('/Admin/restoreApp/{id}', 'AdminController@restoreApp');
//App停權恢復
Route::POST('/Admin/newDeveloper', 'AdminController@newDeveloper');
//新增開發者
Route::GET('/Admin/countCategory', 'AdminController@countCategory');
//類別名稱及該類別APP數量





