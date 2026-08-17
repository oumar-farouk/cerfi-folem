@props(['type' => 'info', 'titre' => null])

<div role="{{ in_array($type, ['error', 'warning']) ? 'alert' : 'status' }}"
     @class([
        'flex gap-3 rounded-xl border p-4',
        'border-success-200 bg-success-50 text-success-800 dark:border-success-500/30 dark:bg-success-500/10 dark:text-success-300' => $type === 'success',
        'border-error-200 bg-error-50 text-error-800 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-300' => $type === 'error',
        'border-warning-200 bg-warning-50 text-warning-800 dark:border-warning-500/30 dark:bg-warning-500/10 dark:text-warning-300' => $type === 'warning',
        'border-brand-200 bg-brand-50 text-brand-800 dark:border-brand-500/30 dark:bg-brand-500/10 dark:text-brand-200' => $type === 'info',
     ])>
    <div class="min-w-0 text-theme-sm">
        @if ($titre)
            <p class="font-semibold">{{ $titre }}</p>
        @endif
        {{ $slot }}
    </div>
</div>
