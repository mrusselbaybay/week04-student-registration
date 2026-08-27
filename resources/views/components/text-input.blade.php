@props(['name', 'type' => 'text'])

<input
    type="{{ $type }}"
    name="{{ $name }}"
    id="{{ $name }}"
    value="{{ old($name) }}"
    {{ $attributes->merge(['class' => 'block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white ' . ($errors->has($name) ? 'border-red-400 dark:border-red-500' : 'border-gray-300 dark:border-gray-600')]) }}
>
