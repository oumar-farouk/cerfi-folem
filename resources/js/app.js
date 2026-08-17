import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);

window.Alpine = Alpine;

/*
|--------------------------------------------------------------------------
| Store « theme » — mode sombre du back-office
|--------------------------------------------------------------------------
| La préférence est mémorisée dans localStorage, avec repli sur la préférence
| système. Le script anti-flash se trouve dans le <head> du layout admin.
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
| Trois états distincts : déployée (desktop), réduite en rail d'icônes avec
| ouverture au survol, et tiroir superposé sur mobile.
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
| Utilisé dans le tableau de bord. Les données sont passées depuis Blade,
| jamais construites côté client, pour rester une simple couche d'affichage.
*/
Alpine.data('graphique', (options = {}) => ({
    instance: null,

    async init() {
        // Import dynamique : la bibliothèque (environ 500 Ko) n'est téléchargée
        // que sur les pages qui contiennent réellement un graphique, pas sur le
        // site public ni sur les écrans de saisie.
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
            // Repli pour les navigateurs sans API presse-papiers (contexte non sécurisé).
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

Alpine.start();
