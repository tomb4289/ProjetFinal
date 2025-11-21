// Afficher ou masquer les boutons d'action de la cave au clic du bouton de réglage
// Sélection des éléments du DOM
const cellarToggleActionBtn = document.getElementById("setting-btn");
const cellarBoxes = document.querySelectorAll(".cellar-box");
// Vérification de l'existence du bouton
if (cellarToggleActionBtn) {
    // Initialisation de l'état du bouton
    let clicked = false;
    cellarToggleActionBtn.addEventListener("click", () => {
        clicked = !clicked;
        if (clicked) {
            // Utiliser des classes Tailwind existantes au lieu de bg-focus et border-muted
            cellarToggleActionBtn.classList.add(
                "bg-blue-600",
                "border-blue-600",
                "border"
            );
            // Affiche les boutons d'action dans chaque boîte de cave
            cellarBoxes.forEach((box) => {
                box.classList.add("animate-shake");
                const actionBtns = box.querySelector(".cellar-action-btns");
                // VÉRIFICATION : s'assurer que l'élément existe avant d'accéder à classList
                if (actionBtns) {
                    actionBtns.classList.remove("hidden");
                }
            });
            // Ajouter des classes pour l'animation de secousse
        } else {
            cellarToggleActionBtn.classList.remove(
                "bg-blue-600",
                "border-blue-600",
                "border"
            );
            // Cacher les boutons d'action dans chaque boîte de cave
            cellarBoxes.forEach((box) => {
                box.classList.remove("animate-shake");
                const actionBtns = box.querySelector(".cellar-action-btns");
                // VÉRIFICATION : s'assurer que l'élément existe avant d'accéder à classList
                if (actionBtns) {
                    actionBtns.classList.add("hidden");
                }
            });
        }
    });
}
