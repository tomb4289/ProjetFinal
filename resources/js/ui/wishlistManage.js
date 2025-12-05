document.addEventListener("DOMContentLoaded", () => {
    /* ============================================================
       GESTION QUANTITÉ (+ / -)
       ============================================================ */
    document.querySelectorAll(".wishlist-qty-btn").forEach((btn) => {
        // Empêche double binding
        if (btn.dataset.jsBound === "true") return;
        btn.dataset.jsBound = "true";

        btn.addEventListener("click", () => {
            const container = btn.parentElement;
            const display = container.querySelector(".wishlist-qty-display");

            if (!display) {
                console.error("wishlist-qty-display introuvable !");
                return;
            }

            const oldValue = parseInt(display.textContent);
            let value = oldValue;

            // Up / Down
            if (btn.dataset.direction === "up") {
                value++;
            } else if (btn.dataset.direction === "down" && value > 1) {
                value--;
            }

            // Afficher le spinner pendant le chargement
            const spinnerTemplate = document.getElementById("spinner-inline-template");
            if (spinnerTemplate) {
                const clone = spinnerTemplate.content.cloneNode(true);
                display.innerHTML = "";
                display.appendChild(clone);
            } else {
                // Fallback si le template n'existe pas
                display.innerHTML = `
                    <div 
                        class="inline-block w-6 h-6 border-2 border-neutral-200 border-t-primary rounded-full animate-spin" 
                        role="status" 
                        aria-label="Loading..."
                    ></div>
                `;
            }

            // Construction FormData pour Laravel
            const formData = new FormData();
            formData.append("_method", "PUT");
            formData.append("quantite", value);

            // Requête backend
            fetch(btn.dataset.url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                },
                body: formData,
            })
            .then(res => {
                if (res.ok) {
                    // Mettre à jour l'affichage avec la nouvelle valeur
                    display.textContent = value;
                    showToast("Quantité mise à jour", "success");
                } else {
                    // Restaurer l'ancienne valeur en cas d'erreur
                    display.textContent = oldValue;
                    showToast("Erreur lors de la mise à jour", "error");
                }
            })
            .catch(() => {
                // Restaurer l'ancienne valeur en cas d'erreur réseau
                display.textContent = oldValue;
                showToast("Erreur réseau", "error");
            });
        });
    });

    /* ============================================================
       CHECKBOX : MARQUER COMME ACHETÉ
       ============================================================ */
    document.querySelectorAll(".wishlist-check-achete").forEach((checkbox) => {
        if (checkbox.dataset.jsBound === "true") return;
        checkbox.dataset.jsBound = "true";

        checkbox.addEventListener("change", () => {
            const formData = new FormData();
            formData.append("_method", "PUT");
            formData.append("achete", checkbox.checked ? 1 : 0);

            fetch(checkbox.dataset.url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                },
                body: formData,
            })
                .then((res) => {
                    // Style barré
                    const label = checkbox.parentElement.querySelector("span");

                    if (checkbox.checked) {
                        label.classList.add("line-through", "text-gray-400");
                    } else {
                        label.classList.remove("line-through", "text-gray-400");
                    }

                    showToast("Statut mis à jour", "success");
                })
                .catch(() => showToast("Erreur réseau", "error"));
        });
    });
});

async function refreshStats() {
    const response = await fetch("/api/listeAchat/stats");
    if (!response.ok) return;

    const stats = await response.json();

    const totalItemContainer = document.getElementById("totalItemContainer");
    const averagePriceContainer = document.getElementById(
        "averagePriceContainer"
    );
    const totalPriceContainer = document.getElementById("totalPriceContainer");

    totalItemContainer.textContent = stats.totalItem;
    averagePriceContainer.textContent = stats.averagePrice.toFixed(2) + " $";
    totalPriceContainer.textContent = stats.totalPrice.toFixed(2) + " $";
}
