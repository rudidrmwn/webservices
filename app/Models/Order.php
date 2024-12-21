<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function userOrder()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
