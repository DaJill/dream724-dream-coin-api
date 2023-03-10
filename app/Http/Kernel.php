<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
        \Barryvdh\Cors\HandleCors::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'                    => \App\Http\Middleware\Authenticate::class,
        'auth.basic'              => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'                => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers'           => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'                     => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'                   => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed'                  => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'                => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'                => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'validator.common'        => \App\Http\Middleware\CustomValidatorMiddleware::class,
        'api.mem'                 => \App\Http\Middleware\MemApiMiddleware::class,
        'api.admin'               => \App\Http\Middleware\AdminApiMiddleware::class,
        'api.mem_xin'             => \App\Http\Middleware\MemXinApiMiddleware::class,
        'set.header'              => \App\Http\Middleware\SetHeaderMiddleware::class,
        'set.jwt'                 => \App\Http\Middleware\SetJwt::class,
        'validator.product'       => \App\Http\Middleware\CustomValidatorMiddleware::class,
        'validator.product_order' => \App\Http\Middleware\CustomValidatorMiddleware::class,
        'validator.deposit'       => \App\Http\Middleware\CustomValidatorMiddleware::class,
        'cors'                    => \Barryvdh\Cors\HandleCors::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
