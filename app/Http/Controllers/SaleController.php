<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
// use App\Http\Controllers\Api\SaleController;

class SaleController extends Controller
{

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $product = Product::lockForUpdate()->find($request->product_id);
            
            

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => '商品が見つかりませんでした。'
                ], 404);
            }

            if ($product->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => '在庫が不足しています（現在の在庫: ' . $product->stock . '）'
                ], 400);
            }
            

            // 売上テーブルに記録
            Sale::create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);

            // 在庫を減算
            $product->decrement('stock', $request->quantity);

            return response()->json([
                'success' => true,
                'message' => '購入が完了しました',
                'remaining_stock' => $product->stock
            ]);
        });
    }

}

