<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory, \App\Traits\GeneratesCustomIds;

    protected $primaryKey = 'id_arsip';
    public $incrementing = false;
    protected $keyType = 'string';
    const ID_PREFIX = 'AR';

    protected $fillable = [
        'id_arsip', 'id_dok', 'id_akta', 'id_kasus', 'id_klien',
        'client_name', 'folder_location'
    ];

    public function case()
    {
        return $this->belongsTo(NotarisCase::class, 'id_kasus', 'id_kasus');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_klien', 'id_klien');
    }

    /**
     * All case documents linked to this archive folder.
     * Documents are linked via id_arsip on case_documents table.
     */
    public function documents()
    {
        return $this->hasMany(CaseDocument::class, 'id_arsip', 'id_arsip')->latest();
    }
}
