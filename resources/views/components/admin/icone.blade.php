@props(['nom' => 'grille', 'classe' => 'size-5'])

@php
    /*
    | Jeu d'icônes en trait, aligné sur le style TailAdmin. Les tracés sont
    | inlinés pour éviter une dépendance supplémentaire côté JS.
    */
    $traces = [
        'grille' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'liste' => '<path d="M8 6h13M8 12h13M8 18h13" stroke-linecap="round"/><circle cx="3.5" cy="6" r="1"/><circle cx="3.5" cy="12" r="1"/><circle cx="3.5" cy="18" r="1"/>',
        'calendrier' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4" stroke-linecap="round"/>',
        'horloge' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round" stroke-linejoin="round"/>',
        'utilisateurs' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0" stroke-linecap="round"/><path d="M16 5.2a3.5 3.5 0 0 1 0 5.6M18 20a6.4 6.4 0 0 0-2-4.7" stroke-linecap="round"/>',
        'poignee' => '<path d="m11 17 2 2a1.4 1.4 0 0 0 2-2l-3-3" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 11 8 6l3 2 4-3 6 5-4 5-3-2-3 3-4-1Z" stroke-linecap="round" stroke-linejoin="round"/>',
        'image' => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="m4 17 5-4 4 3 3-2 4 4" stroke-linecap="round" stroke-linejoin="round"/>',
        'monnaie' => '<circle cx="12" cy="12" r="9"/><path d="M15 9.5A3 3 0 0 0 9 11c0 2.5 6 1 6 3.5A3 3 0 0 1 9 15M12 6.5v11" stroke-linecap="round"/>',
        'carte' => '<path d="m9 4-6 2.5v14L9 18l6 2.5 6-2.5v-14L15 6.5 9 4Z" stroke-linejoin="round"/><path d="M9 4v14M15 6.5v14" stroke-linecap="round"/>',
        'badge' => '<circle cx="12" cy="9" r="4"/><path d="M6 21a6 6 0 0 1 12 0" stroke-linecap="round"/>',
        'plus' => '<path d="M12 5v14M5 12h14" stroke-linecap="round"/>',
        'crayon' => '<path d="M4 20h4l10-10-4-4L4 16v4Z" stroke-linejoin="round"/><path d="m14.5 5.5 4 4" stroke-linecap="round"/>',
        'corbeille' => '<path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13" stroke-linecap="round" stroke-linejoin="round"/>',
        'telecharger' => '<path d="M12 4v11m0 0 4-4m-4 4-4-4M4 19h16" stroke-linecap="round" stroke-linejoin="round"/>',
        'recherche' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5" stroke-linecap="round"/>',
        'fleche-gauche' => '<path d="M19 12H5m0 0 6-6m-6 6 6 6" stroke-linecap="round" stroke-linejoin="round"/>',
        'controle' => '<path d="M4 6h16M4 12h10M4 18h6" stroke-linecap="round"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => $classe]) }} viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="1.7" aria-hidden="true" focusable="false">
    {!! $traces[$nom] ?? $traces['grille'] !!}
</svg>
