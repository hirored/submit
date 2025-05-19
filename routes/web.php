<?php

use Illuminate\Support\Facades\Route;
// "Route"というツールを使うために必要な部品を取り込んでいます。
use App\Http\Controllers\ProductController;
// ProductControllerに繋げるために取り込んでいます
use Illuminate\Support\Facades\Auth;
// "Auth"という部品を使うために取り込んでいます。この部品はユーザー認証（ログイン）に関する処理を行います
use App\Http\Controllers\ProductShowController;
// 詳細画面に作ったProductShowControllerを定義
use App\Http\Controllers\ProductEditController;
// 編集画面に作ったProductShowControllerを定義
use App\Http\Controllers\Api\SaleController;



Route::get('/', function () {
    // ウェブサイトのホームページ（'/'のURL）にアクセスした場合のルートです
    if (Auth::check()) {
        // ログイン状態ならば
        return redirect()->route('products.index');
        // 商品一覧ページ（ProductControllerのindexメソッドが処理）へリダイレクトします
    } else {
        // ログイン状態でなければ
        return redirect()->route('login');
        //　ログイン画面へリダイレクトします
    }
});
// もしCompanyControllerだった場合は
// companies.index のように、英語の正しい複数形になります。


Auth::routes();

// Auth::routes();はLaravelが提供している便利な機能で
// 一般的な認証に関するルーティングを自動的に定義してくれます
// この一行を書くだけで、ログインやログアウト
// パスワードのリセット、新規ユーザー登録などのための
// ルートが作成されます。
//　つまりログイン画面に用意されたビューのリンク先がこの1行で済みます

Route::group(['middleware' => 'auth'], function () {
    Route::resource('products', ProductController::class);
});



Auth::routes();

Route::get('/search', [App\Http\Controllers\ProductController::class, 'search'])->name('products.search');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/create', [ProductController::class, 'create'])->name('products.create');

Route::post('/products/store', [App\Http\Controllers\ProductController::class, 'store'])->name('products.store');

Route::POST('/products', [App\Http\Controllers\ProductController::class, 'store'])->name('products_store');


Route::get('/show/{id}', [ProductShowController::class, 'show'])->name('products_show');

Route::get('/show/{id}',[App\Http\Controllers\ProductController::class, 'show'])->name('products_show');

Route::POST('/update/{product}', [App\Http\Controllers\ProductController::class, 'update'])->name('products.update');


Route::get('/products', [ProductController::class, 'index'])->name('products');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::get('/search', [ProductController::class, 'search'])->name('search');

Route::post('/validate-product', [ProductController::class, 'validateProduct']);

Route::delete('/destroy/{id}', [ProductController::class, 'destroy'])->name('products.destroy');


// Route::get('/', 'HomeController@index')->name('crud.index'); /* 一覧表示 */

// Route::post('/destroy/{id}', 'HomeController@destroy'); /* レコード削除 */


