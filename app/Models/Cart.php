<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Cart.php
class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
