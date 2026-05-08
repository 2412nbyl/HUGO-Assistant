<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory, \App\Traits\GeneratesCustomIds;

    protected $primaryKey = 'id_klien';
    public $incrementing = false;
    protected $keyType = 'string';
    const ID_PREFIX = 'K';

    protected $fillable = [
        'id_klien', 'name', 'phone', 'birth_date', 'address', 'notes'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function cases()
    {
        return $this->hasMany(NotarisCase::class, 'id_klien', 'id_klien');
    }
}
