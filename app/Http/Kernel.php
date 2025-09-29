<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Middleware global executado em todas as requisições.
     */
    protected $middleware = [
        // Confia no cabeçalho X-Forwarded-Proto/IP → HTTPS/Proxy
        \App\Http\Middleware\TrustProxies::class,

        // Bloqueia requests acima de post_max_size
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Converte strings vazias em null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,

        // Barra acesso se o app está em manutenção
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
    ];

    /**
     * Agrupamentos de middleware.
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Aliases aplicáveis diretamente nas rotas.
     */
    protected $middlewareAliases = [
        // Autenticação / sessão
        'auth'             => \App\Http\Middleware\Authenticate::class,
        'auth.basic'       => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session'     => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'guest'            => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'           => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Se precisar de autorização baseada em nível, crie middlewares próprios, tipo:
        // 'nivel' => \App\Http\Middleware\CheckNivel::class,
    ];
}