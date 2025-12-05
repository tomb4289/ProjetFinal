const boutonsAjouterCellier = document.querySelectorAll(".add-to-cellar-btn");
const boutonFermer = document.getElementById("closeAddWine");
const panneauCellier = document.getElementById("addWineBtnContainer");
const overlay = document.getElementById("addWineOverlay");

// Vérifier que les éléments existent avant de continuer
if (boutonFermer && panneauCellier) {
    let celliersPrecharges = null;
    let idBouteilleActive = null;
    let quantiteActive = 1;
    let chargementEnCours = false;

    // Précharger les celliers
    obtenirCelliers().then((data) => (celliersPrecharges = data));
    boutonFermer.addEventListener("click", fermerPanneau);

    // Ouvrir le panneau quand on clique sur Ajouter
    document.addEventListener("click", async (e) => {
        const bouton = e.target.closest(".add-to-cellar-btn");
        if (!bouton) return; // pas un bouton ajouter
        e.preventDefault();

        const formulaire = bouton.closest("form");

        idBouteilleActive = formulaire.querySelector(
            'input[name="bottle_id"]'
        ).value;
        quantiteActive =
            parseInt(
                formulaire.querySelector('input[name="quantity"]').value
            ) || 1;

        await ouvrirPanneau();
    });

    // Ouvrir le panneau
    async function ouvrirPanneau() {
        panneauCellier.classList.remove("translate-y-full");
        const listeCelliers = document.getElementById("cellar-list");
        overlay.classList.remove("opacity-0", "pointer-events-none");

        if (!celliersPrecharges) {
            afficherChargement(listeCelliers);

            if (!chargementEnCours) {
                chargementEnCours = true;
                celliersPrecharges = await obtenirCelliers();
                chargementEnCours = false;
            }
        }

        peuplerOptionsCelliers(celliersPrecharges);
    }

    // Fermer le panneau
    function fermerPanneau() {
        overlay.classList.add("opacity-0", "pointer-events-none");

        panneauCellier.classList.add("translate-y-full");
    }

    async function obtenirCelliers() {
        const reponse = await fetch("/api/celliers", {
            headers: {
                Accept: "application/json",
            },
        });
        return reponse.json();
    }

    // Afficher un message de chargement
    function afficherChargement(container) {
        const template = document.getElementById("loading-template");
        if (!template) {
            return;
        }
        const clone = template.content.cloneNode(true);
        container.innerHTML = "";
        container.appendChild(clone);
    }

    function peuplerOptionsCelliers(celliers) {
        const listeCelliers = document.getElementById("cellar-list");
        listeCelliers.innerHTML = "";

        if (celliers.length === 0) {
            const emptyTemplate = document.getElementById("empty-cellars-template");
            if (emptyTemplate) {
                const clone = emptyTemplate.content.cloneNode(true);
                listeCelliers.appendChild(clone);
            }
            return;
        }

        const itemTemplate = document.getElementById("cellar-item-template");
        if (!itemTemplate) {
            return;
        }

        celliers.forEach((cellier) => {
            const clone = itemTemplate.content.cloneNode(true);
            const link = clone.querySelector(".cellar-box");
            const name = clone.querySelector(".cellar-name");
            const count = clone.querySelector(".cellar-count");

            link.dataset.cellarId = cellier.id;
            link.dataset.bottleId = idBouteilleActive;
            link.dataset.quantity = quantiteActive;
            name.textContent = cellier.nom;

            if (
                cellier.total_bouteilles == 0 ||
                cellier.total_bouteilles === null
            ) {
                count.textContent = "Aucune bouteille";
                count.classList.add("text-gray-400", "italic");
            } else {
                count.textContent = `${cellier.total_bouteilles} Bouteille${
                    cellier.total_bouteilles > 1 ? "s" : ""
                }`;
            }

            listeCelliers.appendChild(clone);
        });
    }

    // Clique sur un cellier dans le panneau
    document.addEventListener("click", async (e) => {
        // Recharger les celliers pour s'assurer qu'on a les dernières données
        obtenirCelliers().then((data) => (celliersPrecharges = data));

        const boite = e.target.closest(".cellar-box");
        if (!boite || !boite.dataset.cellarId) return;

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
                Accept: "application/json",
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
                window.showToast(
                    donnees.message || "Bouteille ajoutée avec succès",
                    "success"
                );
            }
        } else {
            if (window.showToast) {
                window.showToast(
                    donnees.message || "Erreur lors de l'ajout",
                    "error"
                );
            }
        }

        fermerPanneau();
    });
}
