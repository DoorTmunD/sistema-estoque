<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // registrar bindings, singletons, etc.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Adiciona IP, User-Agent, cidade, país e hostname em todos os logs do Spatie Activitylog
        Activity::creating(function ($activity) {
            $properties = is_array($activity->properties)
                ? $activity->properties
                : (is_object($activity->properties)
                    ? (array) $activity->properties
                    : (json_decode($activity->properties, true) ?? [])
                );

            if (app()->runningInConsole()) {
                $properties['ip'] = null;
                $properties['user_agent'] = 'console';
                $properties['city'] = null;
                $properties['country'] = null;
                $properties['hostname'] = null;
            } else {
                $ip = Request::ip();
                $properties['ip'] = $ip;
                $properties['user_agent'] = Request::header('User-Agent');
                // GeoIP retorna cidade e país (depende do pacote torann/geoip)
                try {
                    $geo = geoip($ip);
                    $properties['city'] = $geo->city ?? null;
                    $properties['country'] = $geo->country ?? null;
                } catch (\Exception $e) {
                    $properties['city'] = null;
                    $properties['country'] = null;
                }
                // Hostname pode ser demorado se IP externo, mas não trava se timeout
                try {
                    $properties['hostname'] = gethostbyaddr($ip) ?: null;
                } catch (\Exception $e) {
                    $properties['hostname'] = null;
                }
            }

            $activity->properties = $properties;
        });
    }
}