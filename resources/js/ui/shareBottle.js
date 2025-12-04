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
                        <div class="flex items-center justify-center w-16 h-16 bg-primary hover:bg-primary-hover rounded-full transition-all duration-300 shadow-md hover:shadow-lg">
                            <svg class="w-9 h-9 fill-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
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
                        <div class="flex items-center justify-center w-16 h-16 bg-primary hover:bg-primary-hover rounded-full transition-all duration-300 shadow-md hover:shadow-lg">
                            <svg class="w-9 h-9 fill-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
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
                        <div class="flex items-center justify-center w-16 h-16 bg-primary hover:bg-primary-hover rounded-full transition-all duration-300 shadow-md hover:shadow-lg">
                            <svg class="w-9 h-9 fill-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                            </svg>
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

