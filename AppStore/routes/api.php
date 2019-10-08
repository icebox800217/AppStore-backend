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

Route::GET('/member/{id}', 'MembersController@getMember');

route::POST('/member', 'MembersController@store');

Route::POST('/login','MembersController@login');

Route::POST('/icon','MembersController@iconUp');

