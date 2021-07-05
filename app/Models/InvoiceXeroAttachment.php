<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceXeroAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'url'
    ];
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
