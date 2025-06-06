<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            {
            Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->integer('quantity');
                $table->timestamps();
            });

                Schema::table('sales', function (Blueprint $table) {
                $table->integer('quantity');
    });
        }

            $table->id(); // bigint(20) の ID
            $table->unsignedBigInteger('product_id');
            $table->timestamps(); // created_at と updated_at の timestamp

            // 外部キー制約を追加
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
        Schema::table('sales', function (Blueprint $table) {
        $table->dropColumn('quantity');
    });
    }

    
}

