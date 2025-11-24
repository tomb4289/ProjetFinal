/**
 * Gestion du système de notation par étoiles (0-10)
 * Permet d'afficher et de modifier interactivement la note d'une bouteille
 */

// Sélectionner tous les conteneurs de notation par étoiles
const ratingContainers = document.querySelectorAll('.star-rating-container');

ratingContainers.forEach(container => {
    const isEditable = container.dataset.editable === 'true';
    const stars = container.querySelectorAll('.star-btn');
    const hiddenInput = container.querySelector('input[type="hidden"]');
    const maxRating = parseInt(container.dataset.maxRating) || 10;
    let currentRating = parseInt(container.dataset.rating) || 0;
    
    // Si ce n'est pas éditable, on ne fait rien
    if (!isEditable) {
        return;
    }
    
    stars.forEach((star, index) => {
        const starValue = index + 1;
        
        // Survol de la souris (prévisualisation)
        star.addEventListener('mouseenter', function() {
            // Mettre en surbrillance toutes les étoiles jusqu'à celle survolée
            stars.forEach((s, i) => {
                if (i < starValue) {
                    s.classList.remove('text-gray-300');
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                }
            });
        });
        
        // Sortie de la souris (retour à la valeur actuelle)
        star.addEventListener('mouseleave', function() {
            updateStarDisplay(stars, currentRating);
        });
        
        // Clic sur une étoile
        star.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            
            currentRating = starValue;
            container.dataset.rating = currentRating;
            
            // Mettre à jour l'affichage
            updateStarDisplay(stars, currentRating);
            
            // Mettre à jour l'input caché
            if (hiddenInput) {
                hiddenInput.value = currentRating;
            }
            
            // Mettre à jour le texte affiché
            const ratingText = container.querySelector('span');
            if (ratingText) {
                ratingText.textContent = currentRating + '/10';
            }
        });
    });
});

/**
 * Met à jour l'affichage des étoiles selon la note
 * @param {NodeList} stars - Liste des boutons étoiles
 * @param {number} rating - Note actuelle (0-10)
 */
function updateStarDisplay(stars, rating) {
    stars.forEach((star, index) => {
        const starValue = index + 1;
        if (starValue <= rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}

