{{-- resources/views/suppliers/_form.blade.php --}}
@php
  $action = isset($supplier)
      ? route('suppliers.update', $supplier)
      : route('suppliers.store');
  $method = isset($supplier) ? 'PUT' : 'POST';
@endphp

<form id="supplier-form" action="{{ $action }}" method="POST" class="space-y-6">
  @csrf
  @if($method==='PUT') @method('PUT') @endif

  <div>
    <label class="block font-bold mb-1 text-gray-700 dark:text-gray-300">Nome <span class="text-red-400">*</span></label>
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-green-400"><i class="fa-solid fa-user"></i></span>
        <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}"
               class="pl-10 pr-4 py-2 w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none shadow-sm transition-all"
               required>
    </div>
    @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block font-bold mb-1 text-gray-700 dark:text-gray-300">E-mail</label>
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-400"><i class="fa-solid fa-envelope"></i></span>
        <input type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}"
               class="pl-10 pr-4 py-2 w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none shadow-sm transition-all">
    </div>
    @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block font-bold mb-1 text-gray-700 dark:text-gray-300">Telefone</label>
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-purple-400"><i class="fa-solid fa-phone"></i></span>
        <input type="text" id="phone" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}"
               class="pl-10 pr-4 py-2 w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-purple-400 focus:outline-none shadow-sm transition-all">
    </div>
    @error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block font-bold mb-1 text-gray-700 dark:text-gray-300">Endereço</label>
    <textarea name="address"
      class="w-full min-h-[70px] max-h-40 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:outline-none shadow-sm transition-all"
      >{{ old('address', $supplier->address ?? '') }}</textarea>
    @error('address')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
  </div>
</form>