@props(['type' => 'neutre'])

<span @class([
    'inline-flex items-center rounded-full px-2.5 py-1 text-theme-xs font-medium',
    'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' => $type === 'succes',
    'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400' => $type === 'attente',
    'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400' => $type === 'erreur',
    'bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300' => $type === 'marque',
    'bg-gray-100 text-gray-600 dark:bg-white/5 dark:text-gray-300' => $type === 'neutre',
])>{{ $slot }}</span>
