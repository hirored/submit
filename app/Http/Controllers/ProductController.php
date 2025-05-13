<?php

// まずは必要なモジュールを読み込んでいます。今回はProductとCompanyの情報と、リクエストの情報が必要です。
namespace App\Http\Controllers;

use App\Models\Product; // Productモデルを現在のファイルで使用できるようにするための宣言です。
use App\Models\Company; // Companyモデルを現在のファイルで使用できるようにするための宣言です。
use App\Http\Requests\ProductsRequest; // Requestクラスという機能を使えるように宣言します
// Requestクラスはブラウザに表示させるフォームから送信されたデータをコントローラのメソッドで引数として受け取ることができます。
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller //コントローラークラスを継承します（コントローラーの機能が使えるようになります）
{
    
    public function index(Request $request)
{
    $query = Product::with('company'); // company情報も取得

    // 商品名での部分一致検索
    if ($request->product_name) {
        $query->where('product_name', 'LIKE', "%{$request->product_name}%");
    }

    // 会社IDで絞り込み
    if ($request->company_id) {
        $query->where('company_id', '=', $request->company_id);
    }

    // ▼ 並び替えの処理を追加
    $sort = $request->input('sort');       // 例: price, stock
    $direction = $request->input('direction'); // 例: asc, desc

    if ($sort && in_array($sort, ['price', 'stock']) &&
        $direction && in_array($direction, ['asc', 'desc'])) {
        $query->orderBy($sort, $direction);
    } else {
        $query->orderBy('id', 'asc'); // デフォルト
    }

    $products = $query->get();
    $companies = Company::all();

    return view('products', compact('products', 'companies'));
}


    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        Log::info($request);
        $company_id = $request->input('company_id');
        $min_price = $request->input('min_price');
        $max_price = $request->input('max_price');
        $min_stock = $request->input('min_stock');
        $max_stock = $request->input('max_stock');
        $query = Product::query();

        if ($keyword) {
            $query->where('product_name', 'LIKE', "%{$keyword}%");
        }

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        if ($min_price) {
            $query->where('price', '>', $min_price);
        }

        if ($max_price) {
            $query->where('price', '<', $max_price);
        }

        if ($min_stock) {
            $query->where('stock', '>', $min_stock);
        }

        if ($max_stock) {
            $query->where('stock', '<', $max_stock);
        }

        $products = $query->with('company')->get();
        

        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }

        return response()->json($products);
    }

    public function destroy($id){
        $product = Product::find($id);
    
        if (!$product) {
            return response()->json(['success' => false, 'message' => '商品が見つかりませんでした。']);
        }
    
        // 関連する sales データを削除
        \DB::table('sales')->where('product_id', $id)->delete();
    
        $product->delete();
    
        return response()->json(['success' => true, 'message' => '商品を削除しました。']);
    }


    public function create(){
        // 商品作成画面で会社の情報が必要なので、全ての会社の情報を取得します。
        $companies = Company::all();

        // 商品作成画面を表示します。その際に、先ほど取得した全ての会社情報を画面に渡します。
        return view('products_create', compact('companies'));
    }

    // 送られたデータをデータベースに保存するメソッドです
    public function store(ProductsRequest $request){
    // トランザクション開始前に到達するか確認
            // トランザクション開始
            DB::beginTransaction();

        try {
            // 新しく商品を作ります。そのための情報はリクエストから取得します。
        $product = new Product([
            'product_name' => $request->get('product_name'),
            'company_id' => $request->get('company_id'),
            'price' => $request->get('price'),
            'stock' => $request->get('stock'),
            'comment' => $request->get('comment'),
        ]);
        
        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }
        // $request->hasFile('img_path')は、ブラウザにアップロードされたファイルが存在しているかを確認
        // getClientOriginalName()はアップロードしたファイル名を取得するメソッドです。
        // 作成したデータベースに新しいレコードとして保存します。
        $product->save();
        //new Product([]) によって新しい「Product」（レコード）を作成しています。
        //new を使うことで新しいインスタンスを作成することができます
            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            return back();
        }
        
        return redirect('products');
    } 
    



    public function show(Product $product)
    //(Product $product) 指定されたIDで商品をデータベースから自動的に検索し、その結果を $product に割り当てます。
    {
        // 商品詳細画面を表示します。その際に、商品の詳細情報を画面に渡します。
        return view('products_show', ['product' => $product]);
    //　ビューへproductという変数が使えるように値を渡している
    // ['product' => $product]でビューでproductを使えるようにしている
    // compact('products')と行うことは同じであるためどちらでも良い
    }

    public function edit(Product $product){
        

        // 商品編集画面で会社の情報が必要なので、全ての会社の情報を取得します。
        $companies = Company::all();

        // 商品編集画面を表示します。その際に、商品の情報と会社の情報を画面に渡します。
        return view('products_edit', compact('product', 'companies'));
    }

    public function update(ProductsRequest $request, Product $product){
        // リクエストされた情報を確認して、必要な情報が全て揃っているかチェックします。
        //バリデーションによりフォームに未入力項目があればエラーメッセー発生させる（未入力です　など）

        // 商品の情報を更新します。
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
        
        // 画像処理はここに

        // 更新した商品を保存します。
        $product->save();
        // モデルインスタンスである$productに対して行われた変更をデータベースに保存するためのメソッド（機能）です。
        if ($product->save()) {
            return redirect()->route('products')->with('success', 'Products updated successfully');
        } else {
            
        }
        // 全ての処理が終わったら、商品一覧画面に戻ります。
        return redirect()->route('products')
            ->with('success', 'Products updated successfully');
        // ビュー画面にメッセージを代入した変数(success)を送ります
    }
    
    public function find(ProductsRequest $request){
    // 商品名の検索キーワードがある場合、そのキーワードを含む商品をクエリに追加
    $query = Product::query();
    
    if($search = $request->search){
        $query->where('product_name', 'LIKE', "%{$search}%");
    }

    // 上記の条件(クエリ）に基づいて商品を取得し、10件ごとのページネーションを適用
    $products = $query->paginate(10);

    // 商品一覧ビューを表示し、取得した商品情報をビューに渡す
    return view('products', ['products' => $products]);

    }

    


}
