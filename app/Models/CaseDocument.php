<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseDocument extends Model
{
    use HasFactory, \App\Traits\GeneratesCustomIds;

    protected $primaryKey = 'id_dok';
    public $incrementing = false;
    protected $keyType = 'string';
    const ID_PREFIX = 'DO';

    protected $fillable = ['id_dok', 'id_kasus', 'id_klien', 'id_arsip', 'filename', 'filepath', 'uploaded_by'];

    public function case()
    {
        return $this->belongsTo(NotarisCase::class, 'id_kasus', 'id_kasus');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }

    public function archive()
    {
        return $this->belongsTo(\App\Models\Archive::class, 'id_arsip', 'id_arsip');
    }

}