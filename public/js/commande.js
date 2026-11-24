/*
 * commande.js
 * Aperçu du prix d'une commande en temps réel, sans rechargement de page.
 *
 * Les données du menu (prix minimum, nombre minimum de personnes) sont
 * transmises par Twig dans des attributs data-* : le HTML reste séparé
 * du JavaScript.
 *
 * Ce calcul sert uniquement à l'affichage. Le serveur (CommandeController)
 * refait toujours le même calcul avant d'enregistrer la commande : un
 * utilisateur qui modifierait ce script ne pourrait pas changer le prix payé.
 */

const detailPrix = document.getElementById('detail-prix');

if (detailPrix) {
    const prixMinimum = Number(detailPrix.dataset.prixMinimum);
    const nombreMinimum = Number(detailPrix.dataset.nombreMinimum);
    const prixParPersonne = prixMinimum / nombreMinimum;

    const personnesInput = document.getElementById('commande_nombrePersonnes');
    const distanceInput = document.getElementById('commande_distanceLivraison');

    // Formate un nombre en euros à la française : 1234.5 -> "1 234,50 €"
    function formaterEuros(montant) {
        return montant.toLocaleString('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }) + ' €';
    }

    // Met à jour l'aperçu du prix selon le nombre de personnes et la distance.
    function mettreAJourPrix() {
        const nombrePersonnes = Math.max(0, Number(personnesInput?.value || 0));
        const distance = Math.max(0, Number(distanceInput?.value || 0));

        // Le prix suit le nombre réel de personnes.
        const prixMenu = prixParPersonne * nombrePersonnes;

        let reduction = 0;
        let livraison = 0;

        // Réduction de 10 % à partir de 5 personnes au-dessus du minimum.
        if (nombrePersonnes >= nombreMinimum + 5) {
            reduction = prixMenu * 0.10;
        }

        // Livraison gratuite à Bordeaux, sinon 5 € + 0,59 € par kilomètre.
        if (distance > 0) {
            livraison = 5 + 0.59 * distance;
        }

        const total = prixMenu - reduction + livraison;

        // textContent : on n'injecte jamais de HTML (protection XSS).
        document.getElementById('prix-menu').textContent = formaterEuros(prixMenu);
        document.getElementById('reduction-prix').textContent = formaterEuros(reduction);
        document.getElementById('frais-livraison').textContent = formaterEuros(livraison);
        document.getElementById('prix-total').textContent = formaterEuros(total);
    }

    personnesInput?.addEventListener('input', mettreAJourPrix);
    distanceInput?.addEventListener('input', mettreAJourPrix);

    // Affiche le bon montant dès l'ouverture de la page.
    mettreAJourPrix();
}
