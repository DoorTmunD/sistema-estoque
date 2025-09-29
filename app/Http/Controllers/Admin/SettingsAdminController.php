<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SettingsAdminController extends Controller
{
public function email()
{
    // Busca as configs do banco (tabela settings)...
    $settings = collect(\DB::table('settings')->pluck('value', 'key'))->toArray();
    return view('admin.settings.email', compact('settings'));
}

public function emailUpdate(\Illuminate\Http\Request $request)
{
    $request->validate([
        'mail_mailer'         => 'required|string',
        'mail_host'           => 'required|string',
        'mail_port'           => 'required|integer',
        'mail_username'       => 'required|string',
        'mail_password'       => 'required|string',
        'mail_encryption'     => 'required|string',
        'mail_from_address'   => 'required|email',
        'mail_from_name'      => 'required|string',
    ]);

    $fields = $request->only([
        'mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password',
        'mail_encryption', 'mail_from_address', 'mail_from_name'
    ]);

    foreach ($fields as $key => $value) {
        \DB::table('settings')->updateOrInsert(['key' => $key], ['value' => $value]);
    }

    return redirect()->route('admin.settings.email')->with('success', 'Configurações de e-mail salvas com sucesso!');
}

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || $user->nivel !== 'super-admin') {
                abort(403, 'Acesso restrito: apenas super-admin pode acessar Configurações.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view('admin.settings.index');
    }

    public function notifications()
    {
        return view('admin.settings.notifications');
    }

    public function backup()
    {
        return view('admin.settings.backup');
    }

    public function permissions()
    {
        return view('admin.settings.permissions');
    }
}