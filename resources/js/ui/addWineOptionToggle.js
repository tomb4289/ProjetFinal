document.addEventListener("DOMContentLoaded", () => {
    const panel = document.getElementById("addWineBtnContainer");
    const openBtn = document.getElementById("addWineToCellar");
    const closeBtn = document.getElementById("closeAddWine");

    if (!panel || !openBtn || !closeBtn) return;
    openBtn.addEventListener("click", () => {
        panel.classList.remove("translate-y-full");
        panel.classList.add("translate-y-0");
    });

    closeBtn.addEventListener("click", () => {
        panel.classList.add("translate-y-full");
        panel.classList.remove("translate-y-0");
    });
});
