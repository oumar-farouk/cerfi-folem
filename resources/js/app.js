import './bootstrap';

import collapse from '@alpinejs/collapse';

// En Livewire v3, Alpine est déjà initialisé par Livewire.
// On intercepte l'événement d'initialisation d'Alpine fourni par Livewire :
document.addEventListener('alpine:init', () => {
    // On enregistre les plugins Alpine
    Alpine.plugin(collapse);

    /*
    |--------------------------------------------------------------------------
    | Store « theme » — mode sombre du back-office
    |--------------------------------------------------------------------------
    */
    Alpine.store('theme', {
        theme: 'light',

        init() {
            const enregistre = localStorage.getItem('theme');
            const systeme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            this.theme = enregistre || systeme;
            this.applique();
        },

        toggle() {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            localStorage.setItem('theme', this.theme);
            this.applique();
        },

        applique() {
            const html = document.documentElement;
            const body = document.body;

            if (this.theme === 'dark') {
                html.classList.add('dark');
                body.classList.add('dark');
            } else {
                html.classList.remove('dark');
                body.classList.remove('dark');
            }
        },
    });

    /*
    |--------------------------------------------------------------------------
    | Store « sidebar » — état de la barre latérale du back-office
    |--------------------------------------------------------------------------
    */
    Alpine.store('sidebar', {
        isExpanded: window.innerWidth >= 1280,
        isMobileOpen: false,
        isHovered: false,

        toggleExpanded() {
            this.isExpanded = !this.isExpanded;
            this.isMobileOpen = false;
        },

        toggleMobileOpen() {
            this.isMobileOpen = !this.isMobileOpen;
        },

        setMobileOpen(valeur) {
            this.isMobileOpen = valeur;
        },

        setHovered(valeur) {
            if (window.innerWidth >= 1280 && !this.isExpanded) {
                this.isHovered = valeur;
            }
        },
    });

    /*
    |--------------------------------------------------------------------------
    | Composant « graphique » — enveloppe ApexCharts
    |--------------------------------------------------------------------------
    */
    Alpine.data('graphique', (options = {}) => ({
        instance: null,

        async init() {
            const { default: ApexCharts } = await import('apexcharts');

            this.instance = new ApexCharts(this.$el, options);
            this.instance.render();

            this.$watch('$store.theme.theme', () => {
                this.instance.updateOptions({ theme: { mode: Alpine.store('theme').theme } });
            });
        },

        destroy() {
            this.instance?.destroy();
        },
    }));

    /*
    |--------------------------------------------------------------------------
    | Composant « copier » — bouton de copie du code d'inscription
    |--------------------------------------------------------------------------
    */
    Alpine.data('copier', (texte = '') => ({
        copie: false,

        async copierTexte() {
            try {
                await navigator.clipboard.writeText(texte);
            } catch (e) {
                const champ = document.createElement('textarea');
                champ.value = texte;
                champ.setAttribute('readonly', '');
                champ.style.position = 'absolute';
                champ.style.left = '-9999px';
                document.body.appendChild(champ);
                champ.select();
                document.execCommand('copy');
                document.body.removeChild(champ);
            }

            this.copie = true;
            setTimeout(() => (this.copie = false), 2000);
        },
    }));
});

// ⚠️ NE PAS METTRE import Alpine from 'alpinejs';
// ⚠️ NE PAS METTRE Alpine.start();
