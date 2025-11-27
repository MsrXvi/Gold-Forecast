<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediksi extends Model
{
    use HasFactory;

    protected $table = 'tb_prediksi';

    protected $fillable = [
        'tanggal_prediksi',
        'harga_prediksi_usd',
        'harga_prediksi_idr',
        'lower_bound',
        'upper_bound',
        'periode_tahun'
    ];

    protected $casts = [
        'tanggal_prediksi' => 'date',
        'harga_prediksi_usd' => 'decimal:2',
        'harga_prediksi_idr' => 'decimal:2',
        'lower_bound' => 'decimal:2',
        'upper_bound' => 'decimal:2'
    ];

    public function scopeByPeriode($query, $tahun)
    {
        return $query->where('periode_tahun', $tahun);
    }

    public function scopeOrderByDate($query)
    {
        return $query->orderBy('tanggal_prediksi', 'asc');
    }
}

