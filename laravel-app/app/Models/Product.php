<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';
    protected $appends = ['is_sale'];
    protected $guarded = [];

    public function getIsSaleAttribute()
    {
        return $this->quantity > 0 && $this->sale_price !== 0 && $this->sale_price !== null && $this->date_on_sale_from < Carbon::now() && $this->date_on_sale_to > Carbon::now();
    }


    public function images(){
        return $this->hasMany(ProductImage::class);
    }

    public function scopeSearch($query, $search)
    {
        $query->where('name', 'LIKE', '%' . trim($search) . '%')->orWhere('description', 'LIKE', '%' . trim($search) . '%');
    }

    public function scopeFilter($query)
    {
        if (request()->has('category')) {
            $query->where('category_id', request()->category);
        }

        if (request()->has('sortBy')) {
            switch (request()->sortBy) {
                case 'max':
                    $query->orderBy('price', 'desc');
                    break;
                case 'min':
                    $query->orderBy('price');
                    break;
                case 'bestseller':
                    $query;
                    break;
                case 'sale':
                    $query->where('sale_price', '!=', 0)->where('date_on_sale_from', '<', Carbon::now())->where('date_on_sale_to', '>', Carbon::now());
                    break;
                default:
                    $query;
                    break;
            }
        }

        return $query;
    }
}
