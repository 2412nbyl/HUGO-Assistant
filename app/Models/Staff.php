<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory, \App\Traits\GeneratesCustomIds;

    protected $table = 'staffs';
    protected $primaryKey = 'id_staff';
    public $incrementing = false;
    protected $keyType = 'string';
    const ID_PREFIX = 'S';

    protected $fillable = [
        'id_staff', 'id_user', 'name', 'position', 'work_status',
        'phone', 'email', 'address', 'birth_date', 'notes'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
