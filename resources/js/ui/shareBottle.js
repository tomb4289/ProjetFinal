/**
 * Gestion du partage de bouteille
 * 
 * Permet de générer un lien partageable pour une bouteille
 * et de copier ce lien dans le presse-papier.
 */

document.addEventListener('DOMContentLoaded', function() {
    const shareBtn = document.getElementById('shareBottleBtn');
    const shareModal = document.getElementById('shareModal');
    const shareModalClose = document.getElementById('shareModalClose');
    const shareModalContent = document.getElementById('shareModalContent');

    if (!shareBtn || !shareModal || !shareModalClose || !shareModalContent) {
        return; // Les éléments ne sont pas présents sur cette page
    }

    // Ouvrir le modal au clic sur le bouton Partager
    shareBtn.addEventListener('click', function() {
        const bouteilleId = this.getAttribute('data-bouteille-id');
        if (!bouteilleId) {
            console.error('ID de bouteille manquant');
            return;
        }

        // Afficher le modal avec un indicateur de chargement
        shareModal.classList.remove('hidden');
        shareModal.setAttribute('aria-hidden', 'false');
        shareModalContent.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>
        `;

        // Générer le lien de partage
        generateShareLink(bouteilleId);
    });

    // Fermer le modal
    function closeModal() {
        shareModal.classList.add('hidden');
        shareModal.setAttribute('aria-hidden', 'true');
    }

    shareModalClose.addEventListener('click', closeModal);

    // Fermer le modal en cliquant en dehors
    shareModal.addEventListener('click', function(e) {
        if (e.target === shareModal) {
            closeModal();
        }
    });

    // Fermer le modal avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !shareModal.classList.contains('hidden')) {
            closeModal();
        }
    });

    /**
     * Génère un lien de partage pour une bouteille
     */
    async function generateShareLink(bouteilleId) {
        try {
            const response = await fetch(`/api/partage/${bouteilleId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Erreur lors de la génération du lien');
            }

            if (data.success && data.url) {
                displayShareLink(data.url);
            } else {
                throw new Error('Réponse invalide du serveur');
            }
        } catch (error) {
            console.error('Erreur:', error);
            shareModalContent.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-600 mb-4">${error.message || 'Une erreur est survenue lors de la génération du lien.'}</p>
                    <button 
                        onclick="location.reload()"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition-colors">
                        Réessayer
                    </button>
                </div>
            `;
        }
    }

    /**
     * Affiche le lien de partage avec le bouton de copie et les boutons de partage social
     */
    function displayShareLink(url) {
        // Encoder l'URL pour le partage social
        const encodedUrl = encodeURIComponent(url);
        const shareText = encodeURIComponent('Découvrez cette bouteille de vin sur Vino !');
        
        // Construire les chemins des images
        const baseUrl = window.location.origin;
        const facebookIcon = `${baseUrl}/images/icons8-facebook-64.png`;
        const twitterIcon = `${baseUrl}/images/icons8-twitter-circled-64.png`;
        const instagramIcon = `${baseUrl}/images/icons8-instagram-64.png`;
        
        shareModalContent.innerHTML = `
            <div class="space-y-6">
                <p class="text-text-body text-center mb-6 font-medium">
                    Partagez cette bouteille sur vos réseaux sociaux :
                </p>
                
                <div class="flex items-center justify-center gap-5 mb-6">
                    <a 
                        href="https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group flex flex-col items-center justify-center gap-2 transition-all duration-300 hover:scale-105"
                        aria-label="Partager sur Facebook"
                        title="Partager sur Facebook">
                        <div class="flex items-center justify-center w-16 h-16 bg-[#1877F2] hover:bg-[#166FE5] rounded-full transition-all duration-300 shadow-md hover:shadow-lg">
                            <img 
                                src="${facebookIcon}" 
                                alt="Facebook" 
                                class="w-9 h-9"
                            />
                        </div>
                        <span class="text-xs text-text-muted group-hover:text-text-heading transition-colors">Facebook</span>
                    </a>
                    
                    <a 
                        href="https://twitter.com/intent/tweet?url=${encodedUrl}&text=${shareText}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group flex flex-col items-center justify-center gap-2 transition-all duration-300 hover:scale-105"
                        aria-label="Partager sur Twitter"
                        title="Partager sur Twitter">
                        <div class="flex items-center justify-center w-16 h-16 bg-[#1DA1F2] hover:bg-[#1a8cd8] rounded-full transition-all duration-300 shadow-md hover:shadow-lg">
                            <img 
                                src="${twitterIcon}" 
                                alt="Twitter" 
                                class="w-9 h-9"
                            />
                        </div>
                        <span class="text-xs text-text-muted group-hover:text-text-heading transition-colors">Twitter</span>
                    </a>
                    
                    <a 
                        href="https://www.instagram.com/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group flex flex-col items-center justify-center gap-2 transition-all duration-300 hover:scale-105"
                        aria-label="Ouvrir Instagram"
                        title="Ouvrir Instagram pour partager">
                        <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#833AB4] via-[#FD1D1D] to-[#FCAF45] hover:opacity-90 rounded-full transition-all duration-300 shadow-md hover:shadow-lg">
                            <img 
                                src="${instagramIcon}" 
                                alt="Instagram" 
                                class="w-9 h-9"
                            />
                        </div>
                        <span class="text-xs text-text-muted group-hover:text-text-heading transition-colors">Instagram</span>
                    </a>
                </div>

                <div class="border-t border-border-base pt-4">
                    <p class="text-text-body text-sm mb-3 text-center">
                        Ou copiez ce lien :
                    </p>
                    
                    <div class="flex items-center gap-2 p-3 bg-gray-50 border border-border-base rounded-lg">
                        <input 
                            type="text" 
                            id="shareLinkInput"
                            value="${url}" 
                            readonly
                            class="flex-1 bg-transparent border-none outline-none text-sm text-text-body"
                            aria-label="Lien de partage"
                        />
                        <button 
                            id="copyShareLinkBtn"
                            class="flex items-center gap-2 px-4 py-2 bg-button-default border-2 border-primary text-primary font-semibold rounded-lg hover:bg-button-hover hover:text-white active:bg-primary-active transition-colors duration-300"
                            aria-label="Copier le lien dans le presse-papier"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span>Copier</span>
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Ajouter l'événement de copie
        const copyBtn = document.getElementById('copyShareLinkBtn');
        const shareLinkInput = document.getElementById('shareLinkInput');

        if (copyBtn) {
            copyBtn.addEventListener('click', function() {
                copyToClipboard(url, shareLinkInput);
            });
        }

        // Ajouter les événements pour fermer le modal après clic sur les liens sociaux
        const socialLinks = shareModalContent.querySelectorAll('a[target="_blank"]');
        socialLinks.forEach(link => {
            link.addEventListener('click', function() {
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
                window.showToast('Lien copié dans le presse-papier', 'success');
            }

            // Fermer le modal
            closeModal();

        } catch (error) {
            console.error('Erreur lors de la copie:', error);
            if (window.showToast) {
                window.showToast('Erreur lors de la copie du lien', 'error');
            }
        }
    }
});

