@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-8 flex items-center gap-3 select-none">
        <i class="fa-solid fa-envelope-circle-check text-cyan-400"></i>
        Configurações de E-mail (SMTP)
    </h1>

    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700 font-bold shadow">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.settings.email.update') }}" method="POST" class="max-w-xl mx-auto bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-2xl border-l-4 border-cyan-400 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1">Mailer</label>
                <select name="mail_mailer" class="input input-bordered w-full bg-cyan-50 dark:bg-cyan-950 text-gray-800 dark:text-gray-100" required>
                    <option value="smtp" {{ ($settings['mail_mailer'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                    <option value="sendmail" {{ ($settings['mail_mailer'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Host SMTP</label>
                <input type="text" name="mail_host" class="input input-bordered w-full bg-cyan-50 dark:bg-cyan-950" value="{{ $settings['mail_host'] ?? '' }}" placeholder="smtp.seudominio.com" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Porta</label>
                <input type="number" name="mail_port" class="input input-bordered w-full bg-cyan-50 dark:bg-cyan-950" value="{{ $settings['mail_port'] ?? 587 }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Usuário SMTP</label>
                <input type="text" name="mail_username" class="input input-bordered w-full bg-cyan-50 dark:bg-cyan-950" value="{{ $settings['mail_username'] ?? '' }}" placeholder="usuario@seudominio.com" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Senha SMTP</label>
                <input type="password" name="mail_password" class="input input-bordered w-full bg-cyan-50 dark:bg-cyan-950" value="{{ $settings['mail_password'] ?? '' }}" autocomplete="new-password" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Encriptação</label>
                <select name="mail_encryption" class="input input-bordered w-full bg-cyan-50 dark:bg-cyan-950" required>
                    <option value="tls" {{ ($settings['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="" {{ empty($settings['mail_encryption']) ? 'selected' : '' }}>Nenhuma</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">E-mail Remetente</label>
                <input type="email" name="mail_from_address" class="input input-bordered w-full bg-cyan-50 dark:bg-cyan-950" value="{{ $settings['mail_from_address'] ?? '' }}" placeholder="notificacoes@seudominio.com" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nome Remetente</label>
                <input type="text" name="mail_from_name" class="input input-bordered w-full bg-cyan-50 dark:bg-cyan-950" value="{{ $settings['mail_from_name'] ?? config('app.name') }}" placeholder="EstoCORE" required>
            </div>
        </div>
        <div class="flex gap-4 mt-8">
            <button class="px-6 py-2 rounded bg-cyan-600 text-white font-bold shadow hover:bg-cyan-700 transition" type="submit">
                Salvar Configuração
            </button>
            <a href="{{ route('admin.settings') }}" class="px-6 py-2 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                Cancelar
            </a>
        </div>
    </form>
@endsection