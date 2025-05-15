{{-- resources/views/suppliers/_form.blade.php --}}
@php
  $action = isset($supplier)
      ? route('suppliers.update', $supplier)
      : route('suppliers.store');
  $method = isset($supplier) ? 'PUT' : 'POST';
@endphp

<form action="{{ $action }}" method="POST" class="space-y-4">
  @csrf
  @if($method==='PUT') @method('PUT') @endif

  <div>
    <label class="block font-medium">Nome</label>
    <input type="text"
           name="name"
           value="{{ old('name',$supplier->name??'') }}"
           class="w-full border rounded px-3 py-2"
           required>
    @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block font-medium">E-mail</label>
    <input type="email"
           name="email"
           value="{{ old('email',$supplier->email??'') }}"
           class="w-full border rounded px-3 py-2">
    @error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block font-medium">Telefone</label>
    <input type="text"
           id="phone"
           name="phone"
           value="{{ old('phone',$supplier->phone??'') }}"
           class="w-full border rounded px-3 py-2">
    @error('phone')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block font-medium">Endereço</label>
    <textarea name="address"
              class="w-full border rounded px-3 py-2">{{ old('address',$supplier->address??'') }}</textarea>
    @error('address')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  <button type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
    {{ isset($supplier) ? 'Atualizar' : 'Cadastrar' }}
  </button>
</form>