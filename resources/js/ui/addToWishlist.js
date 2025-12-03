function initWishlistButtons() {
    const buttons = document.querySelectorAll(".add-to-wishlist");

    buttons.forEach(btn => {

        if (btn.dataset.jsBound === "true") return;
        btn.dataset.jsBound = "true";

        btn.addEventListener("click", () => {
            const id = btn.dataset.id;

            const formData = new FormData();
            formData.append("bouteille_catalogue_id", id);

            fetch("/liste-achat", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Accept": "application/json"
                },
                body: formData
            })
            .then(async res => {
                const data = await res.json();

                if (!res.ok) {
                    showToast("Erreur lors de l’ajout.", "error");
                    return;
                }

                showToast(data.message || "Bouteille ajoutée à votre liste d'achat.", "success");
            })
            .catch(() => showToast("Erreur réseau", "error"));
        });
    });
}

document.addEventListener("DOMContentLoaded", initWishlistButtons);
window.addEventListener("catalogueReloaded", initWishlistButtons);