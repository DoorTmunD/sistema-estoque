<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        // Só deixa entrar se for admin ou super-admin
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || !in_array($user->nivel, ['super-admin', 'adm'])) {
                abort(403, 'Acesso restrito ao painel administrativo.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
