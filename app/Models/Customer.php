<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory;

    /**
     * Tentukan Primary Key tabel.
     *
     * @var string
     */
    protected $primaryKey = 'no_hp_cust';

    /**
     * Tentukan apakah Primary Key auto-increment atau tidak.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tentukan tipe data Primary Key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Atribut yang boleh diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'no_hp_cust',
        'cust_name',
        'cust_point',
        'level_id',
        'total_spent',
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model Level.
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
}