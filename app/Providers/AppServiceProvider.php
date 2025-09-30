<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Ex.: binds/singletons aqui se precisar
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * ───────────────────────────────────────────────────────────────
         * 1) HTTPS forçado em produção/Render (evita mixed content)
         *    - Se a app estiver em 'production' e o proxy informar HTTPS
         *      via X-Forwarded-Proto, força https nas URLs geradas.
         *    - Ou se a env FORCE_HTTPS=true, força mesmo assim.
         * ───────────────────────────────────────────────────────────────
         */
        $isProduction = app()->environment('production');
        $forwardedProto = Request::header('X-Forwarded-Proto');
        $shouldForceHttps = env('FORCE_HTTPS', false) || ($isProduction && $forwardedProto === 'https');

        if ($shouldForceHttps) {
            URL::forceScheme('https');
        }

        /**
         * ───────────────────────────────────────────────────────────────
         * 2) Vite: fallback de manifest por env (opcional)
         *    - Se você definiu VITE_MANIFEST_PATH no Render, garantimos
         *      que o plugin use esse caminho.
         * ───────────────────────────────────────────────────────────────
         */
        if ($manifest = env('VITE_MANIFEST_PATH')) {
            // ex.: public/build/manifest.json ou public/build/.vite/manifest.json
            Config::set('vite.manifest', $manifest);
        }

        /**
         * ───────────────────────────────────────────────────────────────
         * 3) Enriquecimento dos logs do Spatie Activitylog
         *    - Mantém sua lógica original, com proteções extras.
         * ───────────────────────────────────────────────────────────────
         */
        Activity::creating(function (Activity $activity) {
            // Normaliza $properties em array
            $props = $activity->properties;

            if (is_string($props)) {
                $decoded = json_decode($props, true);
                $properties = is_array($decoded) ? $decoded : [];
            } elseif (is_object($props)) {
                $properties = (array) $props;
            } elseif (is_array($props)) {
                $properties = $props;
            } else {
                $properties = [];
            }

            if (app()->runningInConsole()) {
                $properties['ip']        = null;
                $properties['user_agent']= 'console';
                $properties['city']      = null;
                $properties['country']   = null;
                $properties['hostname']  = null;
            } else {
                $ip = Request::ip();
                $properties['ip']         = $ip;
                $properties['user_agent'] = Request::header('User-Agent');

                // GeoIP é opcional; se falhar, segue em frente
                try {
                    if (function_exists('geoip') && $ip) {
                        $geo = geoip($ip);
                        $properties['city']    = $geo->city    ?? null;
                        $properties['country'] = $geo->country ?? null;
                    } else {
                        $properties['city'] = $properties['country'] = null;
                    }
                } catch (\Throwable $e) {
                    $properties['city'] = $properties['country'] = null;
                }

                // Hostname (não bloqueia em caso de timeout/erro)
                try {
                    $properties['hostname'] = $ip ? (gethostbyaddr($ip) ?: null) : null;
                } catch (\Throwable $e) {
                    $properties['hostname'] = null;
                }
            }

            $activity->properties = $properties;
        });
    }
}
