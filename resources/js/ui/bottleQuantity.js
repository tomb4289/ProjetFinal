document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll("[data-qty-btn]");
    if (!buttons.length) return;

    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute("content") : null;

    buttons.forEach((btn) => {
        btn.addEventListener("click", async () => {
            const direction = btn.dataset.direction;
            const cellierId = btn.dataset.cellierId;
            const bottleId = btn.dataset.bottleId;

            const valueSpan = document.querySelector(
                `[data-qty-value="${bottleId}"]`
            );

            if (!direction || !cellierId || !bottleId || !valueSpan) {
                console.error("Données manquantes pour la mise à jour de quantité.");
                return;
            }

            try {
                const response = await fetch(
                    `/celliers/${cellierId}/bouteilles/${bottleId}/quantite`,
                    {
                        method: "PATCH",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            ...(csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {}),
                        },
                        body: JSON.stringify({ direction }),
                    }
                );

                if (!response.ok) {
                    console.error("Erreur API quantité", await response.text());
                    return;
                }

                const data = await response.json();

                if (data.success && typeof data.quantite !== "undefined") {
                    valueSpan.textContent = `x ${data.quantite}`;
                }
            } catch (error) {
                console.error("Erreur réseau lors de la mise à jour de quantité", error);
            }
        });
    });
});
