<?php

// まずは必要なモジュールを読み込んでいます。今回はProductとCompanyの情報と、リクエストの情報が必要です。
namespace App\Http\Controllers;

use App\Models\Product; // Productモデルを現在のファイルで使用できるようにするための宣言です。
use App\Models\Company; // Companyモデルを現在のファイルで使用できるようにするための宣言です。
use App\Http\Requests\ProductsRequest; // Requestクラスという機能を使えるように宣言します
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller //コントローラークラスを継承します（コントローラーの機能が使えるようになります）
{
    
    public function index(Request $request){
        
        $query = Product::query();
        if($request->product_name){
            $query->where('product_name', 'LIKE', "%{$request->product_name}%");
        }
        
        if($request->company_id){
            $query->where('company_id', '=',$request->company_id);
        }

        
        
        $products = $query->orderBy('id', 'asc')->get();
        $companies = Company::all();
        // 全ての商品情報を取得しています。これが商品一覧画面で使われます。
        
    

        // 商品一覧画面を表示します。その際に、先ほど取得した全ての商品情報を画面に渡します。
        return view('products', compact('products','companies'));
        
    }

    
    


    public function create(){
        // 商品作成画面で会社の情報が必要なので、全ての会社の情報を取得します。
        $companies = Company::all();

        // 商品作成画面を表示します。その際に、先ほど取得した全ての会社情報を画面に渡します。
        return view('products_create', compact('companies'));
    }

    
    public function store(ProductsRequest $request) {
        DB::beginTransaction();
        
        try {
            // 画像処理
            if($request->hasFile('img_path')) { 
                $filename = $request->img_path->getClientOriginalName();
                
                $filePath = $request->img_path->storeAs('products', $filename, 'public');
                $request->merge(['img' => '/storage/' . $filePath]);
            }
    
            // 商品登録
            $product = new Product;
            $product->registProduct($request);
    
            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withErrors(['error' => '登録に失敗しました。']);
        }
        
        return redirect('products');
    }
    



    public function show(Product $product){
        // 商品詳細画面を表示します。その際に、商品の詳細情報を画面に渡します。
        return view('products_show', ['product' => $product]);
    //　ビューへproductという変数が使えるように値を渡している
    }

    public function edit(Product $product){
        

        // 商品編集画面で会社の情報が必要なので、全ての会社の情報を取得します。
        $companies = Company::all();

        // 商品編集画面を表示します。その際に、商品の情報と会社の情報を画面に渡します。
        return view('products_edit', compact('product', 'companies'));
    }

    public function update(ProductsRequest $request, Product $product){

        // リクエストされた情報を確認して、必要な情報が全て揃っているかチェックします。
        $product->product_name = $request->product_name;
        //productモデルのproduct_nameをフォームから送られたproduct_nameの値に書き換える
        $product->company_id = $request->company_id;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->comment = $request->comment;
        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }

        
        $product->save();
        // モデルインスタンスである$productに対して行われた変更をデータベースに保存するためのメソッド（機能）です。
        
        if ($product->save()) {
            return redirect()->route('products.index')->with('success', 'Products updated successfully');
        } else {
            // 全ての処理が終わったら、商品一覧画面に戻ります。
        return redirect()->route('products.index')
        ->with('success', 'Products updated successfully');
    // ビュー画面にメッセージを代入した変数(success)を送ります
        }
        
    }
    
    

    public function destroy($id){
        \DB::beginTransaction();
        try {

        // 関連する sales のデータを削除
        \DB::table('sales')->where('product_id', $id)->delete();

        // products のデータを削除
        \DB::table('products')->where('id', $id)->delete();

        // トランザクションのコミット
        \DB::commit();

        return redirect()->route('products.index')->with('success', '商品を削除しました。');
    } catch (\Exception $e) {
        // トランザクションのロールバック
        \DB::rollBack();

        // エラーメッセージをログに記録する (任意)
        \Log::error('商品削除エラー: ' . $e->getMessage());

        // エラーメッセージと共にリダイレクト
        return redirect()->route('products.index')->with('error', '商品削除中にエラーが発生しました。');
    }
}

}

