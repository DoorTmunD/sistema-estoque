<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-2xl shadow p-6']) }}>
  <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ $title }}</h3>
  <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $value }}</p>
</div>