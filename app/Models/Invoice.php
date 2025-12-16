<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'payment_date',
        'company_id',
        'company_name',
        'company_address',
        'company_bank',
        'client_id',
        'client_name',
        'client_address',
        'client_bank',
        'total_without_pvn',
        'total_pvn',
        'total_sum',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

}
