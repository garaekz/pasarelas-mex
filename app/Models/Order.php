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

    protected $appends = [
        'payment_method_formatted',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getCreatedAtAttribute($value)
    {
        return date('d/m/Y H:i:s a', strtotime($value));
    }

    public function getPaymentMethodFormattedAttribute()
    {
        $value = $this->payment_method;
        if ($value == 'bank_account') {
            return 'Transferencia bancaria';
        }

        if ($value == 'card') {
            return 'Tarjeta de crédito';
        }

        if ($value == 'oxxo') {
            return 'Pago en OXXO';
        }

        return $value;
    }

    public function getStatusAttribute($value)
    {
        if ($value == 'pending') {
            return 'Pendiente';
        }

        if ($value == 'paid') {
            return 'Pagado';
        }

        if ($value == 'expired') {
            return 'Expirado';
        }

        if ($value == 'cancelled') {
            return 'Cancelado';
        }

        if ($value == 'refunded') {
            return 'Reembolsado';
        }

        return $value;
    }

    public function getPdfUrlAttribute()
    {
        if ($this->payment_method == 'bank_account') {
            return env('OPENPAY_BASE_SPEI_URL') . env('OPENPAY_ID') . '/' . $this->payment_id;
        }

        return null;
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
