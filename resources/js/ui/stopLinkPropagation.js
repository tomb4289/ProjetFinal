/**
 * Empêche la propagation des événements de clic sur les éléments interactifs
 * à l'intérieur des liens cliquables (cartes de bouteilles).
 * 
 * Cette fonctionnalité permet d'avoir des cartes cliquables tout en gardant
 * des boutons et formulaires fonctionnels à l'intérieur.
 */

// Sélectionner tous les éléments avec la classe stop-link-propagation
const stopPropagationElements = document.querySelectorAll('.stop-link-propagation');

stopPropagationElements.forEach(element => {
    // Empêcher la propagation des événements de clic
    element.addEventListener('click', function(event) {
        event.stopPropagation();
        event.preventDefault();
    });
});

