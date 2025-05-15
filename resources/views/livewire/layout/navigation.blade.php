<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Desloga o usuário atual da aplicação.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <nav
        x-data="{ open: false, dark: localStorage.getItem('dark') === 'true' }"
        x-init="if (dark) document.documentElement.classList.add('dark')"
        class="bg-white border-b border-gray-100 dark:bg-gray-800 dark:border-gray-700"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo + Links -->
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" wire:navigate>
                            <x-application-logo class="h-9 w-auto fill-current text-gray-800 dark:text-white" />
                        </a>
                    </div>
                    <div class="hidden sm:-my-px sm:ms-10 sm:flex space-x-8">
                        <x-nav-link
                            :href="route('dashboard')"
                            :active="request()->routeIs('dashboard')"
                            wire:navigate
                            class="dark:text-gray-200 dark:hover:text-white"
                        >
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('products.index')"
                            :active="request()->routeIs('products.*')"
                            wire:navigate
                            class="dark:text-gray-200 dark:hover:text-white"
                        >
                            {{ __('Produtos') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('categories.index')"
                            :active="request()->routeIs('categories.*')"
                            wire:navigate
                            class="dark:text-gray-200 dark:hover:text-white"
                        >
                            {{ __('Categorias') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('suppliers.index')"
                            :active="request()->routeIs('suppliers.*')"
                            wire:navigate
                            class="dark:text-gray-200 dark:hover:text-white"
                        >
                            {{ __('Fornecedores') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('inventory.index')"
                            :active="request()->routeIs('inventory.*')"
                            wire:navigate
                            class="dark:text-gray-200 dark:hover:text-white"
                        >
                            {{ __('Estoque') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('movements.history')"
                            :active="request()->routeIs('movements.history')"
                            wire:navigate
                            class="dark:text-gray-200 dark:hover:text-white"
                        >
                            {{ __('Histórico') }}
                        </x-nav-link>

                        @can('manage-users')
                            <x-nav-link
                                :href="route('users.index')"
                                :active="request()->routeIs('users.*')"
                                wire:navigate
                                class="dark:text-gray-200 dark:hover:text-white"
                            >
                                {{ __('Usuários') }}
                            </x-nav-link>
                        @endcan
                    </div>
                </div>

                <!-- Dark toggle + Dropdown -->
                <div class="flex items-center">
                    <button
                        @click="
                            dark = !dark;
                            document.documentElement.classList.toggle('dark');
                            localStorage.setItem('dark', dark);
                        "
                        class="p-2 rounded focus:outline-none focus:ring focus:ring-indigo-500"
                    >
                        <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 3v2m0 14v2m9-9h-2m-14 0H3m16.07 4.93l-1.414-1.414M6.343 6.343L4.93
                                  4.929m12.728 0l-1.414 1.414M6.343 17.657l-1.414 1.414"/>
                        </svg>
                        <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
                        </svg>
                    </button>

                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium
                                           rounded-md text-gray-500 bg-white hover:text-gray-700 dark:bg-gray-800 dark:text-gray-200
                                           dark:hover:text-white focus:outline-none transition ease-in-out duration-150"
                                >
                                    <div
                                        x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                                        x-text="name"
                                        x-on:profile-updated.window="name = $event.detail.name"
                                        class="dark:text-gray-200"
                                    ></div>
                                    <svg class="fill-current h-4 w-4 ms-1 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414
                                                 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')" wire:navigate class="dark:text-gray-200">
                                    {{ __('Perfil') }}
                                </x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link
                                        href="#"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="dark:text-gray-200"
                                    >
                                        {{ __('Sair') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <div class="sm:hidden -me-2 flex items-center">
                        <button
                            @click="open = !open"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500
                                   hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition duration-150 ease-in-out"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{ 'hidden': open, 'inline-flex': !open }"
                                      class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6h16M4 12h16M4 18h16"/>
                                <path :class="{ 'hidden': !open, 'inline-flex': open }"
                                      class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Responsive Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate class="dark:text-gray-200">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" wire:navigate class="dark:text-gray-200">
                {{ __('Produtos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" wire=navigate class="dark:text-gray-200">
                {{ __('Categorias') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" wire=navigate class="dark:text-gray-200">
                {{ __('Fornecedores') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('inventory.index')" :active="request()->routeIs('inventory.*')" wire=navigate class="dark:text-gray-200">
                {{ __('Estoque') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link
                :href="route('movements.history')"
                :active="request()->routeIs('movements.history')"
                wire=navigate
                class="dark:text-gray-200"
            >
                {{ __('Histórico') }}
            </x-responsive-nav-link>
            @can('manage-users')
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" wire=navigate class="dark:text-gray-200">
                    {{ __('Usuários') }}
                </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Perfil e Logout -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700">
            <!-- ... (restante sem alterações) -->
        </div>
    </div>
</div>
