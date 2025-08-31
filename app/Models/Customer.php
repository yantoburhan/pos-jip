<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'no_hp_cust';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_hp_cust',
        'cust_name',
        'cust_point',
        'level_id',
        'total_spent',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
    
    /**
     * Menghitung ulang statistik customer berdasarkan semua transaksinya.
     */
    public function recalculateStats(): void
    {
        // 1. Hitung total belanja & poin dari semua transaksi dalam satu kueri
        $stats = Transaction::where('no_hp_cust', $this->no_hp_cust)
                            ->selectRaw('SUM(total_penjualan) as total_spent, SUM(total_poin) as total_points')
                            ->first();

        $totalSpent = $stats->total_spent ?? 0;
        $totalPoin = $stats->total_points ?? 0;

        // 2. Cari level tertinggi yang memenuhi syarat poin.
        // Logika ini sederhana karena kita tahu level untuk 0 poin ("N/A") pasti ada.
        $applicableLevel = Level::where('level_point', '<=', $totalPoin)
                     ->orderBy('level_point', 'desc')
                     ->first();
        
        // 3. Update data customer dengan statistik yang baru
        if ($applicableLevel) {
             $this->update([
                'total_spent' => $totalSpent,
                'cust_point'  => $totalPoin,
                'level_id'    => $applicableLevel->id,
            ]);
        } else {
            // Kasus darurat ini hanya akan terjadi jika tabel 'levels' Anda benar-benar kosong,
            // yang seharusnya tidak mungkin terjadi karena ada seeder.
            Log::error('Could not find any level in the database for customer ' . $this->no_hp_cust . '. Please run the LevelSeeder.');
        }
    }
}