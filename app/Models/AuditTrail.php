<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $fillable = [
        'user_id', 'table_name', 'record_id',
        'action', 'old_value', 'new_value',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convenience method to log any action.
     */
    public static function log(string $table, string $recordId, string $action, $old = null, $new = null): void
    {
        static::create([
            'user_id'    => auth()->id(),
            'table_name' => $table,
            'record_id'  => $recordId,
            'action'     => $action,
            'old_value'  => $old,
            'new_value'  => $new,
        ]);
    }
}
