@php
    $action = isset($user)
        ? route('users.update', $user)
        : route('users.store');
    $method = isset($user) ? 'PUT' : 'POST';
@endphp

<form action="{{ $action }}" method="POST" class="space-y-4">
    @csrf
    @method($method)

    <!-- Nome -->
    <div>
        <label class="block font-medium">Nome</label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $user->name ?? '') }}"
            class="block w-full rounded-md border-gray-300 focus:ring focus:ring-opacity-50"
        >
        @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <!-- Email -->
    <div>
        <label class="block font-medium">Email</label>
        <input
            type="email"
            name="email"
            value="{{ old('email', $user->email ?? '') }}"
            class="block w-full rounded-md border-gray-300 focus:ring focus:ring-opacity-50"
        >
        @error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <!-- Senha -->
    <div>
        <label class="block font-medium">
            {{ isset($user) ? 'Nova Senha (opcional)' : 'Senha' }}
        </label>
        <input
            type="password"
            name="password"
            class="block w-full rounded-md border-gray-300 focus:ring focus:ring-opacity-50"
        >
        @error('password')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <!-- Confirmar Senha -->
    <div>
        <label class="block font-medium">Confirmar Senha</label>
        <input
            type="password"
            name="password_confirmation"
            class="block w-full rounded-md border-gray-300 focus:ring focus:ring-opacity-50"
        >
    </div>

    <!-- Nível -->
    <div>
        <label class="block font-medium">Nível</label>
        <select
            name="nivel"
            class="block w-full rounded-md border-gray-300 focus:ring focus:ring-opacity-50"
        >
            @foreach(['super-admin'=>'Super Admin','adm'=>'Administrador','common'=>'Comum'] as $key => $label)
                <option
                    value="{{ $key }}"
                    @selected(old('nivel', $user->nivel ?? '') === $key)
                >
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('nivel')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <!-- Botão -->
    <div class="mt-6">
        <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
            {{ isset($user) ? 'Atualizar' : 'Cadastrar' }}
        </button>
    </div>
</form>