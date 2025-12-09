/**
 * Affiche un toast avec animation d'écriture (typewriter) en rouge vin
 * Position fixe et stable, animation fluide sans saccades
 * 
 * @param {string} message - Le texte à afficher
 * @param {Object} options - Options de configuration
 * @param {string|Object} options.position - Position: 'bottom-right' (défaut), 'top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'center', ou un objet {top, left, right, bottom, transform}
 * @param {number} options.speed - Vitesse d'écriture en ms (défaut: 30)
 * @param {number} options.duration - Durée d'affichage après écriture en ms (défaut: 4000, 0 = infini)
 * @param {string} options.fontSize - Taille de la police (défaut: '1.5rem')
 * @param {Function} options.onComplete - Callback appelé après l'écriture complète
 */
window.showTypewriterToast = function(message, options = {}) {
    const container = document.getElementById("typewriter-toast-container");
    if (!container || !message) return;

    const {
        position = 'bottom-right',
        speed = 30,
        duration = 4000,
        onComplete = null,
        fontSize = '1.5rem'
    } = options;

    // Créer le toast avec position fixe
    const toast = document.createElement("div");
    toast.className = "typewriter-toast-item";
    
    // Styles de base
    const baseStyles = {
        position: 'fixed',
        maxWidth: '400px',
        minWidth: '250px',
        background: 'white',
        border: '2px solid #d1d5db',
        borderRadius: '12px',
        padding: '16px 20px',
        boxShadow: '0 10px 25px rgba(0, 0, 0, 0.15)',
        zIndex: '9999',
        pointerEvents: 'auto',
        opacity: '0',
        transform: 'translateY(20px) scale(0.95)',
        transition: 'opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
    };
    
    // Appliquer les styles de base
    Object.assign(toast.style, baseStyles);
    
    // Appliquer le positionnement
    applyPosition(toast, position);
    
    // Conteneur pour le texte
    const textContainer = document.createElement("span");
    textContainer.className = "typewriter-text-content";
    const fontSpecial = getComputedStyle(document.documentElement).getPropertyValue('--font-special').trim();
    textContainer.style.cssText = `
        font-family: ${fontSpecial || '"Caveat", cursive'};
        font-size: ${fontSize};
        font-weight: 500;
        color: #7a1f3d;
        letter-spacing: 0.03em;
        line-height: 1.4;
        display: block;
        word-wrap: break-word;
    `;
    
    toast.appendChild(textContainer);
    container.appendChild(toast);
    container.style.display = "block";
    
    // Animation d'entrée fluide
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            toast.style.opacity = "1";
            toast.style.transform = toast.style.transform.replace('translateY(20px)', 'translateY(0)').replace('scale(0.95)', 'scale(1)');
        });
    });
    
    // Gérer le stacking des toasts (seulement pour bottom-right)
    if (position === 'bottom-right' || (typeof position === 'object' && position.bottom !== undefined)) {
        const existingToasts = container.querySelectorAll('.typewriter-toast-item');
        existingToasts.forEach((existingToast, index) => {
            if (existingToast !== toast && existingToast.style.bottom) {
                const currentBottom = parseInt(existingToast.style.bottom) || 20;
                existingToast.style.bottom = `${currentBottom + 80}px`;
            }
        });
    }
    
    // Animation d'écriture fluide avec requestAnimationFrame
    let index = 0;
    let lastTime = performance.now();
    let animationFrameId = null;
    
    const writeNext = (currentTime) => {
        if (index < message.length) {
            const elapsed = currentTime - lastTime;
            
            // Calculer le délai basé sur le caractère
            let charDelay = speed;
            const char = message[index];
            
            if (char === ',' || char === '.' || char === '!' || char === '?') {
                charDelay = speed * 2;
            } else if (char === ' ') {
                charDelay = speed * 1.2;
            } else if (char === '\n') {
                charDelay = speed * 1.5;
            } else {
                // Variation subtile pour un effet plus naturel
                charDelay = speed * (0.9 + Math.random() * 0.2);
            }
            
            if (elapsed >= charDelay) {
                textContainer.textContent += char;
                index++;
                lastTime = currentTime;
            }
            
            animationFrameId = requestAnimationFrame(writeNext);
        } else {
            // Écriture terminée
            if (onComplete && typeof onComplete === 'function') {
                onComplete();
            }
            
            // Ajouter un curseur clignotant à la fin
            const cursor = document.createElement("span");
            cursor.className = "typewriter-cursor";
            cursor.textContent = "|";
            cursor.style.cssText = `
                display: inline-block;
                margin-left: 2px;
                animation: blink 1s infinite;
                color: #7a1f3d;
            `;
            textContainer.appendChild(cursor);
            
            if (duration > 0) {
                setTimeout(() => {
                    // Animation de sortie fluide
                    toast.style.opacity = "0";
                    const currentTransform = toast.style.transform;
                    toast.style.transform = currentTransform
                        .replace('translateY(0)', 'translateY(20px)')
                        .replace('scale(1)', 'scale(0.95)');
                    
                    setTimeout(() => {
                        toast.remove();
                        
                        // Réorganiser les toasts restants (seulement pour bottom-right)
                        if (position === 'bottom-right' || (typeof position === 'object' && position.bottom !== undefined)) {
                            const remainingToasts = container.querySelectorAll('.typewriter-toast-item');
                            remainingToasts.forEach((remainingToast, idx) => {
                                if (remainingToast.style.bottom) {
                                    remainingToast.style.bottom = `${20 + idx * 80}px`;
                                }
                            });
                        }
                        
                        if (container.children.length === 0) {
                            container.style.display = "none";
                        }
                    }, 300);
                }, duration);
            }
        }
    };
    
    // Démarrer l'animation
    animationFrameId = requestAnimationFrame(writeNext);
    
    // Nettoyer l'animation si le toast est supprimé
    toast.addEventListener('remove', () => {
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }
    });
};

/**
 * Applique le positionnement au toast de manière stable
 */
function applyPosition(element, position) {
    // Réinitialiser tous les styles de position
    element.style.top = '';
    element.style.bottom = '';
    element.style.left = '';
    element.style.right = '';
    element.style.transform = '';
    
    if (typeof position === 'object') {
        // Position personnalisée avec objet
        Object.keys(position).forEach(key => {
            if (key === 'transform') {
                const currentTransform = element.style.transform || '';
                element.style.transform = currentTransform ? `${currentTransform} ${position[key]}` : position[key];
            } else {
                element.style[key] = typeof position[key] === 'number' 
                    ? `${position[key]}px` 
                    : position[key];
            }
        });
    } else {
        // Positions prédéfinies avec valeurs fixes
        switch(position) {
            case 'top-left':
                element.style.top = '20px';
                element.style.left = '20px';
                break;
            case 'top-center':
                element.style.top = '20px';
                element.style.left = '50%';
                element.style.transform = 'translateX(-50%)';
                break;
            case 'top-right':
                element.style.top = '20px';
                element.style.right = '20px';
                break;
            case 'bottom-left':
                element.style.bottom = '20px';
                element.style.left = '20px';
                break;
            case 'bottom-center':
                element.style.bottom = '20px';
                element.style.left = '50%';
                element.style.transform = 'translateX(-50%)';
                break;
            case 'bottom-right':
            default:
                element.style.bottom = '20px';
                element.style.right = '20px';
                break;
            case 'center':
                element.style.top = '50%';
                element.style.left = '50%';
                element.style.transform = 'translate(-50%, -50%)';
                break;
        }
    }
}
