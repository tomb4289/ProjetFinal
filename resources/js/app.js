import "./bootstrap";

// Import des scripts
import "./ui/header";
import "./ui/authForm";
import "./ui/toggleCellarAction";
import "./ui/addWineOptionToggle";

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
   MODULE : Gestion de Quantité (mise à jour optimiste)
   ------------------------------------------------------------
   Lorsqu’un usager clique sur + ou −, la quantité change
   immédiatement à l'écran. Ensuite, on envoie la mise à jour
   au serveur. Si le serveur refuse (ex : quantité négative),
   on remet l’ancienne valeur et on affiche un toast d’erreur.
   ============================================================ */

document.querySelectorAll(".bottle").forEach((bottle) => {
    const id = bottle.dataset.id;
    const qtyBadge = bottle.querySelector(".quantite");
    const btnPlus = bottle.querySelector(".btn-plus");
    const btnMinus = bottle.querySelector(".btn-minus");

    // Cliquer sur + augmente la quantité
    if (btnPlus) {
        btnPlus.addEventListener("click", () =>
            updateQuantity(id, qtyBadge, +1)
        );
    }

    // Cliquer sur − diminue la quantité
    if (btnMinus) {
        btnMinus.addEventListener("click", () =>
            updateQuantity(id, qtyBadge, -1)
        );
    }
});

/**
 * Met à jour la quantité d'une bouteille avec une approche optimiste.
 *
 * @param {number} id - Identifiant de la bouteille.
 * @param {HTMLElement} qtyBadge - Badge affichant la quantité.
 * @param {number} delta - +1 ou -1 selon le bouton pressé.
 */

function updateQuantity(id, qtyBadge, delta) {
    const oldValue = parseInt(qtyBadge.textContent, 10);
    const newValue = oldValue + delta;

    // On bloque dès le départ les quantités négatives
    if (newValue < 0) {
        showToast("Erreur : La quantité ne peut pas être négative.", "error");
        return;
    }

    // Mise à jour immédiate à l'écran (optimiste)
    qtyBadge.textContent = newValue;

    // Envoi de la mise à jour au serveur
    fetch(`/bouteilles/${id}/quantite`, {
        method: "PATCH",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']")
                .content,
        },
        body: JSON.stringify({ quantite: newValue }),
    })
        .then((res) => res.json())
        .then((data) => {
            // Réponse positive → toast de succès
            if (data.success) {
                showToast(data.message, "success");
            } else {
                // Réponse négative → retour à l’ancienne valeur
                qtyBadge.textContent = oldValue;
                showToast(data.message, "error");
            }
        })
        .catch(() => {
            // Erreur réseau → retour à la valeur précédente
            qtyBadge.textContent = oldValue;
            showToast("Erreur serveur. Réessayez plus tard.", "error");
        });
}

