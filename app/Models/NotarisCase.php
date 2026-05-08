<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotarisCase extends Model
{
    use HasFactory, \App\Traits\GeneratesCustomIds;

    protected $table = 'cases';
    protected $primaryKey = 'id_kasus';
    public $incrementing = false;
    protected $keyType = 'string';
    const ID_PREFIX = 'CS';

    protected $fillable = [
        'id_kasus', 'client_name', 'phone', 'address', 'birth_date',
        'case_name', 'type', 'status', 'deadline', 'progress_note',
        'nominal_bayar',
        'file_ktp', 'file_npwp', 'file_kk',
        'file_surat_tanah', 'file_surat_perintah', 'file_buku_nikah',
        'created_by', 'id_klien', 'id_dok',
    ];


    protected $casts = [
        'deadline'   => 'date',
        'birth_date' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function payment()
    {
        return $this->hasOne(Payment::class, 'id_kasus', 'id_kasus');
    }

    public function documents()
    {
        return $this->hasMany(CaseDocument::class, 'id_kasus', 'id_kasus');
    }

    public function caseNotes()
    {
        return $this->hasMany(CaseNote::class, 'id_kasus', 'id_kasus');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }


    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class, 'record_id')
                    ->where('table_name', 'cases')
                    ->latest();
    }

    // ── Birthday helpers ───────────────────────────────────────────────────────

    /** Days until this client's next birthday (returns null if no birth_date) */
    public function daysUntilBirthday(): ?int
    {
        if (!$this->birth_date) return null;
        $today     = now()->startOfDay();
        $next      = $this->birth_date->copy()->year($today->year);
        if ($next->lt($today)) $next->addYear();
        return (int) $today->diffInDays($next);
    }

    public function isBirthdayToday(): bool
    {
        if (!$this->birth_date) return false;
        return $this->birth_date->format('m-d') === now()->format('m-d');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeByType($query, $type)    { return $query->where('type', $type); }
    public function scopeByStatus($query, $status) { return $query->where('status', $status); }
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
    }
}
