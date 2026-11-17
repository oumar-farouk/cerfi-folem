<x-public-layout>
<div class="folem-jy">
    <style>
        .folem-jy{
            --night:#0F3D2E; --night-2:#14503C; --ochre:#B8901E; --ochre-light:#E8C766;
            --clay:#F05A28; --cream:#FBF6EC; --ink:#1B2E22; --line:rgba(27,46,34,0.14);
            font-family:'Work Sans', sans-serif; color:var(--ink); background:var(--cream);
        }
        .folem-jy h1,.folem-jy .font-display{ font-family:'Amiri', serif; }
        .folem-jy .hero{
            background:linear-gradient(160deg, var(--night) 0%, var(--night-2) 60%, #1A6650 100%);
            color:var(--cream); padding:44px 24px; text-align:center;
        }
        .folem-jy .etape{ margin-bottom:28px; }
        .folem-jy .etape h2{ font-family:'Amiri', serif; font-size:22px; color:var(--night); margin-bottom:10px; }
        .folem-jy .champ{ display:block; margin-bottom:12px; }
        .folem-jy .champ span{ display:block; font-size:14px; margin-bottom:6px; color:#4a5a4f; }
        .folem-jy input[type=text]{
            width:100%; padding:12px 14px; background:#fff; border:1px solid var(--line); border-radius:8px; font-size:16px;
        }
        .folem-jy input[type=text]:focus-visible{ outline:3px solid var(--ochre-light); outline-offset:1px; }
        .folem-jy .btn{
            display:inline-block; border:none; border-radius:6px; padding:14px 20px; cursor:pointer;
            font-family:'Space Grotesk', sans-serif; font-weight:600; letter-spacing:.02em; text-align:center; font-size:15px;
        }
        .folem-jy .btn:focus-visible{ outline:3px solid var(--ochre-light); outline-offset:2px; }
        .folem-jy .btn-clay{ background:var(--clay); color:#fff; }
        .folem-jy .btn-night{ background:var(--night); color:#fff; }
        .folem-jy .btn-ligne{ background:#fff; color:var(--night); border:1.5px solid var(--night); }
        .folem-jy .btn[disabled]{ opacity:.55; cursor:wait; }
        .folem-jy .photo-input:focus-visible + label{ outline:3px solid var(--ochre-light); outline-offset:2px; }
        .folem-jy .apercu{ display:block; width:100%; height:auto; border-radius:14px; box-shadow:0 12px 40px rgba(15,61,46,.25); background:#E9EFEA; }
        .folem-jy .zone{ position:absolute; cursor:grab; touch-action:none; }
        .folem-jy .zone:active{ cursor:grabbing; }
        .folem-jy .erreur{ background:#FDECEA; border:1px solid #F3B7B0; color:#8A2A1F; border-radius:8px; padding:12px 14px; font-size:14px; margin-bottom:12px; }
        .folem-jy .info{ background:#FBF3E1; border:1px solid #EAD9A8; color:#8A6A1F; border-radius:8px; padding:12px 14px; font-size:13px; }
    </style>

    <div class="hero">
        <h1 class="text-3xl font-semibold">Créez votre affiche « J'y serai »</h1>
        <p class="text-[var(--ochre-light)] mt-1 font-medium">{{ $edition->nom }}</p>
        <p class="mt-2 text-sm opacity-90">Ajoutez votre photo et votre nom, puis partagez votre présence avec vos contacts.</p>
    </div>

    @if (! $config['cadre'])
        <div class="mx-auto max-w-xl px-6 py-16 text-center">
            <p class="font-display text-2xl text-[var(--night)]">L'affiche n'est pas encore disponible.</p>
            <p class="mt-2 text-gray-500">Revenez très bientôt pour créer la vôtre.</p>
        </div>
    @else
    <script>window.jyseraiConfig = @js($config);</script>

    <div class="mx-auto grid max-w-5xl gap-10 px-6 py-10 lg:grid-cols-2" x-data="jyserai(window.jyseraiConfig)">

        {{-- Étapes --}}
        <div>
            <div class="etape">
                <h2>1. Votre photo</h2>
                <input id="photo" type="file" accept="image/*" class="photo-input sr-only" @change="choisir($event)">
                <label for="photo" class="btn btn-night">
                    <span x-text="aPhoto ? 'Changer de photo' : 'Choisir une photo'"></span>
                </label>
                <p class="mt-2 text-sm text-gray-500">
                    Depuis un téléphone, vous pouvez aussi prendre un selfie directement.
                </p>

                <div x-show="aPhoto" x-cloak class="mt-4">
                    <label class="champ" for="zoom"><span>Zoom</span></label>
                    <input id="zoom" type="range" min="1" max="3" step="0.01" x-model.number="zoom" class="w-full">
                    <p class="mt-1 text-sm text-gray-500">Faites glisser la photo dans le cadre pour la recentrer.</p>
                </div>
            </div>

            <div class="etape">
                <h2>2. Vos informations</h2>
                <p x-show="prefilled" x-cloak class="mb-3 text-sm text-gray-500">
                    Ces informations viennent de votre inscription. Vous pouvez les modifier.
                </p>
                <label class="champ">
                    <span>Nom affiché sur l'affiche</span>
                    <input type="text" x-model="nom" maxlength="40" autocomplete="name" placeholder="Prénom Nom">
                </label>
                <label class="champ">
                    <span>Fonction · Structure (facultatif)</span>
                    <input type="text" x-model="role" maxlength="60" placeholder="Ex. : Directrice, Ministère…">
                </label>
            </div>

            <p class="info">
                Votre photo reste sur votre appareil : elle n'est ni envoyée ni enregistrée sur nos serveurs.
            </p>
        </div>

        {{-- Aperçu et actions --}}
        <div>
            <div class="relative mx-auto w-full lg:sticky lg:top-6" style="max-width:420px">
                <canvas x-ref="toile" class="apercu" width="1200" height="1200"
                        role="img" aria-label="Aperçu de votre affiche J'y serai"></canvas>
                <div x-show="aPhoto" x-cloak class="zone"
                     :style="`left:${zone.left}%;top:${zone.top}%;width:${zone.width}%;height:${zone.height}%`"
                     @pointerdown="debut($event)" @pointermove="deplace($event)"
                     @pointerup="fin()" @pointercancel="fin()"
                     @wheel.prevent="molette($event)"></div>

                <div class="mt-5 space-y-3">
                    <div x-show="erreur" x-cloak class="erreur" role="alert" x-text="erreur"></div>

                    <button type="button" class="btn btn-clay w-full" :disabled="occupe" @click="telecharger()">
                        Télécharger mon affiche
                    </button>
                    <button type="button" class="btn btn-ligne w-full" :disabled="occupe" @click="partager()">
                        Partager (WhatsApp)
                    </button>
                    <p class="text-center text-sm text-gray-500" x-show="message" x-cloak x-text="message" aria-live="polite"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function jyserai(config) {
            const SANS = "'Work Sans', system-ui, sans-serif";
            const TAILLE = 1200; // l'affiche est carrée

            /*
             * Positions exprimées en FRACTIONS de l'image du comité (0 à 1) : elles
             * restent valables quelle que soit la résolution du fichier fourni, tant
             * que la mise en page est la même. Mesurées sur le modèle 3e édition ;
             * à ajuster ici si le comité déplace la fenêtre photo.
             */
            const FENETRE = { x: 0.2810, y: 0.3106, w: 0.4384, h: 0.4255 }; // carré blanc de la photo
            const RAYON = 0.049;                                            // coins arrondis (haut-gauche, bas-droite)
            const BADGE = { x: 0.6082, y: 0.6020, w: 0.2329, h: 0.0864 };   // pastille « J'Y SERAI »
            const VERT = '#0A8A33', ORANGE = '#EA5A10';

            // Hors de l'objet réactif : Alpine encapsulerait ces éléments dans des proxys.
            let photo = null, gabarit = null, ouverte = false, prise = null, raf = null;

            // Rectangle aux quatre rayons indépendants (arcTo avec rayon 0 = coin droit).
            const rect = (c, x, y, w, h, tl, tr, br, bl) => {
                c.beginPath();
                c.moveTo(x + tl, y);
                c.lineTo(x + w - tr, y);       c.arcTo(x + w, y, x + w, y + tr, tr);
                c.lineTo(x + w, y + h - br);   c.arcTo(x + w, y + h, x + w - br, y + h, br);
                c.lineTo(x + bl, y + h);       c.arcTo(x, y + h, x, y + h - bl, bl);
                c.lineTo(x, y + tl);           c.arcTo(x, y, x + tl, y, tl);
                c.closePath();
            };

            // Écrit un texte : réduit la taille jusqu'à `min`, puis tronque avec « … ».
            const ecrire = (c, texte, x, y, o) => {
                if (!texte) return;
                let t = o.taille;
                c.textAlign = o.align || 'center';
                c.font = `${o.poids} ${t}px ${SANS}`;
                while (c.measureText(texte).width > o.max && t > (o.min || 16)) {
                    t -= 2;
                    c.font = `${o.poids} ${t}px ${SANS}`;
                }
                if (c.measureText(texte).width > o.max) {
                    let n = texte.length;
                    while (n > 1 && c.measureText(texte.slice(0, n) + '\u2026').width > o.max) n--;
                    texte = texte.slice(0, n) + '\u2026';
                }
                c.globalAlpha = o.alpha ?? 1;
                c.fillStyle = o.couleur;
                c.fillText(texte, x, y);
                c.globalAlpha = 1;
            };

            // Le modèle a-t-il une fenêtre TRANSPARENTE (photo dessinée dessous) ou un
            // carré blanc opaque (photo dessinée dessus) ? On le détecte au centre de la fenêtre.
            const fenetreTransparente = (img) => {
                try {
                    const sonde = document.createElement('canvas');
                    sonde.width = sonde.height = 200;
                    const sc = sonde.getContext('2d', { willReadFrequently: true });
                    sc.drawImage(img, 0, 0, 200, 200);
                    const cx = Math.round((FENETRE.x + FENETRE.w / 2) * 200);
                    const cy = Math.round((FENETRE.y + FENETRE.h / 2) * 200);
                    return sc.getImageData(cx, cy, 1, 1).data[3] < 40;
                } catch (e) {
                    return false;
                }
            };

            return {
                nom: '', role: '', zoom: 1, offX: 0, offY: 0, aPhoto: false,
                zone: { left: 0, top: 0, width: 0, height: 0 },
                erreur: '', message: '', occupe: false, prefilled: false,

                init() {
                    // Personne déjà inscrite : nom et fonction repris de son dossier (modifiables).
                    if (config.prefill) {
                        this.nom = config.prefill.nom || '';
                        this.role = config.prefill.role || '';
                        this.prefilled = true;
                    }

                    const img = new Image();
                    img.onload = () => { gabarit = img; ouverte = fenetreTransparente(img); this.dessiner(); };
                    img.onerror = () => { this.erreur = 'Le modèle d\u2019affiche est introuvable.'; };
                    img.src = config.cadre;

                    document.fonts.load('700 40px "Work Sans"').catch(() => {}).finally(() => this.dessiner());

                    ['nom', 'role', 'zoom'].forEach((k) => this.$watch(k, () => this.planifier()));
                    this.dessiner();
                },

                planifier() {
                    if (raf) return;
                    raf = requestAnimationFrame(() => { raf = null; this.dessiner(); });
                },

                dessiner() {
                    const toile = this.$refs.toile;
                    if (toile.width !== TAILLE) { toile.width = TAILLE; toile.height = TAILLE; }
                    const S = TAILLE;
                    const c = toile.getContext('2d');
                    c.clearRect(0, 0, S, S);
                    c.textBaseline = 'alphabetic';

                    const f = { x: FENETRE.x * S, y: FENETRE.y * S, w: FENETRE.w * S, h: FENETRE.h * S };
                    const r = RAYON * S;
                    this.zone = {
                        left: FENETRE.x * 100, top: FENETRE.y * 100,
                        width: FENETRE.w * 100, height: FENETRE.h * 100,
                    };

                    if (!gabarit) {
                        c.fillStyle = '#E9EFEA';
                        c.fillRect(0, 0, S, S);
                        ecrire(c, 'Chargement\u2026', S / 2, S / 2, { poids: 500, taille: 40, couleur: '#4a5a4f', max: S * 0.8 });
                        return;
                    }

                    // La photo, rognée aux coins de la fenêtre. Le liseré vert du modèle reste visible.
                    const dessinerPhoto = () => {
                        const m = 0.0016 * S;
                        c.save();
                        if (ouverte) rect(c, f.x - m, f.y - m, f.w + 2 * m, f.h + 2 * m, r, 0, r, 0);
                        else rect(c, f.x + m, f.y + m, f.w - 2 * m, f.h - 2 * m, r, 0, r, 0);
                        c.clip();
                        if (photo) {
                            const base = Math.max(f.w / photo.width, f.h / photo.height) * this.zoom;
                            const dw = photo.width * base, dh = photo.height * base;
                            const mx = (dw - f.w) / 2, my = (dh - f.h) / 2;
                            // La photo doit toujours couvrir entièrement la fenêtre.
                            this.offX = Math.max(-mx, Math.min(mx, this.offX));
                            this.offY = Math.max(-my, Math.min(my, this.offY));
                            c.drawImage(photo, f.x + (f.w - dw) / 2 + this.offX, f.y + (f.h - dh) / 2 + this.offY, dw, dh);
                        } else {
                            c.fillStyle = '#F3F6F4';
                            c.fillRect(f.x, f.y, f.w, f.h);
                            ecrire(c, 'Votre photo ici', f.x + f.w / 2, f.y + f.h / 2,
                                { poids: 600, taille: S * 0.034, couleur: '#6b7a70', max: f.w * 0.8 });
                        }
                        c.restore();
                    };

                    if (ouverte) {
                        dessinerPhoto();
                        c.drawImage(gabarit, 0, 0, S, S);
                    } else {
                        // Modèle à carré blanc opaque : la photo recouvre la fenêtre, y compris
                        // une partie de la pastille. On redessine donc la pastille par-dessus.
                        c.drawImage(gabarit, 0, 0, S, S);
                        dessinerPhoto();

                        const b = { x: BADGE.x * S, y: BADGE.y * S, w: BADGE.w * S, h: BADGE.h * S };
                        c.save();
                        c.shadowColor = 'rgba(0,0,0,0.3)';
                        c.shadowBlur = 10;
                        c.shadowOffsetY = 4;
                        rect(c, b.x, b.y, b.w, b.h, S * 0.0285, S * 0.008, S * 0.0285, S * 0.008);
                        c.fillStyle = ORANGE;
                        c.fill();
                        c.restore();
                        ecrire(c, "J'Y SERAI", b.x + b.w / 2, b.y + b.h / 2 + S * 0.0145,
                            { poids: 700, taille: S * 0.0414, min: 24, couleur: '#FFFFFF', max: b.w * 0.84 });
                    }

                    // Plaque du participant, en bas à gauche de la photo (la pastille occupe le bas à droite).
                    const nom = this.nom.trim(), role = this.role.trim();
                    const pw = f.w * 0.66, ph = S * (role ? 0.088 : 0.062), pad = S * 0.02;
                    const px = f.x, py = f.y + f.h - ph;
                    c.save();
                    c.shadowColor = 'rgba(0,0,0,0.25)';
                    c.shadowBlur = 14;
                    c.shadowOffsetY = 4;
                    c.fillStyle = VERT;
                    c.fillRect(px, py, pw, ph);
                    c.restore();
                    c.fillStyle = ORANGE;
                    c.fillRect(px, py, S * 0.008, ph);

                    ecrire(c, nom || 'Votre nom', px + pad, py + (role ? ph * 0.46 : ph * 0.68),
                        { align: 'left', poids: 700, taille: S * 0.04, min: S * 0.022, couleur: '#FFFFFF', alpha: nom ? 1 : 0.55, max: pw - 2 * pad });
                    if (role) {
                        ecrire(c, role, px + pad, py + ph * 0.84,
                            { align: 'left', poids: 500, taille: S * 0.024, min: S * 0.016, couleur: '#CFF0DC', max: pw - 2 * pad });
                    }
                },

                choisir(e) {
                    const fichier = e.target.files && e.target.files[0];
                    if (!fichier) return;
                    this.erreur = ''; this.message = '';

                    if (!fichier.type.startsWith('image/')) { this.erreur = 'Ce fichier n\u2019est pas une image.'; return; }
                    if (fichier.size > 15 * 1024 * 1024) { this.erreur = 'Cette photo est trop lourde (15 Mo maximum).'; return; }

                    const url = URL.createObjectURL(fichier);
                    const img = new Image();
                    img.onload = () => {
                        photo = img;
                        this.aPhoto = true;
                        this.zoom = 1; this.offX = 0; this.offY = 0;
                        this.dessiner();
                        URL.revokeObjectURL(url);
                    };
                    img.onerror = () => { this.erreur = 'Impossible de lire cette photo. Essayez un autre fichier.'; URL.revokeObjectURL(url); };
                    img.src = url;
                },

                // Déplacement de la photo dans la fenêtre
                debut(e) {
                    if (!photo) return;
                    prise = { x: e.clientX, y: e.clientY };
                    e.target.setPointerCapture(e.pointerId);
                },
                deplace(e) {
                    if (!prise) return;
                    const toile = this.$refs.toile;
                    const k = toile.width / toile.getBoundingClientRect().width;
                    this.offX += (e.clientX - prise.x) * k;
                    this.offY += (e.clientY - prise.y) * k;
                    prise = { x: e.clientX, y: e.clientY };
                    this.planifier();
                },
                fin() { prise = null; },
                molette(e) { this.zoom = Math.min(3, Math.max(1, this.zoom - e.deltaY * 0.002)); },

                // Export et partage
                verifier() {
                    if (!photo) { this.erreur = 'Ajoutez votre photo pour créer l\u2019affiche.'; return false; }
                    if (!this.nom.trim()) { this.erreur = 'Indiquez votre nom pour personnaliser l\u2019affiche.'; return false; }
                    this.erreur = '';
                    return true;
                },
                blob() { return new Promise((resolve) => this.$refs.toile.toBlob(resolve, 'image/png')); },
                nomFichier() {
                    const s = this.nom.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                    return `jyserai-folem-${s || 'affiche'}.png`;
                },

                async telecharger() {
                    if (!this.verifier()) return;
                    this.occupe = true;
                    try {
                        const b = await this.blob();
                        const a = document.createElement('a');
                        a.href = URL.createObjectURL(b);
                        a.download = this.nomFichier();
                        a.click();
                        setTimeout(() => URL.revokeObjectURL(a.href), 1000);
                        this.message = 'Affiche téléchargée.';
                    } finally { this.occupe = false; }
                },

                async partager() {
                    if (!this.verifier()) return;
                    this.occupe = true;
                    try {
                        const b = await this.blob();
                        const fichier = new File([b], this.nomFichier(), { type: 'image/png' });
                        if (navigator.canShare && navigator.canShare({ files: [fichier] })) {
                            await navigator.share({ files: [fichier], text: config.texte });
                        } else {
                            // Ordinateur : on télécharge l'image et on ouvre WhatsApp avec le texte.
                            await this.telecharger();
                            window.open('https://wa.me/?text=' + encodeURIComponent(config.texte), '_blank', 'noopener');
                        }
                    } catch (e) {
                        if (e.name !== 'AbortError') {
                            this.erreur = 'Le partage a échoué. Téléchargez l\u2019affiche puis partagez-la manuellement.';
                        }
                    } finally { this.occupe = false; }
                },
            };
        }
    </script>
    @endif
</div>
</x-public-layout>
