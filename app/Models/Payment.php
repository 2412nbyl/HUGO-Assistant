<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, \App\Traits\GeneratesCustomIds;

    protected $primaryKey = 'id_transaksi';
    public $incrementing = false;
    protected $keyType = 'string';
    const ID_PREFIX = 'TR';

    protected $fillable = ['id_transaksi', 'id_kasus', 'amount', 'status'];

    public function case()
    {
        return $this->belongsTo(NotarisCase::class, 'id_kasus', 'id_kasus');
    }

    public function histories()
    {
        return $this->hasMany(PaymentHistory::class, 'payment_id', 'id_transaksi')->latest();
    }
}

