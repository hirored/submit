@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">商品新規登録</h1>

    <a href="{{ route('products') }}" class="btn btn-primary mt-1 mb-3">商品一覧に戻る</a>

    <form method="POST" action="{{ route('products_store') }}" enctype="multipart/form-data">

        @csrf
            <div class="mb-3">
            <label for="product_name" class="form-label">商品名:</label>
            <input id="product_name" type="text" name="product_name" class="form-control">
            @if($errors->has('product_name'))
                        <p>{{ $errors->first('product_name') }}</p>
                    @endif
        </div>

        <div class="mb-3">
            <label for="company_id" class="form-label">メーカー</label>
            <select class="form-select" id="company_id" name="company_id">
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">価格:</label>
            <input id="price" type="text" name="price" class="form-control" >
            @if($errors->has('price'))
                        <p>{{ $errors->first('price') }}</p>
                    @endif
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">在庫数:</label>
            <input id="stock" type="text" name="stock" class="form-control" >
            @if($errors->has('stock'))
                        <p>{{ $errors->first('stock') }}</p>
                    @endif
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">コメント:</label>
            <textarea id="comment" name="comment" class="form-control" rows="3"></textarea>
            @if($errors->has('comment'))
                        <p>{{ $errors->first('comment') }}</p>
                    @endif
        </div>

        <div class="mb-3">
            <label for="img_path" class="form-label">商品画像:</label>
            <input id="img_path" type="file" name="img_path" class="form-control">
            @if($errors->has('img_path'))
                        <p>{{ $errors->first('img_path') }}</p>
                    @endif
        </div>

        <button type="submit" class="btn btn-primary">登録</button>
    </form>


</div>
@endsection

