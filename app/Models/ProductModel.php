<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductModel extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'category',
        'description',
        'date_and_time'
    ];

    public function scopeKeyword($query, $value)
    {
        return $query->where('name', 'like', $value)->orWhere('description', 'like', $value);
    }
}
