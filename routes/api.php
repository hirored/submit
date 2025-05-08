<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::middleware('api')->group(function () {
    Route::get('api/search-product', [ProductController::class, 'searchProduct']);
});
