<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProductsRequest;
use Illuminate\Database\Eloquent\Model;


class ProductsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {   
        return [
            'product_name' => 'required|max:255',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'company_id' => 'required|integer',
            'comment' => 'nullable|string|max:10000',
            'img_path' => 'nullable|file|image|max:2048',
        ];
    }

    /**
     * 属性名のカスタマイズ
     */
    public function attributes()
    {
        return [
            'product_name' => '商品名',
            'price' => '価格',
            'stock' => '在庫数',
            'company_id' => 'メーカー',
            'comment' => 'コメント',
            'img_path' => '商品画像',
        ];
    }

    /**
     * エラーメッセージのカスタマイズ
     */
    public function messages() {
        return [
            'product_name.required' => ':attributeは必須項目です。',
            'price.required' => ':attributeは必須項目です。',
            'price.integer' => ':attributeは数字で入力してください。',
            'stock.required' => ':attributeは必須項目です。',
            'stock.integer' => ':attributeは数字で入力してください。',
            'company_id.required' => ':attributeは必須項目です。',
            'company_id.integer' => ':attributesは数字で入力してください。',
            'img_path.image' => ':attributeは画像形式でアップロードしてください。',
            'img_path.max' => ':attributeは2MB以下にしてください。',
        ];
    }
}


