<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'item',
        'description',
        'quantity',
        'price',
        'pvn',
        'item_sum',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
