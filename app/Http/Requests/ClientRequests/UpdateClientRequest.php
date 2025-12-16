<?php

namespace App\Http\Requests\ClientRequests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_name' => 'required|string|max:255',
            'registration_num' => 'nullable|string|max:255',
            'tax_num' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',

            'contacts' => 'nullable|array',
            'contacts.*.type' => 'required|in:email,phone',
            'contacts.*.value' => 'required|string|max:255',
            'contacts.*.comment' => 'nullable|string|max:255',
        ];
    }
}
