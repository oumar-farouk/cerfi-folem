import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // Les polices et images restent proches de la racine du manifeste pour
        // simplifier le déploiement derrière un CDN ou un sous-répertoire.
        assetsInlineLimit: 2048,
    },
});
