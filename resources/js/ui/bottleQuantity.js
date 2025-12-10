// Gestion des quantités de bouteilles
function initBottleQuantity() {
    // Sélection des boutons permettant d'augmenter ou diminuer la quantité
    const buttons = document.querySelectorAll(".qty-btn");
    if (buttons.length) {
        // Récupération du token CSRF pour sécuriser les requêtes PATCH
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute("content") : "";

        // Ajout des écouteurs d'événements sur chaque bouton
        buttons.forEach((btn) => {
            btn.addEventListener("click", () => {
                // Récupération des données nécessaires au fonctionnement
                const url = btn.dataset.url; // Route Laravel pour modifier la quantité
                const direction = btn.dataset.direction; // Type d'action ("up" ou "down")
                const bouteilleId = btn.dataset.bouteille; // ID de la bouteille ciblée

                // Élément visuel affichant la quantité pour cette bouteille
                const display = document.querySelector(
                    `.qty-display[data-bouteille="${bouteilleId}"]`
                );

                // Vérification des données nécessaires
                if (!url || !direction || !display) {
                    console.error(
                        "Données manquantes pour la mise à jour de quantité."
                    );
                    return;
                }

                // Empêcher les interactions multiples pendant une mise à jour en cours
                if (display.dataset.loading === "true") {
                    return;
                }

                const oldText = display.textContent; // Mémoriser l'ancienne quantité

                // Indiquer qu'une mise à jour est en cours
                display.dataset.loading = "true";

                // Affichage d'un indicateur de chargement (spinner)
                const spinnerTemplate = document.getElementById(
                    "spinner-inline-template"
                );
                if (spinnerTemplate) {
                    // Utilisation d'un template HTML si disponible
                    const clone = spinnerTemplate.content.cloneNode(true);
                    display.innerHTML = "";
                    display.appendChild(clone);
                } else {
                    // Fallback au cas où aucun template n'existe
                    display.innerHTML = `
                        <div 
                            class="inline-block w-6 h-6 border-2 border-neutral-200 border-t-primary rounded-full animate-spin" 
                            role="status" 
                            aria-label="Loading..."
                        ></div>
                    `;
                }

                // Envoi de la requête PATCH pour modifier la quantité côté serveur
                fetch(url, {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({ direction }),
                })
                    .then((res) => {
                        // Vérifie qu'une réponse valide a été reçue
                        if (!res.ok) {
                            throw new Error("Réponse serveur non OK");
                        }
                        return res.json();
                    })
                    .then((data) => {
                        // Mise à jour de l'affichage selon la réponse retournée par l'API
                        if (
                            data.success &&
                            typeof data.quantite !== "undefined"
                        ) {
                            display.textContent = `${data.quantite}`;
                            showToast("Quantité mise à jour", "success");
                        } else {
                            display.textContent = oldText; // Restaure si anomalie
                            showToast(
                                data.message ||
                                    "Impossible de mettre à jour la quantité",
                                "error"
                            );
                        }

                        // Autoriser de nouveau les interactions
                        display.dataset.loading = "false";
                    })
                    .catch((err) => {
                        // Gestion des erreurs réseau ou serveur
                        console.error("Erreur quantité:", err);
                        display.textContent = oldText; // Restauration de la valeur précédente
                        display.dataset.loading = "false"; // Réactivation des interactions
                    });
            });
        });
    }
}

// Initialisation au chargement de la page
initBottleQuantity();

// Réinitialiser les listeners après un rafraîchissement AJAX du cellier
window.addEventListener("cellierReloaded", initBottleQuantity);
