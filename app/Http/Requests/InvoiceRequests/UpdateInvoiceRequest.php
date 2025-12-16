<?php

namespace App\Http\Requests\InvoiceRequests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_date' => 'required|date',
            'payment_date' => 'required|date',

            'company_name' => 'required|string',
            'company_address' => 'nullable|string',
            'company_bank' => 'nullable|string',

            'client_name' => 'required|string',
            'client_address' => 'nullable|string',
            'client_bank' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.pvn' => 'required|numeric|min:0',
        ];
    }
}
