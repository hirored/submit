@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">商品情報一覧</h1>

    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">商品新規登録</a>
    <form action="{{ route('products.index') }}" method="GET">
        <div class="m-5">
        <label>商品名:</label>
        <input type="text" name="product_name" value="{{ request('search') }}">
        </div>
        
        <div>
        <label class="">メーカー名:</label>
        <select class="form-select" id="company_id" name="company_id" alt="Bootstrap" width="100" height="100">
            <option value=""></option>
        </div>
        
            @foreach($companies as $company)
                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
            @endforeach
        </select>

        
        <button type="submit" class="m-5">検索</button>
    </form>


    <div class="products mt-5">
        <h2>商品情報</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>商品ID</th>
                    <th>商品名</th>
                    <th>メーカー名</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>コメント</th>
                    <th>商品画像</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($products as $product)
            <tr>
              <td>{{ $product->id }}</td>
              <td>{{ $product->product_name }}</td>
              <td>{{ $product->company->company_name }}</td>
              <td>{{ $product->price }}</td>
              <td>{{ $product->stock }}</td> 
              <td>{{ $product->comment }}</td>
              <td><img src="{{ asset($product->img_path) }}" alt="商品画像" width="100"></td>
              <td>
                  <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm mx-1">詳細表示</a>  
                      <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm mx-1">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    
</div>
@endsection

