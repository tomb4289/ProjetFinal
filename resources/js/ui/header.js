// Cache/affiche le header en fonction du défilement
// Cache le header quand on défile vers le bas et l'affiche quand on remonte
let lastScroll = 0;
const header = document.getElementById("mainHeader");

// Vérifie que le header existe

window.addEventListener("scroll", () => {
    // Position actuelle du défilement
    const currentScroll = window.pageYOffset;

    if (currentScroll <= 0) {
        // Tout en haut de la page → toujours afficher le header
        header.classList.remove("-translate-y-full");
        return;
    }

    if (currentScroll > lastScroll) {
        // Défilement vers le bas → cacher le header (le faire monter)
        header.classList.add("-translate-y-full");
    } else {
        // Défilement vers le haut → afficher le header (le faire redescendre)
        header.classList.remove("-translate-y-full");
    }

    // Met à jour la dernière position connue
    lastScroll = currentScroll;
});
