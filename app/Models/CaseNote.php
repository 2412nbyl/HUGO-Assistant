<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseNote extends Model
{
    protected $table = 'case_notes';

    protected $fillable = [
        'id_kasus',
        'user_id',
        'status',
        'note',
    ];

    public function case()
    {
        return $this->belongsTo(NotarisCase::class, 'id_kasus', 'id_kasus');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
