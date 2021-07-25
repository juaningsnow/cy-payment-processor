<?php

namespace App\Models;

use App\Utils\HasCompanyFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    use HasCompanyFilter;

    protected $guarded = ['id'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
