<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaEmas extends Model
{
    use HasFactory;

    protected $table = 'tb_harga_emas';

    // Tambahkan primary key jika berbeda dari 'id'
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'tanggal',
        'harga_usd',
        'harga_idr',
        'perubahan_persen'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'harga_usd' => 'decimal:2',
        'harga_idr' => 'decimal:2',
        'perubahan_persen' => 'decimal:2'
    ];

    // Scope untuk latest, tapi perbaiki dengan limit
    public function scopeLatestData($query)
    {
        return $query->orderBy('tanggal', 'desc')->limit(1);
    }

    // Scope untuk date range
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }
}
