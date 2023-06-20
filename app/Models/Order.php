<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'comment',
        'file_url',
        'tel',
        'file_size',
        'file_name',
        'total_sum',
        'quantity',
    ];
}
