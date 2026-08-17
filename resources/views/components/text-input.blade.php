@props(['disabled' => false])

<input @disabled($disabled)
       {{ $attributes->merge(['class' => 'w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs transition placeholder:text-gray-400 focus:border-brand-400 focus:outline-none focus:ring-3 focus:ring-brand-500/15 disabled:opacity-60 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90']) }}>
