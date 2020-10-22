<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::post('auth/login', 'AuthApiController@login');
Route::post('auth/register', 'AuthApiController@register');

Route::group(['middleware' => ['can:website.view']], function() {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('auth/logout', 'AuthApiController@logout');
        Route::get('auth/user', 'AuthApiController@user');
        Route::post('upload', 'UploadApiController@store')->middleware('can:upload');

        //Admin
        Route::group(['middleware' => 'can:admin.view'], function() {
            //Admin - Packages
            Route::group(['middleware' => 'can:admin.packages'], function() {
                Route::get('package', 'PackageApiController@installed');
                Route::post('package/enable', 'PackageApiController@enable');
                Route::post('package/disable', 'PackageApiController@disable');
            });

            Route::group(['middleware' => 'can:admin.permissions'], function() {
                Route::get('permission', 'PermissionApiController@get');
                Route::get('role', 'RoleApiController@get');
            });
        });
    });

    Route::get('album', 'AlbumApiController@get');
    Route::get('artist', 'ArtistApiController@get');
    Route::get('request', 'RequestApiController@index');
    Route::get('history', 'HistoryApiController@index');
    Route::get('history/last', 'HistoryApiController@last');
});
