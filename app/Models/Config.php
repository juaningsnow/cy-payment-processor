<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_counter',
        'client_id',
        'client_secret',
        'access_token',
        'refresh_token',
        'xero_tenant_id',
        'redirect_url'
    ];
}