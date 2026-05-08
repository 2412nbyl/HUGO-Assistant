<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     * GitHub webhook POSTs cannot include a CSRF token.
     */
    protected $except = [
        'webhook/github',
    ];
}
