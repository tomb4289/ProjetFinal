/**
 * Gestion du partage de bouteille
 *
 * Permet de générer un lien partageable pour une bouteille
 * et de copier ce lien dans le presse-papier.
 */

document.addEventListener("DOMContentLoaded", function () {
    const shareBtn = document.getElementById("shareBottleBtn");
    const shareModal = document.getElementById("shareModal");
    const shareModalClose = document.getElementById("shareModalClose");
    const shareModalContent = document.getElementById("shareModalContent");

    if (!shareBtn || !shareModal || !shareModalClose || !shareModalContent) {
        return; // Les éléments ne sont pas présents sur cette page
    }

    // Ouvrir le modal au clic sur le bouton Partager
    shareBtn.addEventListener("click", function () {
        const bouteilleId = this.getAttribute("data-bouteille-id");
        if (!bouteilleId) {
            console.error("ID de bouteille manquant");
            return;
        }

        // Afficher le modal avec un indicateur de chargement
        shareModal.classList.remove("hidden");
        shareModal.setAttribute("aria-hidden", "false");
        afficherChargement();

        // Générer le lien de partage
        generateShareLink(bouteilleId);
    });

    // Fermer le modal
    function closeModal() {
        shareModal.classList.add("hidden");
        shareModal.setAttribute("aria-hidden", "true");
    }

    shareModalClose.addEventListener("click", closeModal);

    // Afficher un message de chargement
    function afficherChargement() {
        const template = document.getElementById("loading-template");
        if (!template) {
            return;
        }
        const clone = template.content.cloneNode(true);
        shareModalContent.innerHTML = "";
        shareModalContent.appendChild(clone);
    }

    // Fermer le modal en cliquant en dehors
    shareModal.addEventListener("click", function (e) {
        if (e.target === shareModal) {
            closeModal();
        }
    });

    // Fermer le modal avec la touche Échap
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && !shareModal.classList.contains("hidden")) {
            closeModal();
        }
    });

    /**
     * Génère un lien de partage pour une bouteille
     */
    async function generateShareLink(bouteilleId) {
        try {
            const response = await fetch(`/api/partage/${bouteilleId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.message || "Erreur lors de la génération du lien"
                );
            }

            if (data.success && data.url) {
                displayShareLink(data.url);
            } else {
                throw new Error("Réponse invalide du serveur");
            }
        } catch (error) {
            console.error("Erreur:", error);
            afficherErreur(
                error.message ||
                    "Une erreur est survenue lors de la génération du lien."
            );
        }
    }

    /**
     * Affiche un message d'erreur
     */
    function afficherErreur(message) {
        const template = document.getElementById("share-error-template");
        if (!template) {
            return;
        }
        const clone = template.content.cloneNode(true);
        const errorMessage = clone.querySelector(".share-error-message");
        errorMessage.textContent = message;
        shareModalContent.innerHTML = "";
        shareModalContent.appendChild(clone);
    }

    /**
     * Affiche le lien de partage avec le bouton de copie et les boutons de partage social
     */
    function displayShareLink(url) {
        const template = document.getElementById("share-link-template");
        if (!template) {
            return;
        }

        // Encoder l'URL pour le partage social
        const encodedUrl = encodeURIComponent(url);
        const shareText = encodeURIComponent(
            "Découvrez cette bouteille de vin sur Vino !"
        );

        const clone = template.content.cloneNode(true);
        const facebookLink = clone.querySelector(".share-facebook-link");
        const twitterLink = clone.querySelector(".share-twitter-link");
        const shareLinkInput = clone.querySelector("#shareLinkInput");

        // Définir les URLs de partage
        facebookLink.href = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
        twitterLink.href = `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${shareText}`;
        shareLinkInput.value = url;

        shareModalContent.innerHTML = "";
        shareModalContent.appendChild(clone);

        setupShareLinkEvents(url);
    }

    /**
     * Configure les événements pour les boutons de copie et les liens sociaux
     */
    function setupShareLinkEvents(url) {
        // Ajouter l'événement de copie
        const copyBtn = document.getElementById("copyShareLinkBtn");
        const copyText = document.getElementById("copyShareLinkText");
        const shareLinkInput = document.getElementById("shareLinkInput");

        if (copyBtn && shareLinkInput) {
            copyBtn.addEventListener("click", function () {
                copyToClipboard(url, shareLinkInput);
            });
        }

        if (copyText && shareLinkInput) {
            copyText.addEventListener("click", function () {
                copyToClipboard(url, shareLinkInput);
            });
        }

        // Ajouter les événements pour fermer le modal après clic sur les liens sociaux
        const socialLinks =
            shareModalContent.querySelectorAll('a[target="_blank"]');
        socialLinks.forEach((link) => {
            link.addEventListener("click", function () {
                // Fermer le modal après un court délai pour permettre l'ouverture du lien
                setTimeout(() => {
                    closeModal();
                }, 100);
            });
        });
    }

    /**
     * Copie le lien dans le presse-papier et ferme le modal
     */
    async function copyToClipboard(url, inputElement) {
        try {
            // Sélectionner le texte dans l'input
            inputElement.select();
            inputElement.setSelectionRange(0, 99999); // Pour mobile

            // Copier dans le presse-papier
            await navigator.clipboard.writeText(url);

            // Afficher un toast de confirmation
            if (window.showToast) {
                window.showToast("Lien copié dans le presse-papier", "success");
            }

            // Fermer le modal
            closeModal();
        } catch (error) {
            console.error("Erreur lors de la copie:", error);
            if (window.showToast) {
                window.showToast("Erreur lors de la copie du lien", "error");
            }
        }
    }
});
