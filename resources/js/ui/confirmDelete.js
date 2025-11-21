// Model pour la confirmation de suppression
document.addEventListener("DOMContentLoaded", () => {
    // Sélection des éléments du DOM
    const modal = document.getElementById("confirmModal");
    const form = document.getElementById("confirmForm");
    const cancel = document.getElementById("confirmCancel");

    // Ajout des écouteurs d'événements aux boutons de suppression
    document.querySelectorAll(".use-confirm").forEach((btn) => {
        btn.addEventListener("click", () => {
            form.action = btn.dataset.action;
            modal.classList.remove("hidden");
        });
    });

    // Gestion de la fermeture du modal
    cancel.addEventListener("click", () => {
        modal.classList.add("hidden");
    });

    // Fermeture du modal au clic en dehors du contenu
    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.add("hidden");
        }
    });
});
