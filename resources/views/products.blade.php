@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">商品情報一覧</h1>

    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">商品新規登録</a>
    <form action="{{ route('products') }}" method="GET">
        <div class="m-5">
        <label>商品名:</label>
        <input type="text" name="keyword" value="{{ request('search') }}" id="search_box">
        </div>
        
        <div>
        <label class="">メーカー名:</label>
        <select class="form-select"  name="company_id" alt="Bootstrap" width="100" height="100" id="company_id">
            <option value=""></option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
            @endforeach
        </select>
        </div>

        <!-- 検索フォームのセクション -->
<div class="search mt-5">
    
    <!-- 検索のタイトル -->
    <h2>検索条件で絞り込み</h2>
    
    <!-- 検索フォーム。GETメソッドで、商品一覧のルートにデータを送信 -->
    <form action="{{ route('products') }}" method="GET" class="row g-3">

        <!-- 最小価格の入力欄 -->
        <div class="col-sm-12 col-md-2">
            <input type="number" name="min_price" class="form-control" placeholder="最小価格" value="{{ request('min_price') }}" id="min_price">
        </div>

        <!-- 最大価格の入力欄 -->
        <div class="col-sm-12 col-md-2">
            <input type="number" name="max_price" class="form-control" placeholder="最大価格" value="{{ request('max_price') }}" id="max_price">
        </div>

        <!-- 最小在庫数の入力欄 -->
        <div class="col-sm-12 col-md-2">
            <input type="number" name="min_stock" class="form-control" placeholder="最小在庫" value="{{ request('min_stock') }}" id="min_stock">
        </div>

        <!-- 最大在庫数の入力欄 -->
        <div class="col-sm-12 col-md-2">
            <input type="number" name="max_stock" class="form-control" placeholder="最大在庫" value="{{ request('max_stock') }}" id="max_stock">
        </div>

        <!-- 絞り込みボタン -->
        <div class="col-sm-12 col-md-1">
            <button id="search-btn" type="button" class="btn btn-outline-secondary" type="submit">絞り込み</button>
        </div>
    </form>
</div>

<!-- 検索条件をリセットするためのリンクボタン -->
    <a href="{{ route('products') }}" class="btn btn-success mt-3">検索条件を元に戻す</a>

    <div id="loading" style="display: none;">検索中...
        
    </div>


    <div class="products mt-5">
        <h2>商品情報</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>商品ID</th>
                    <th>商品名</th>
                    <th>メーカー名</th>
                    <th>価格
                        <!-- <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'direction' => 'asc']) }}">↑</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'direction' => 'desc']) }}">↓</a> -->
                        <a href="#" class="sort-link" data-sort="price" data-direction="asc">↑</a>
                        <a href="#" class="sort-link" data-sort="price" data-direction="desc">↓</a>
                    </th>
                        
                    <th>在庫数
                        <!-- <a href="{{ request()->fullUrlWithQuery(['sort' => 'stock', 'direction' => 'asc']) }}">↑</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'stock', 'direction' => 'desc']) }}">↓</a> -->
                        <a href="#" class="sort-link" data-sort="stock" data-direction="asc">↑</a>
                        <a href="#" class="sort-link" data-sort="stock" data-direction="desc">↓</a>
                    </th>
                    <th>コメント</th>
                    <th>商品画像</th>
                </tr>
            </thead>
            
            <tbody id="result-tbody">
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

    

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    $(document).ready(function() {
    $("#search-btn").click(function() {
        let keyword = $("#search_box").val();  // 入力されたキーワード取得
        let company_id = $("#company_id").val();
        let min_price = $("#min_price").val();
        let max_price = $("#max_price").val();
        let min_stock = $("#min_stock").val();
        let max_stock = $("#max_stock").val();
        $("#loading").show();  // 読み込み中表示

        $.ajax({
            url: "search",
            type: "GET",
            data: { 
                    keyword: keyword,
                    company_id: company_id,
                    min_price: min_price,
                    max_price: max_price,
                    min_stock: min_stock,
                    max_stock: max_stock,
                    sort: sort,
                    direction: direction
                    },
            dataType: "json",
            success: function(response) {
                $("#loading").hide();  // 読み込み完了で非表示
                let tableBody = $("#result-tbody");
                tableBody.empty();  // 一度リセット

                if (response.length === 0) {
                    tableBody.append("<tr><td colspan='3'>該当商品なし</td></tr>");
                } else {
                    response.forEach(product => {
                        let image = `${product.img_path}`
                        let url = "{{asset ("")}}" +image
                        let row = `<tr>
                            <td>${product.id}</td>
                            <td>${product.product_name}</td>
                            <td>${product.company.company_name}</td>
                            <td>${product.price}</td>
                            <td>${product.stock}</td>
                            <td>${product.comment}</td>
                            <td><img src="${url}" alt="商品画像" width="100"></td>
                            <td><a href="/products/${product.id}" class="btn btn-info btn-sm mx-1">詳細表示</a>
                                <button type="button" class="btn btn-danger btn-sm mx-1 delete-btn" data-product_id="${product.id}">削除</button>
                            </td>
                        </tr>`;
                        tableBody.append(row);
                    });
                }
            },
            error: function() {
                $("#loading").hide();
                alert("検索に失敗しました。");
            }
        });
    });
});
        $(function() {
            $(document).on('click', '.btn-danger', function(e) {
                e.preventDefault();

                if (confirm('削除してよろしいですか？')) {
                    const productId = $(this).data('product_id');
                    const baseUrl = "{{ url('') }}"; // Bladeから取得

                    $.ajax({
                        type: 'POST',
                        url: `${baseUrl}/destroy/${productId}`,
                        dataType: 'json',
                        data: {
                            _method: 'DELETE',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            alert('削除が完了しました');
                            location.reload();
                        },
                        error: function() {
                            alert('削除に失敗しました');
                        }
                    });
                }
            });
        });

        $(document).ready(function() {
            $(".delete-btn").click(function() {
                let productId = $(this).data("id"); // クリックされた商品のIDを取得
                let row = $("#row-" + productId); // 削除する行を取得

                if (!confirm("本当に削除しますか？")) {
                    return;
            }

            $.ajax({
                url: "/products/" + productId,
                type: "POST",
                data: {
                    _method: "DELETE",
                    _token: "{{ csrf_token() }}" // CSRFトークンを送信
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        row.remove(); // 成功時に行を削除
                    } else {
                        alert("削除に失敗しました。");
                    }
                },
                error: function() {
                    alert("削除に失敗しました。");
                }
            });
        });
    });
    </script>
    @endsection

