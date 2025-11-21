document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".qty-btn");
    if (!buttons.length) return;

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute("content") : "";

    buttons.forEach((btn) => {
        btn.addEventListener("click", () => {
            const url        = btn.dataset.url;
            const direction  = btn.dataset.direction;
            const bouteilleId = btn.dataset.bouteille;

            const display = document.querySelector(
                `.qty-display[data-bouteille="${bouteilleId}"]`
            );

            if (!url || !direction || !display) {
                console.error("Données manquantes pour la mise à jour de quantité.");
                return;
            }

            const oldText = display.textContent;
            display.textContent = "x ...";

            fetch(url, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ direction }),
            })
                .then((res) => {
                    console.log("Réponse API quantité:", res.status);
                    if (!res.ok) {
                        throw new Error("Réponse serveur non OK");
                    }
                    return res.json();
                })
                .then((data) => {
                    console.log("Données JSON:", data);
                    if (data.success && typeof data.quantite !== "undefined") {
                        display.textContent = `x ${data.quantite}`;
                    } else {
                        display.textContent = oldText;
                    }
                })
                .catch((err) => {
                    console.error("Erreur quantité:", err);
                    display.textContent = oldText;
                });
        });
    });
});
