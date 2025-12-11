/**
 * Gestion du partage de bouteille
 *
 * Permet de générer un lien partageable pour une bouteille
 * et de copier ce lien dans le presse-papier.
 */

document.addEventListener("DOMContentLoaded", function () {
    // Éléments principaux du système de partage
    const shareBtn = document.getElementById("shareBottleBtn");
    const shareModal = document.getElementById("shareModal");
    const shareModalClose = document.getElementById("shareModalClose");
    const shareModalContent = document.getElementById("shareModalContent");

    // Si les éléments n'existent pas, ne pas exécuter ce script
    if (!shareBtn || !shareModal || !shareModalClose || !shareModalContent) {
        return;
    }

    // Ouvre le modal et lance la génération du lien de partage
    shareBtn.addEventListener("click", function () {
        const bouteilleId = this.getAttribute("data-bouteille-id");
        if (!bouteilleId) {
            console.error("ID de bouteille manquant");
            return;
        }

        // Affiche le modal et un indicateur de chargement pendant le fetch
        shareModal.classList.remove("hidden");
        shareModal.setAttribute("aria-hidden", "false");
        afficherChargement();

        // Appel au backend pour obtenir un lien partageable
        generateShareLink(bouteilleId);
    });

    // Ferme le modal et réinitialise son état
    function closeModal() {
        shareModal.classList.add("hidden");
        shareModal.setAttribute("aria-hidden", "true");
    }

    // Bouton de fermeture du modal
    shareModalClose.addEventListener("click", closeModal);

    // Affiche un loader dans la modale pendant le traitement de la requête
    function afficherChargement() {
        const template = document.getElementById("loading-template");
        if (!template) {
            return;
        }
        const clone = template.content.cloneNode(true);
        shareModalContent.innerHTML = "";
        shareModalContent.appendChild(clone);
    }

    // Fermeture du modal au clic en dehors du contenu
    shareModal.addEventListener("click", function (e) {
        if (e.target === shareModal) {
            closeModal();
        }
    });

    // Fermeture via la touche Échap
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && !shareModal.classList.contains("hidden")) {
            closeModal();
        }
    });

    /**
     * Fait une requête vers l'API pour générer un lien de partage
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

            // Parse de la réponse API
            const data = await response.json();

            // Vérification des réponses invalides
            if (!response.ok) {
                throw new Error(
                    data.message || "Erreur lors de la génération du lien"
                );
            }

            // Affiche le lien de partage s'il est valide
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
     * Affiche un message d'erreur dans la modale
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
     * Affiche le lien généré + les options sociales + le bouton copier
     */
    function displayShareLink(url) {
        const template = document.getElementById("share-link-template");
        if (!template) {
            return;
        }

        // Encodage pour les services externes (Facebook / X)
        const encodedUrl = encodeURIComponent(url);
        const shareText = encodeURIComponent(
            "Découvrez cette bouteille de vin sur Vino !"
        );

        const clone = template.content.cloneNode(true);
        const facebookLink = clone.querySelector(".share-facebook-link");
        const twitterLink = clone.querySelector(".share-twitter-link");
        const shareLinkInput = clone.querySelector("#shareLinkInput");

        // Génération des URLs de partage
        facebookLink.href = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
        twitterLink.href = `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${shareText}`;
        shareLinkInput.value = url;

        shareModalContent.innerHTML = "";
        shareModalContent.appendChild(clone);

        setupShareLinkEvents(url);
    }

    /**
     * Configure les événements des boutons : copie, partage, fermeture
     */
    function setupShareLinkEvents(url) {
        const copyBtn = document.getElementById("copyShareLinkBtn");
        const shareLinkInput = document.getElementById("shareLinkInput");

        // Copier le lien via le bouton
        if (copyBtn && shareLinkInput) {
            copyBtn.addEventListener("click", function () {
                copyToClipboard(url, shareLinkInput);
            });
        }

        // Permettre de copier en cliquant sur l'input
        if (shareLinkInput) {
            shareLinkInput.addEventListener("click", function () {
                copyToClipboard(url, shareLinkInput);
            });
        }

        // Fermer le modal après clic sur un réseau social
        const socialLinks =
            shareModalContent.querySelectorAll('a[target="_blank"]');
        socialLinks.forEach((link) => {
            link.addEventListener("click", function () {
                setTimeout(() => closeModal(), 100);
            });
        });
    }

    /**
     * Copie le lien dans le presse-papier puis ferme le modal
     */
    async function copyToClipboard(url, inputElement) {
        try {
            // Sélection du texte dans l'input
            inputElement.select();
            inputElement.setSelectionRange(0, 99999); // Compatibilité mobile

            // Copie native
            await navigator.clipboard.writeText(url);

            if (window.showToast) {
                window.showToast("Lien copié dans le presse-papier", "success");
            }

            closeModal();
        } catch (error) {
            console.error("Erreur lors de la copie:", error);
            if (window.showToast) {
                window.showToast("Erreur lors de la copie du lien", "error");
            }
        }
    }
});
