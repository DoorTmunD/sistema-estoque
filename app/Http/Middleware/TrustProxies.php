<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * Se você usa múltiplos proxies (Render, Cloudflare etc.),
     * o asterisco diz ao Laravel que pode confiar em todos.
     */
    protected $proxies = '*';

    /**
     * Esses headers permitem que o framework descubra
     * o esquema (http/https), IP e host originais.
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}