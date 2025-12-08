// Model pour la confirmation de suppression
// Sélection des éléments du DOM
const modal = document.getElementById("confirmModal");
const form = document.getElementById("confirmForm");
const cancel = document.getElementById("confirmCancel");
const confirmMessage = document.getElementById("confirmMessage");

if (modal && form && cancel) {
    let currentButton = null;

    // Ajout des écouteurs d'événements aux boutons de suppression
    document.querySelectorAll(".use-confirm").forEach((btn) => {
        btn.addEventListener("click", () => {
            currentButton = btn;
            form.action = btn.dataset.action;
            
            // Mettre à jour le message si fourni
            if (confirmMessage && btn.dataset.message) {
                confirmMessage.textContent = btn.dataset.message;
            } else if (confirmMessage) {
                confirmMessage.textContent = "Êtes-vous sûr ? Veuillez confirmer la suppression.";
            }
            
            modal.classList.remove("hidden");
            modal.setAttribute("aria-hidden", "false");
        });
    });

    // Gestion de la soumission du formulaire
    form.addEventListener("submit", async (e) => {
        // Si c'est une action AJAX, intercepter la soumission
        if (currentButton && currentButton.dataset.ajax === "true") {
            e.preventDefault();
            
            const url = form.action;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            if (!csrfToken) {
                showToast("Erreur de sécurité. Veuillez recharger la page.", "error");
                modal.classList.add("hidden");
                modal.setAttribute("aria-hidden", "true");
                return;
            }

            // Désactiver le bouton de confirmation pendant la requête
            const confirmBtn = form.querySelector('button[type="submit"]');
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.classList.add("opacity-50", "cursor-not-allowed");
            }

            try {
                const response = await fetch(url, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Accept": "application/json",
                        "Content-Type": "application/json",
                    },
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showToast(
                        data.message || "Suppression réussie.",
                        "success"
                    );
                    
                    // Fermer la modale
                    modal.classList.add("hidden");
                    modal.setAttribute("aria-hidden", "true");
                    
                    // Recharger la page après un court délai
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast(
                        data.message || "Erreur lors de la suppression.",
                        "error"
                    );
                    if (confirmBtn) {
                        confirmBtn.disabled = false;
                        confirmBtn.classList.remove("opacity-50", "cursor-not-allowed");
                    }
                }
            } catch (error) {
                console.error("Erreur:", error);
                showToast("Erreur réseau. Veuillez réessayer.", "error");
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.classList.remove("opacity-50", "cursor-not-allowed");
                }
            }
        }
        // Sinon, laisser le formulaire se soumettre normalement
    });

    // Gestion de la fermeture du modal
    cancel.addEventListener("click", () => {
        modal.classList.add("hidden");
        modal.setAttribute("aria-hidden", "true");
        currentButton = null;
    });

    // Fermeture du modal au clic en dehors du contenu
    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.add("hidden");
            modal.setAttribute("aria-hidden", "true");
            currentButton = null;
        }
    });
}
