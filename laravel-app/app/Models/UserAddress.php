<?php

namespace App\Models;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAddress extends Model
{
    use HasFactory;

    protected $table = 'user_addresses';
    protected $guarded = [];

    public function province(){
        return $this->belongsTo(Province::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }
}
