const cellarToggleActionBtn = document.getElementById("setting-btn");
const cellarBoxes = document.querySelectorAll(".cellar-box");
if (cellarToggleActionBtn) {
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
            cellarBoxes.forEach((box) => {
                box.classList.add("animate-shake");
                const actionBtns = box.querySelector(".cellar-action-btns");
                // VÉRIFICATION : s'assurer que l'élément existe avant d'accéder à classList
                if (actionBtns) {
                    actionBtns.classList.remove("hidden");
                }
            });
        } else {
            cellarToggleActionBtn.classList.remove(
                "bg-blue-600",
                "border-blue-600",
                "border"
            );
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
