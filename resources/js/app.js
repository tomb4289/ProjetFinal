import "./bootstrap";

// Import des scripts
import "./ui/header";
import "./ui/authForm";
import "./ui/toggleCellarAction";
import "./ui/bottleQuantity";
import "./ui/addWineOptionToggle";
import "./ui/confirmDelete";
import "./ui/addToCellar";
import "./ui/search";
import "./ui/stopLinkPropagation";
import "./ui/starRating";
import "./ui/searchCellier";
import "./ui/typewriter-toast";


/* ============================================================
   MODULE : Toasts – Petites notifications
   ------------------------------------------------------------
   Ces toasts permettent d'informer l'usager sans interrompre
   son expérience. Ils apparaissent en bas de l’écran, puis
   disparaissent automatiquement après un court délai.
   ============================================================ */

/**
 * Affiche un message temporaire (toast) en bas à droite de l'écran.
 *
 * @param {string} message - Le texte à afficher à l’usager.
 * @param {"success"|"error"} type - Définit le style du toast.
 */
window.showToast = function (message, type = "success") {
    const container = document.getElementById("toast-container");
    if (!container) return;

    const toast = document.createElement("div");

    // Style : simple, lisible, efficace
    toast.className = `
        px-4 py-2 rounded shadow text-white text-sm animate-toast
        ${type === "success" ? "bg-green-600" : "bg-red-600"}
    `;

    toast.textContent = message;

    // On affiche le toast au bas de la pile
    container.appendChild(toast);
    container.style.display = "flex";

    // Retrait automatique après quelques secondes
    setTimeout(() => {
        toast.remove();

        // Si tous les toasts sont partis, on cache le conteneur
        if (container.children.length === 0) {
            container.style.display = "none";
        }
    }, 2500);
};

/* ============================================================
   MODULE : Gestion de Quantité
   ------------------------------------------------------------
   La gestion de quantité est maintenant gérée par bottleQuantity.js
   qui utilise les sélecteurs corrects (.qty-btn, .qty-display)
   et la route correcte (/celliers/{cellier}/bouteilles/{bouteille}/quantite)
   ============================================================ */
