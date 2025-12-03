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
import "./ui/addToWishlist";
import "./ui/typewriter-toast";
import "./ui/shareBottle";


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
    const isSuccess = type === "success";

    toast.className = `
        pointer-events-auto flex items-center gap-4 w-80 select-none
        bg-white rounded-lg border p-4 shadow-lg
        transition-all duration-300 opacity-0 translate-y-3
        ${isSuccess ? "border-emerald-200" : "border-rose-200"}
    `;

    toast.innerHTML = `
        <div class="flex items-center justify-center h-8 w-8 rounded-full
            ${
                isSuccess
                    ? "bg-emerald-50 text-emerald-600"
                    : "bg-rose-50 text-rose-600"
            }">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                ${
                    isSuccess
                        ? `<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>`
                        : `<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M12 4h.01"/>`
                }
            </svg>
        </div>

        <div class="flex-1">
            <p class="text-sm font-medium text-slate-900">${message}</p>
        </div>

        <button class="text-slate-400 hover:text-slate-600 transition-colors duration-200" aria-label="Fermer">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;

    container.style.display = "flex";
    container.appendChild(toast);

    // smooth entrance animation
    requestAnimationFrame(() =>
        toast.classList.remove("opacity-0", "translate-y-3")
    );

    // manual close
    toast
        .querySelector("button")
        .addEventListener("click", () => removeToast(toast));

    // auto remove
    setTimeout(() => removeToast(toast), 3500);

    function removeToast(el) {
        el.classList.add("opacity-0", "translate-y-3");
        setTimeout(() => {
            el.remove();
            if (!container.children.length) container.style.display = "none";
        }, 250);
    }
};

/* ============================================================
   MODULE : Gestion de Quantité
   ------------------------------------------------------------
   La gestion de quantité est maintenant gérée par bottleQuantity.js
   qui utilise les sélecteurs corrects (.qty-btn, .qty-display)
   et la route correcte (/celliers/{cellier}/bouteilles/{bouteille}/quantite)
   ============================================================ */
