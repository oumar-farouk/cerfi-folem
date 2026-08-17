@props(['action', 'message' => 'Confirmer la suppression ?', 'libelle' => 'Supprimer'])

{{-- Suppression avec confirmation en boîte de dialogue, sans dépendance JS lourde. --}}
<form method="POST" action="{{ $action }}" class="inline"
      x-data
      @submit.prevent="if (window.confirm(@js($message))) $el.submit()">
    @csrf
    @method('DELETE')
    <button type="submit"
            class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-theme-xs font-medium text-error-600 transition hover:bg-error-50 dark:hover:bg-error-500/10">
        <x-admin.icone nom="corbeille" classe="size-4" />
        {{ $libelle }}
    </button>
</form>
