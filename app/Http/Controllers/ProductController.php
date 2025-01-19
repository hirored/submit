<?php

// まずは必要なモジュールを読み込んでいます。今回はProductとCompanyの情報と、リクエストの情報が必要です。
namespace App\Http\Controllers;

use App\Models\Product; // Productモデルを現在のファイルで使用できるようにするための宣言です。
use App\Models\Company; // Companyモデルを現在のファイルで使用できるようにするための宣言です。
use App\Http\Requests\ProductsRequest; // Requestクラスという機能を使えるように宣言します
// Requestクラスはブラウザに表示させるフォームから送信されたデータをコントローラのメソッドで引数として受け取ることができます。
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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
        // productsディレクトリのindex.blade.phpを表示させます
        // compact('products')によって
        // $productsという変数の内容が、ビューファイル側で利用できるようになります。
        // ビューファイル内で$productsと書くことでその変数の中身にアクセスできます。
    }

    
    public function getList(Request $request){
    // selectbox データを取得
    $companies = companies::where('company_name')->get();

    $query = Products::query();

    // この行の後にクエリを逐次構築していきます。
    // そして、最終的にそのクエリを実行するためのメソッド（例：get(), first(), paginate() など）を呼び出すことで、データベースに対してクエリを実行します。
    // 商品名の検索キーワードがある場合、そのキーワードを含む商品をクエリに追加

    if($search = $request->search){
        $query->where('product_name', 'LIKE', "%{$search}%");

    // 検索機能
    $searchword = $request->input('searchword');
    $companyId = $request->input('company_id');

    $query = Products::query()
        // Company アソシエーションを取得
        ->with([
        'companies',
    ]);

    if (!empty($searchword)) {
        // メソッドチェーンを利用
        $query->where('company_id','LIKE',"%{$searchword}%")
            ->orWhere('product_name','LIKE',"%{$searchword}%");
            
    }

    if (!empty($companyId)) {
        // ここで ->get() は不要
        $query->where('company_id',$companyId);
    }

    // 全件取得 
    $products = $query->orderBy('id', 'desc')->get();

    return view('products', compact(
        'products',
        'companies',
        'searchword'
    ));
}
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
            return redirect()->route('products.index')->with('success', 'Products updated successfully');
        } else {
            
        }
        // 全ての処理が終わったら、商品一覧画面に戻ります。
        return redirect()->route('products.index')
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
    return view('products.index', ['products' => $products]);

    }

    public function destroy($id)
{
    // 関連する sales のデータを削除
    \DB::table('sales')->where('product_id', $id)->delete();

    // products のデータを削除
    \DB::table('products')->where('id', $id)->delete();

    return redirect()->route('products.index')->with('success', '商品を削除しました。');
}
}

