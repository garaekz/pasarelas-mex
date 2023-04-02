<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'user_id',
        'public_id',
        'payment_method',
        'payment_gateway',
        'payment_id',
        'status',
        'subtotal',
        'tax',
        'shipping',
        'total',
    ];

    // Format dates as I want
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // Format created at as DD/MM/YYYY HH:MM:SS
    public function getCreatedAtAttribute($value)
    {
        return date('d/m/Y H:i:s', strtotime($value));
    }

    public function uniqueIds()
    {
        return [
            'public_id',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'price');
    }
}
