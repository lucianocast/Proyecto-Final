<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware aliases.
     *
     * @var array
     */
    protected $middlewareAliases = [
        // Registrar alias 'role' apuntando al middleware EnsureRole
        'role' => \App\Http\Middleware\EnsureRole::class,
    ];
}
