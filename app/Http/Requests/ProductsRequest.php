<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\ProductsRequest;
use Illuminate\Support\Facades\DB;

class ProductsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules():array
{   
    return [
        'product_name' => ' required |present | max:255',
        'price' => 'required | integer',
        'stock' => 'required | integer',
        'company_id' => 'required | string | max:255',
        'comment' => 'nullable | string | max:10000',
        'img_path' => 'nullable',
    ];
}

public function attributes()
{
    return [
        'product_name' => '商品名',
        'price' => '金額' ,'価格',
        'stock' => '在庫数' ,
        'company_id' => 'メーカー' ,
        'comment' => 'コメント',
        'img_path' => '画像',
    ];
}

/**
 * エラーメッセージ
 *
 * @return array
 */
public function messages() {
    return [
        'product_name.required' => ':attributeは必須項目です。',
        'price.required' => ':attributeは必須項目です。',
        'price.integer' => ':attributeは数字で入力してください。',
        'stock.required' => ':attributeは必須項目です。',
        'stock.integer' => ':attributeは数字入力してください。',
        'company_id.required' => ':attributeは:max字以内で入力してください。',
        
    ];
}
/**
    //  * @param Validator $validator
    //  */
    // protected function failedValidation(Validator $validator)
    // {
    // }

    // /**
    //  * @return  Validator  $validator
    //  */
    // public function getValidator()
    // {
    //     return $this->getValidatorInstance();
    // }
public function purchase(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
    ]);

    $product = Product::lockForUpdate()->find($id);

    if (!$product) {
        return response()->json([
            'success' => false,
            'message' => '指定された商品が存在しません。'
        ], 404);
    }

    if ($product->stock < $request->quantity) {
        return response()->json([
            'success' => false,
            'message' => '在庫が不足しています（現在の在庫: ' . $product->stock . '）'
        ], 400);
    }

    Sale::create([
        'quantity' => $request->quantity,
    ]);

    $product->decrement('stock', $request->quantity);

    return response()->json([
        'success' => true,
        'message' => '購入が完了しました',
        'remaining_stock' => $product->stock
    ]);
}

}

