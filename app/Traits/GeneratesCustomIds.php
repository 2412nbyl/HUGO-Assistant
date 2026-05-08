<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesCustomIds
{
    protected static function bootGeneratesCustomIds()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = static::generateCustomId();
            }
        });
    }

    public static function generateCustomId()
    {
        $prefix = defined('static::ID_PREFIX') ? static::ID_PREFIX : '';
        
        // For Client, user requested K + Date + Random
        if ($prefix === 'K') {
            return 'K' . date('Ymd') . strtoupper(Str::random(4));
        }

        // Default: Prefix + Random alphanumeric
        return $prefix . strtoupper(Str::random(8));
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
