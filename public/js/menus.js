const filtreTheme = document.getElementById('filtre-theme');
const filtreRegime = document.getElementById('filtre-regime');
const filtrePrixMin = document.getElementById('filtre-prix-min');
const filtrePrixMax = document.getElementById('filtre-prix-max');
const filtrePersonnes = document.getElementById('filtre-personnes');
const listeMenus = document.getElementById('liste-menus');

function filtrerMenus() {
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

    fetch('/menus/filtrer?' + parametres.toString())
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur HTTP : ' + response.status);
            }

            return response.json();
        })
        .then(menus => {
            let html = '<div class="row">';

            if (menus.length === 0) {
                html = '<p>Aucun menu ne correspond aux filtres.</p>';
            } else {
                menus.forEach(menu => {
                    html += `
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h2>${menu.titre}</h2>
                                    <p>${menu.description}</p>
                                    <p><strong>Thème :</strong> ${menu.theme}</p>
                                    <p><strong>Régime :</strong> ${menu.regime}</p>
                                    <p><strong>À partir de :</strong> ${menu.prix} €</p>
                                    <p><strong>Minimum :</strong> ${menu.minimumPersonnes} personnes</p>
                                    <p><strong>Stock :</strong> ${menu.stock}</p>
                                    <p><a href="/menus/${menu.id}">Voir le menu</a></p>
                                </div>
                            </div>
                        </div>
                    `;

                });

                html += '</div>';
            }

            listeMenus.innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur :', error);
        });
}

filtreTheme.addEventListener('change', filtrerMenus);
filtreRegime.addEventListener('change', filtrerMenus);
filtrePrixMin.addEventListener('change', filtrerMenus);
filtrePrixMax.addEventListener('change', filtrerMenus);
filtrePersonnes.addEventListener('change', filtrerMenus);