const boutonsAjouterCellier = document.querySelectorAll(".add-to-cellar-btn");
const boutonFermer = document.getElementById("closeAddWine");
const panneauCellier = document.getElementById("addWineBtnContainer");

// Vérifier que les éléments existent avant de continuer
if (boutonFermer && panneauCellier) {

    let celliersPrecharges = null;
    let idBouteilleActive = null;
    let quantiteActive = 1;

    // Charge les celliers une seule fois
    prechargerCelliers();

    async function prechargerCelliers() {
        celliersPrecharges = await obtenirCelliers();
    }

    boutonFermer.addEventListener("click", fermerPanneau);

    // Ouvrir le panneau quand on clique sur Ajouter
    document.addEventListener("click", (e) => {
        const bouton = e.target.closest(".add-to-cellar-btn");
        if (!bouton) return; // pas un bouton ajouter

        e.preventDefault();

        const formulaire = bouton.closest("form");

        idBouteilleActive = formulaire.querySelector('input[name="bottle_id"]').value;
        quantiteActive =
            parseInt(formulaire.querySelector('input[name="quantity"]').value) || 1;

        ouvrirPanneau();
    });

    async function ouvrirPanneau() {
        panneauCellier.classList.remove("translate-y-full");
        peuplerOptionsCelliers(celliersPrecharges);
    }

    function fermerPanneau() {
        panneauCellier.classList.add("translate-y-full");
    }

    async function obtenirCelliers() {
        const reponse = await fetch("/api/celliers");
        return reponse.json();
    }

    function peuplerOptionsCelliers(celliers) {
        const listeCelliers = document.getElementById("cellar-list");
        listeCelliers.innerHTML = "";

        celliers.forEach((cellier) => {
            listeCelliers.innerHTML += `
                <a 
                    href="#"
                    class="cellar-box block p-3 bg-card rounded-lg shadow-md border border-border-base hover:shadow-sm cursor-pointer"
                    data-cellar-id="${cellier.id}"
                    data-bottle-id="${idBouteilleActive}"
                    data-quantity="${quantiteActive}"
                >
                    <div class="flex justify-between">
                        <div class="flex flex-col gap-1">
                            <h2 class="text-2xl font-semibold">${
                                cellier.nom
                            }</h2>
                            ${
                                cellier.bouteilles_count == 0
                                    ? `<p class="text-gray-400 italic">Aucune bouteille</p>`
                                    : `${cellier.bouteilles_count} Bouteille${
                                          cellier.bouteilles_count > 1 ? "s" : ""
                                      }`
                            }
                        </div>
                    </div>
                </a>
            `;
        });
    }

    // Clique sur un cellier dans le panneau
    document.addEventListener("click", async (e) => {
        const boite = e.target.closest(".cellar-box");
        if (!boite) return;
        
        // Ne prévenir le comportement par défaut que si c'est un cellar-box du modal (avec data-cellar-id)
        // Cela permet aux liens cellar-box normaux de la page d'index de fonctionner normalement
        if (!boite.dataset.cellarId) return;

        e.preventDefault();

        const jetonCsrf = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;

        const idCellier = boite.dataset.cellarId;
        const idBouteille = boite.dataset.bottleId;
        const quantite = boite.dataset.quantity;

        const reponse = await fetch("/api/ajout/cellier", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": jetonCsrf,
            },
            body: JSON.stringify({
                cellar_id: idCellier,
                bottle_id: idBouteille,
                quantity: quantite,
            }),
        });

        const donnees = await reponse.json();
        
        if (donnees.success) {
            if (window.showToast) {
                window.showToast(donnees.message || "Bouteille ajoutée avec succès", "success");
            }
        } else {
            if (window.showToast) {
                window.showToast(donnees.message || "Erreur lors de l'ajout", "error");
            }
        }

        fermerPanneau();
    });
}
