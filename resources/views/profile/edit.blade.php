{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800">
        Meu Perfil
    </h2>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto space-y-6 py-6">
        {{-- 1) Atualizar informações --}}
        <livewire:profile.update-profile-information-form />

        {{-- 2) Mudar senha --}}
        <livewire:profile.update-password-form />

        {{-- 3) Apagar conta --}}
        <livewire:profile.delete-user-form />
    </div>
@endsection
