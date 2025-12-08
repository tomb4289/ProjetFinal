document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".add-to-wishlist-cellar");

    buttons.forEach(btn => {
        if (btn.dataset.bound === "true") return;
        btn.dataset.bound = "true";

        btn.addEventListener("click", async () => {
            const codeSaq = btn.dataset.codeSaq;
            const nom = btn.dataset.nom;
            const quantite = btn.dataset.quantite ?? 1;
            
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
            
            // Fonction pour restaurer l'icône
            const restoreIcon = () => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = originalIconHTML;
                const restoredIcon = tempDiv.firstElementChild;
                spinner.replaceWith(restoredIcon);
                btn.disabled = false;
            };

            try {
                let catalogueData = null;

                // Si code_saq existe, chercher par code_saq
                if (codeSaq && codeSaq !== '') {
                    const catalogueResponse = await fetch(`/api/catalogue/by-code-saq/${encodeURIComponent(codeSaq)}`);
                    
                    if (catalogueResponse.ok) {
                        catalogueData = await catalogueResponse.json();
                    }
                }

                // Si pas trouvé par code_saq et qu'on a un nom, essayer par nom
                if (!catalogueData && nom) {
                    const catalogueResponse = await fetch(`/api/catalogue/by-name/${encodeURIComponent(nom)}`);
                    
                    if (catalogueResponse.ok) {
                        catalogueData = await catalogueResponse.json();
                    }
                }

                // Si toujours pas trouvé, c'est une bouteille manuelle
                if (!catalogueData || !catalogueData.id) {
                    // Restaurer l'icône immédiatement
                    restoreIcon();
                    showToast("Cette bouteille n'est pas dans le catalogue SAQ et ne peut pas être ajoutée à la liste d'achat.", "error");
                    return;
                }

                const formData = new FormData();
                formData.append("bouteille_catalogue_id", catalogueData.id);
                formData.append("quantite", quantite);

                const response = await fetch("/liste-achat", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        "Accept": "application/json"
                    },
                    body: formData
                });

                const data = await response.json();

                // Restaurer l'icône immédiatement après la requête
                restoreIcon();

                if (!response.ok) {
                    showToast(data.message || "Erreur lors de l'ajout.", "error");
                    return;
                }

                showToast(data.message || "Cette bouteille a été ajoutée à votre liste d'achat.", "success");
            } catch (error) {
                console.error("Erreur:", error);
                // Restaurer l'icône en cas d'erreur
                restoreIcon();
                showToast("Erreur réseau", "error");
            }
        });
    });
});
