<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class EmailSettingsController extends Controller
{
    public function edit()
    {
        $settings = Setting::whereIn('key', [
            'mail_mailer', 'mail_host', 'mail_port', 'mail_username',
            'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'
        ])->pluck('value', 'key')->toArray();

        return view('admin.settings.email', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'mail_mailer'      => 'required|string',
            'mail_host'        => 'required|string',
            'mail_port'        => 'required|integer',
            'mail_username'    => 'required|string',
            'mail_password'    => 'required|string',
            'mail_encryption'  => 'nullable|string',
            'mail_from_address'=> 'required|email',
            'mail_from_name'   => 'required|string',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Opcional: cache ou setar config em tempo real

        return redirect()->back()->with('success', 'Configurações de e-mail atualizadas com sucesso!');
    }
}