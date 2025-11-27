<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akurasi extends Model
{
    use HasFactory;

    protected $table = 'tb_akurasi';

    protected $fillable = [
        'mape',
        'rmse',
        'mae',
        'r_squared',
        'data_points',
        'keterangan'
    ];

    protected $casts = [
        'mape' => 'decimal:4',
        'rmse' => 'decimal:4',
        'mae' => 'decimal:4',
        'r_squared' => 'decimal:6'
    ];

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
