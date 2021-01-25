<?php

namespace App\Models;

class UserCartItem extends Model
{
    protected $fillable = ['qty'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }
}
