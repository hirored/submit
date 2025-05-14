<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function up()
{
    Schema::create('sales', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->integer('quantity');
        $table->timestamps();
    });
}


}

