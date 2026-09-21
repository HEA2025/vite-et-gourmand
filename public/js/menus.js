const filtreTheme = document.getElementById('filtre-theme');
const filtreRegime = document.getElementById('filtre-regime');
const filtrePrixMin = document.getElementById('filtre-prix-min');
const filtrePrixMax = document.getElementById('filtre-prix-max');
const filtrePersonnes = document.getElementById('filtre-personnes');
const listeMenus = document.getElementById('liste-menus');

/*
 * Crée un élément HTML contenant uniquement du texte.
 * textContent évite d'interpréter les données reçues comme du HTML.
 */
function creerElement(balise, texte, classe = '') {
    const element = document.createElement(balise);

    if (classe) {
        element.className = classe;
    }

    if (texte !== undefined && texte !== null) {
        element.textContent = texte;
    }

    return element;
}

// Ajoute une information dans la liste descriptive d'un menu.
function ajouterInformation(liste, libelle, valeur) {
    const terme = creerElement('dt', libelle, 'col-5');
    const description = creerElement('dd', valeur, 'col-7');

    liste.append(terme, description);
}

// Construit une carte de menu à partir des données JSON reçues.
function creerCarteMenu(menu) {
    const colonne = creerElement('div', null, 'col-12 col-md-6');
    const article = creerElement('article', null, 'card h-100');
    const corps = creerElement('div', null, 'card-body d-flex flex-column');

    const titre = creerElement('h2', menu.titre, 'h3 card-title');
    const description = creerElement('p', menu.description, 'card-text');
    const informations = creerElement('dl', null, 'row mb-4');

    ajouterInformation(informations, 'Thème', menu.theme);
    ajouterInformation(informations, 'Régime', menu.regime);

    const prix = Number(menu.prix).toLocaleString('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    ajouterInformation(informations, 'À partir de', `${prix} €`);
    ajouterInformation(
        informations,
        'Minimum',
        `${menu.minimumPersonnes} personnes`
    );

    ajouterInformation(
        informations,
        'Stock',
        menu.stock > 0 ? String(menu.stock) : 'Indisponible'
    );

    const zoneBouton = creerElement('div', null, 'mt-auto');
    const lien = creerElement('a', 'Voir le menu', 'btn btn-primary');

    /*
     * L'identifiant vient de l'API de l'application.
     * Il est converti en nombre avant d'être utilisé dans l'URL.
     */
    lien.href = `/menus/${Number(menu.id)}`;

    zoneBouton.appendChild(lien);
    corps.append(titre, description, informations, zoneBouton);
    article.appendChild(corps);
    colonne.appendChild(article);

    return colonne;
}

// Affiche les menus sans injecter directement du HTML reçu depuis l'API.
function afficherMenus(menus) {
    listeMenus.replaceChildren();

    if (menus.length === 0) {
        const message = creerElement(
            'div',
            'Aucun menu ne correspond aux filtres.',
            'alert alert-info'
        );

        message.setAttribute('role', 'status');
        listeMenus.appendChild(message);

        return;
    }

    const ligne = creerElement('div', null, 'row g-4');

    menus.forEach(menu => {
        ligne.appendChild(creerCarteMenu(menu));
    });

    listeMenus.appendChild(ligne);
}

// Envoie les filtres au contrôleur sans recharger toute la page.
async function filtrerMenus() {
    const parametres = new URLSearchParams();

    if (filtreTheme.value) {
        parametres.append('theme', filtreTheme.value);
    }

    if (filtreRegime.value) {
        parametres.append('regime', filtreRegime.value);
    }

    if (filtrePrixMin.value) {
        parametres.append('prixMin', filtrePrixMin.value);
    }

    if (filtrePrixMax.value) {
        parametres.append('prixMax', filtrePrixMax.value);
    }

    if (filtrePersonnes.value) {
        parametres.append('personnes', filtrePersonnes.value);
    }

    listeMenus.setAttribute('aria-busy', 'true');

    try {
        const response = await fetch(
            `/menus/filtrer?${parametres.toString()}`
        );

        if (!response.ok) {
            throw new Error(`Erreur HTTP : ${response.status}`);
        }

        const menus = await response.json();

        afficherMenus(menus);
    } catch (error) {
        console.error('Erreur lors du filtrage des menus :', error);

        listeMenus.replaceChildren();

        const message = creerElement(
            'div',
            'Impossible de filtrer les menus pour le moment.',
            'alert alert-danger'
        );

        message.setAttribute('role', 'alert');
        listeMenus.appendChild(message);
    } finally {
        listeMenus.setAttribute('aria-busy', 'false');
    }
}

// Les filtres sont appliqués dès qu'une valeur est modifiée.
[
    filtreTheme,
    filtreRegime,
    filtrePrixMin,
    filtrePrixMax,
    filtrePersonnes,
].forEach(filtre => {
    filtre.addEventListener('change', filtrerMenus);
});