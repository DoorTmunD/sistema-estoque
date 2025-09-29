<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- LOGO + SLOGAN -->
    <div class="flex flex-col items-center mb-4 select-none">
        {{-- se preferir, troque para <x-logo-estocore class="h-12 w-12 mb-2 drop-shadow" /> --}}
        <x-application-logo class="h-12 w-12 mb-2 drop-shadow" />
        <span class="text-sm md:text-base text-gray-500 dark:text-cyan-100 tracking-wide font-medium uppercase">
            Centralize. Controle. Evolua.
        </span>
    </div>

    <!-- Status (ex.: link de redefinição enviado) -->
    @if (session('status'))
        <div class="mb-3 flex items-center justify-center gap-2 animate-fadeInUp text-green-600">
            <x-auth-session-status :status="session('status')" />
            <!-- ícone ok -->
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                <path d="M8 12l2.6 2.6L16 9" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    @endif

    <form
        x-data="{
            loading: false,
            showPassword: false,
            success: false,
            error: false,
            shake: false,
            hasErrorMsg: @js(session('errors') ? true : false)
        }"
        @submit.prevent="
            loading = true;
            success = false;
            error = false;
            $wire.login()
                .then(() => { success = true })
                .catch(() => {
                    error = true;
                    shake = true;
                    setTimeout(() => shake = false, 700)
                })
                .finally(() => { loading = false });
        "
        :class="(shake || hasErrorMsg) ? 'animate-shake shadow-lg ring-2 ring-red-400 shadow-red-200' : ''"
        autocomplete="on"
        class="transition-all duration-300 space-y-2 w-full"
    >
        <!-- Erro global -->
        <template x-if="error || hasErrorMsg">
            <div class="flex items-center gap-2 mb-2 px-3 py-2 bg-red-50 border border-red-300 rounded shadow animate-fadeInUp text-red-700">
                <!-- ícone erro -->
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M15 9l-6 6M9 9l6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                </svg>
                <span class="text-sm font-semibold">As credenciais não conferem. Tente novamente!</span>
            </div>
        </template>

        <!-- EMAIL -->
        <div class="mb-1">
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
            <div class="relative mt-1">
                <input
                    wire:model.defer="form.email"
                    id="email"
                    class="block w-full rounded-lg border border-gray-300 dark:bg-gray-800 dark:text-gray-100 focus:border-cyan-500 focus:ring-cyan-500 pr-12 py-2 px-3 transition shadow-sm"
                    type="email"
                    name="email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Seu e-mail"
                />
                {{-- Avatar (opcional). Removido md5 para evitar dependência extra.
                <template x-if="$el.querySelector('input').value">
                    <img
                        :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent($el.querySelector('input').value) + '&background=0D8ABC&color=fff&size=32&bold=true'"
                        class="absolute top-1/2 right-2 -translate-y-1/2 w-8 h-8 rounded-full border border-gray-200 shadow opacity-70 transition"
                        alt="avatar"
                    >
                </template>
                --}}
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
        </div>

        <!-- SENHA -->
        <div class="relative mt-2">
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Senha</label>
            <input
                :type="showPassword ? 'text' : 'password'"
                wire:model.defer="form.password"
                id="password"
                name="password"
                class="block w-full rounded-lg border border-gray-300 dark:bg-gray-800 dark:text-gray-100 focus:border-cyan-500 focus:ring-cyan-500 pr-10 py-2 px-3 transition shadow-sm"
                required
                autocomplete="current-password"
                placeholder="Sua senha"
            />
            <button type="button"
                @click="showPassword = !showPassword"
                class="absolute top-8 right-3 text-gray-400 hover:text-cyan-600 transition"
                tabindex="-1"
                aria-label="Mostrar ou ocultar senha"
            >
                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7A10.056 10.056 0 015.5 6.423m4.162-1.27A10.05 10.05 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.056 10.056 0 01-1.473 2.523M15 12a3 3 0 11-6 0 3 3 0 016 0zm-6.875 6.825L20.485 4.515" /></svg>
            </button>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-2">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-cyan-600 shadow-sm focus:ring-cyan-500" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">Lembrar-me</span>
            </label>
        </div>

        <!-- Loader / feedback (SEM LOTTIE) -->
        <div class="flex justify-center mb-2 h-10 items-center">
            <!-- carregando -->
            <template x-if="loading">
                <svg class="w-6 h-6 animate-spin text-cyan-600" viewBox="0 0 24 24" fill="none" aria-label="Carregando">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25" stroke-width="4"/>
                    <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                </svg>
            </template>

            <!-- ok (nenhum erro) -->
            <template x-if="!loading && !(error || hasErrorMsg)">
                <svg class="w-6 h-6 text-cyan-600 animate-pulse" viewBox="0 0 24 24" fill="none" aria-label="Pronto">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6" />
                    <path d="M8 12l2.6 2.6L16 9" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </template>

            <!-- erro -->
            <template x-if="error || hasErrorMsg">
                <svg class="w-6 h-6 text-rose-600" viewBox="0 0 24 24" fill="none" aria-label="Erro">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/>
                    <path d="M15 9l-6 6M9 9l6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                </svg>
            </template>
        </div>

        <!-- Ações -->
        <div class="flex flex-col md:flex-row items-center justify-between mt-4 gap-2">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-500 hover:text-cyan-600 transition rounded focus:outline-none" href="{{ route('password.request') }}" wire:navigate>
                    Esqueceu a senha?
                </a>
            @endif

            <button
                type="submit"
                x-bind:disabled="loading"
                :class="{
                    'bg-green-500 hover:bg-green-600': success,
                    'bg-red-600 hover:bg-red-700': error || hasErrorMsg,
                    'bg-cyan-600 hover:bg-cyan-700': !(success || error || hasErrorMsg)
                }"
                class="relative inline-flex items-center justify-center px-6 py-2 border border-transparent text-base font-semibold text-white rounded-xl transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 shadow-lg w-full md:w-auto"
            >
                <template x-if="!loading && !success && !(error || hasErrorMsg)">
                    <span>Entrar</span>
                </template>
                <template x-if="loading">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                </template>
                <template x-if="success">
                    <span class="flex items-center gap-1">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg> Sucesso
                    </span>
                </template>
                <template x-if="error || hasErrorMsg">
                    <span class="flex items-center gap-1">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg> Erro
                    </span>
                </template>
            </button>
        </div>
    </form>
</div>
