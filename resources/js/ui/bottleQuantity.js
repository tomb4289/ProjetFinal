// Gestion des quantités de bouteilles dans la cave
// Sélection des boutons de quantité
const buttons = document.querySelectorAll(".qty-btn");
if (buttons.length) {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute("content") : "";
    // Ajout des écouteurs d'événements aux boutons
    buttons.forEach((btn) => {
        btn.addEventListener("click", () => {
            const url = btn.dataset.url;
            const direction = btn.dataset.direction;
            const bouteilleId = btn.dataset.bouteille;

            const display = document.querySelector(
                `.qty-display[data-bouteille="${bouteilleId}"]`
            );

            if (!url || !direction || !display) {
                console.error(
                    "Données manquantes pour la mise à jour de quantité."
                );
                return;
            }

            const oldText = display.textContent;
            // Indicateur de chargement (Spinner)
            display.innerHTML = `
                <div 
                    class="inline-block w-6 h-6 border-2 border-neutral-200 border-t-primary rounded-full animate-spin" 
                    role="status" 
                    aria-label="Loading..."
                ></div>
            `;
            // Appel API pour mettre à jour la quantité
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
                    console.log("Réponse API quantité:", res.status);
                    if (!res.ok) {
                        throw new Error("Réponse serveur non OK");
                    }
                    return res.json();
                })
                .then((data) => {
                    console.log("Données JSON:", data);
                    if (data.success && typeof data.quantite !== "undefined") {
                        display.textContent = `${data.quantite}`;
                    } else {
                        display.textContent = oldText;
                    }
                })
                .catch((err) => {
                    console.error("Erreur quantité:", err);
                    display.textContent = oldText;
                });
        });
    });
}
