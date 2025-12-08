function initWishlistButtons() {
    const buttons = document.querySelectorAll(".add-to-wishlist");

    buttons.forEach(btn => {

        if (btn.dataset.jsBound === "true") return;
        btn.dataset.jsBound = "true";

        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            
            // Récupérer l'icône originale
            const icon = btn.querySelector('svg, [class*="lucide"]');
            if (!icon) return;
            
            // Sauvegarder l'icône originale
            const originalIconHTML = icon.outerHTML;
            
            // Utiliser le template spinner-inline-template
            const spinnerTemplate = document.getElementById('spinner-inline-template');
            if (!spinnerTemplate) return;
            
            const spinner = spinnerTemplate.content.cloneNode(true).firstElementChild;
            // Ajuster la taille pour correspondre à l'icône (h-5 w-5 au lieu de w-6 h-6)
            spinner.className = spinner.className.replace('w-6 h-6', 'w-5 h-5');
            icon.replaceWith(spinner);
            
            // Désactiver le bouton pendant le chargement
            btn.disabled = true;

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

                // Restaurer l'icône immédiatement après la requête
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = originalIconHTML;
                const restoredIcon = tempDiv.firstElementChild;
                spinner.replaceWith(restoredIcon);
                btn.disabled = false;

                if (!res.ok) {
                    showToast("Erreur lors de l'ajout.", "error");
                    return;
                }

                showToast(data.message || "Bouteille ajoutée à votre liste d'achat.", "success");
            })
            .catch(() => {
                // Restaurer l'icône en cas d'erreur
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = originalIconHTML;
                const restoredIcon = tempDiv.firstElementChild;
                spinner.replaceWith(restoredIcon);
                btn.disabled = false;
                showToast("Erreur réseau", "error");
            });
        });
    });
}

document.addEventListener("DOMContentLoaded", initWishlistButtons);
window.addEventListener("catalogueReloaded", initWishlistButtons);