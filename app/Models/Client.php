<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'business_name',
        'registration_num',
        'tax_num',
        'address',
        'bank_account',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function contacts()
    {
        return $this->hasMany(ClientContact::class);
    }
}
