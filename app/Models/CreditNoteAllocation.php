<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNoteAllocation extends Model
{
    use HasFactory;

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}