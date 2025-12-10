// Sélection des éléments du DOM nécessaires pour l'interface d'ajout au cellier
const boutonsAjouterCellier = document.querySelectorAll(".add-to-cellar-btn");
const boutonFermer = document.getElementById("closeAddWine");
const panneauCellier = document.getElementById("addWineBtnContainer");
const overlay = document.getElementById("addWineOverlay");
const boutonCreateCellierDisabled = document.getElementById(
    "create-cellar-disabled-btn"
);

// Vérifier que les éléments existent avant de continuer pour éviter des erreurs
if (boutonFermer && panneauCellier) {
    // Variables d'état pour gérer les données et l'interface
    let celliersPrecharges = null; // Contiendra les celliers obtenus de l'API
    let idBouteilleActive = null; // ID de la bouteille sélectionnée
    let quantiteActive = 1; // Quantité par défaut lors de l'ajout
    let chargementEnCours = false; // Empêche les requêtes multiples simultanées

    // Précharger les celliers au chargement de la page pour améliorer la réactivité
    obtenirCelliers().then((data) => {
        celliersPrecharges = data;
    });

    // Attacher l'événement de fermeture au bouton dédié
    boutonFermer.addEventListener("click", fermerPanneau);

    // Fonction pour gérer l'ajout au cellier (réutilisable pour clic et Enter)
    async function handleAddToCellar(bouton) {
        // Récupération des données depuis le formulaire parent du bouton
        const formulaire = bouton.closest("form");

        // Chaque bouton lié possède un input contenant l'ID de la bouteille
        idBouteilleActive = formulaire.querySelector(
            'input[name="bottle_id"]'
        ).value;

        // Récupérer la quantité sélectionnée, avec fallback sur 1
        quantiteActive =
            parseInt(
                formulaire.querySelector('input[name="quantity"]').value
            ) || 1;

        // Lancer l'ouverture du panneau latéral
        await ouvrirPanneau();
    }

    // Écouteur global pour détecter les clics sur les boutons "Ajouter au cellier"
    // Utilisation de la délégation d'événements car les boutons peuvent être dynamiques
    document.addEventListener("click", async (e) => {
        const bouton = e.target.closest(".add-to-cellar-btn");
        if (!bouton) return; // Si ce n'est pas un bouton d'ajout, on ignore
        e.preventDefault(); // Empêcher la soumission standard du formulaire
        await handleAddToCellar(bouton);
    });

    // Écouteur global pour détecter la touche Enter dans les champs de quantité
    // Utilisation de la délégation d'événements pour les éléments dynamiques
    document.addEventListener("keydown", async (e) => {
        // Vérifier si c'est la touche Enter et si l'input est dans un formulaire d'ajout au cellier
        if (e.key === "Enter" || e.keyCode === 13) {
            const input = e.target;
            const formulaire = input.closest(".add-to-cellar-form");
            
            if (formulaire && input.name === "quantity") {
                e.preventDefault(); // Empêcher la soumission du formulaire
                e.stopPropagation(); // Empêcher la propagation de l'événement
                const bouton = formulaire.querySelector(".add-to-cellar-btn");
                if (bouton) {
                    await handleAddToCellar(bouton);
                }
            }
        }
    });

    // Écouteur pour empêcher la soumission du formulaire (sécurité supplémentaire)
    document.addEventListener("submit", (e) => {
        const formulaire = e.target;
        if (formulaire && formulaire.classList.contains("add-to-cellar-form")) {
            e.preventDefault();
            e.stopPropagation();
            const bouton = formulaire.querySelector(".add-to-cellar-btn");
            if (bouton) {
                handleAddToCellar(bouton);
            }
        }
    });

    // Fonction pour ouvrir le panneau et charger les données si nécessaire
    async function ouvrirPanneau() {
        // Animation d'entrée : retrait de la translation et affichage de l'overlay
        panneauCellier.classList.remove("translate-y-full");
        const listeCelliers = document.getElementById("cellar-list");
        overlay.classList.remove("opacity-0", "pointer-events-none");

        // Afficher un loader si les celliers ne sont pas encore chargés
        if (!celliersPrecharges) {
            afficherChargement(listeCelliers);

            // Évite d'appeler plusieurs fois l'API si la requête est déjà en cours
            if (!chargementEnCours) {
                chargementEnCours = true;
                const data = await obtenirCelliers();
                celliersPrecharges = data;
                chargementEnCours = false;
            }
        }

        // Une fois les données disponibles, on génère la liste des options
        peuplerOptionsCelliers(celliersPrecharges);
    }

    // Fonction pour fermer le panneau et masquer l'overlay
    function fermerPanneau() {
        overlay.classList.add("opacity-0", "pointer-events-none");
        panneauCellier.classList.add("translate-y-full");
    }

    // Appel API pour récupérer la liste des celliers de l'utilisateur
    async function obtenirCelliers() {
        // Route API exposée par Laravel retournant les celliers de l'utilisateur
        const reponse = await fetch("/api/celliers", {
            headers: {
                Accept: "application/json",
            },
        });
        const data = await reponse.json();

        // Normalisation de la réponse API :
        // Si la réponse contient la clé 'celliers', on utilise cette structure.
        // Sinon, on retourne les données telles quelles (support ancien format).
        return data.celliers !== undefined
            ? data
            : { celliers: data, canCreateMore: true };
    }

    // Afficher un indicateur visuel de chargement (spinner/skeleton) dans le conteneur
    function afficherChargement(container) {
        const template = document.getElementById("loading-template");
        if (!template) {
            return;
        }
        const clone = template.content.cloneNode(true);
        container.innerHTML = ""; // Vider le contenu actuel
        container.appendChild(clone);
    }

    // Générer le HTML pour la liste des celliers disponibles
    function peuplerOptionsCelliers(data) {
        const listeCelliers = document.getElementById("cellar-list");
        listeCelliers.innerHTML = ""; // Réinitialiser la liste

        // Extraction des données selon la structure reçue
        const celliers = data.celliers || data;
        const canCreateMore =
            data.canCreateMore !== undefined ? data.canCreateMore : true;

        // Cas où l'utilisateur n'a aucun cellier
        if (celliers.length === 0) {
            const emptyTemplate = document.getElementById(
                "empty-cellars-template"
            );
            if (emptyTemplate) {
                const clone = emptyTemplate.content.cloneNode(true);
                const createButton = clone.querySelector(
                    'a[href="/celliers/create"]'
                );

                // Si l'utilisateur a atteint la limite de celliers autorisés
                if (!canCreateMore && createButton) {
                    // Désactivation visuelle du bouton
                    createButton.classList.add(
                        "opacity-50",
                        "cursor-not-allowed",
                        "bg-gray-300",
                        "border-gray-400",
                        "text-gray-500"
                    );
                    createButton.classList.remove(
                        "bg-button-default",
                        "border-primary",
                        "text-primary",
                        "hover:bg-button-hover",
                        "hover:text-white"
                    );
                    createButton.style.pointerEvents = "auto"; // Permet de cliquer malgré l'état désactivé
                    createButton.href = "#"; // Neutralise la vraie route

                    // Affiche un toast expliquant la limite maximale
                    createButton.addEventListener("click", (e) => {
                        e.preventDefault();
                        if (window.showToast) {
                            window.showToast(
                                "Vous avez atteint la limite maximale de 6 celliers. Veuillez supprimer un cellier existant avant d'en créer un nouveau.",
                                "error"
                            );
                        }
                    });

                    // Mise à jour du message textuel d'information
                    const message = clone.querySelector("p");
                    if (message) {
                        message.textContent =
                            "Vous avez atteint la limite maximale de 6 celliers. Veuillez supprimer un cellier existant avant d'en créer un nouveau.";
                    }
                }

                listeCelliers.appendChild(clone);
            }
            return;
        }

        // Récupération du template pour un item de cellier
        const itemTemplate = document.getElementById("cellar-item-template");
        if (!itemTemplate) {
            return;
        }

        // Boucle pour créer chaque élément de cellier dans la liste
        celliers.forEach((cellier) => {
            const clone = itemTemplate.content.cloneNode(true);
            const link = clone.querySelector(".cellar-box");
            const name = clone.querySelector(".cellar-name");
            const count = clone.querySelector(".cellar-count");

            // Ajout des données nécessaires pour l'envoi de la requête POST
            link.dataset.cellarId = cellier.id;
            link.dataset.bottleId = idBouteilleActive;
            link.dataset.quantity = quantiteActive;
            name.textContent = cellier.nom;

            // Afficher le nombre de bouteilles dans le cellier
            if (
                cellier.total_bouteilles == 0 ||
                cellier.total_bouteilles === null
            ) {
                count.textContent = "Aucune bouteille";
                count.classList.add("text-gray-400", "italic");
            } else {
                count.textContent = `${cellier.total_bouteilles} bouteille${
                    cellier.total_bouteilles > 1 ? "s" : ""
                }`;
            }

            listeCelliers.appendChild(clone);
        });
    }

    // Gestion du clic sur un cellier spécifique dans le panneau pour ajouter la bouteille
    document.addEventListener("click", async (e) => {
        // Recharger les celliers pour s'assurer d'avoir les données les plus récentes
        const data = await obtenirCelliers();
        celliersPrecharges = data;

        const boite = e.target.closest(".cellar-box");
        if (!boite || !boite.dataset.cellarId) return;

        e.preventDefault();

        // Récupération du token CSRF pour sécuriser la requête côté Laravel
        const jetonCsrf = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;

        // Extraction des données stockées dans les attributs data-*
        const idCellier = boite.dataset.cellarId;
        const idBouteille = boite.dataset.bottleId;
        const quantite = boite.dataset.quantity;

        // Envoi de la requête d'ajout au cellier
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

        // Afficher un toast selon la réponse du serveur
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

        // Fermer le panneau une fois l'opération terminée
        fermerPanneau();
    });

    // Gestionnaire spécifique pour le bouton "Créer un cellier" désactivé dans la navigation principale
    if (boutonCreateCellierDisabled) {
        boutonCreateCellierDisabled.addEventListener("click", (e) => {
            e.preventDefault();
            if (window.showToast) {
                window.showToast(
                    "Vous avez atteint la limite maximale de 6 celliers. Veuillez supprimer un cellier existant avant d'en créer un nouveau.",
                    "error"
                );
            }
        });
    }
}
