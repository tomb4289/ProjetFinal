// Sélection des éléments du DOM
const panel = document.getElementById("addWineBtnContainer");
const openBtn = document.getElementById("addWineToCellar");
const closeBtn = document.getElementById("closeAddWine");

// Vérification de l'existence des éléments
if (panel && openBtn && closeBtn) {
    // Ajout des écouteurs d'événements pour ouvrir et fermer le panneau
    openBtn.addEventListener("click", () => {
        panel.classList.remove("translate-y-full");
        panel.classList.add("translate-y-0");
    });

    // Fermeture du panneau
    closeBtn.addEventListener("click", () => {
        panel.classList.add("translate-y-full");
        panel.classList.remove("translate-y-0");
    });
}
