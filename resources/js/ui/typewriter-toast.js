/**
 * Affiche un toast avec animation d'écriture (typewriter) en rouge vin
 * Positionnement personnalisable
 * 
 * @param {string} message - Le texte à afficher
 * @param {Object} options - Options de configuration
 * @param {string|Object} options.position - Position: 'top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right', 'center', ou un objet {top, left, right, bottom}
 * @param {number} options.speed - Vitesse d'écriture en ms (défaut: 50)
 * @param {number} options.duration - Durée d'affichage après écriture en ms (défaut: 3000, 0 = infini)
 */
window.showTypewriterToast = function(message, options = {}) {
    const container = document.getElementById("typewriter-toast-container");
    if (!container || !message) return;

    const {
        position = 'top-center',
        speed = 70,
        duration = 3000,
        onComplete = null
    } = options;

    const toast = document.createElement("div");
    toast.className = `
        text-primary font-medium text-base text-center
        animate-fade-in
        relative
    `;
    toast.style.display = 'flex';
    toast.style.flexDirection = 'column';
    toast.style.alignItems = 'center';
    
    const textContainer = document.createElement("span");
    textContainer.className = "typewriter-text handwriting-text";
    textContainer.style.fontFamily = '"Caveat", cursive';
    textContainer.style.fontSize = '1.5rem';
    textContainer.style.fontWeight = '500';
    textContainer.style.letterSpacing = '0.03em';
    textContainer.style.display = 'inline-block';
    
    toast.appendChild(textContainer);
    container.appendChild(toast);
    container.style.display = "block";
    
    applyPosition(toast, position);

    let index = 0;
    const writeNext = () => {
        if (index < message.length) {
            const char = message[index];
            textContainer.textContent += char;
            index++;
            
            let nextDelay = speed;
            if (char === ',' || char === '.' || char === '!' || char === '?') {
                nextDelay = speed * 2.5;
            } else if (char === ' ') {
                nextDelay = speed * 1.5;
            } else {
                nextDelay = speed * (0.8 + Math.random() * 0.4);
            }
            
            setTimeout(writeNext, nextDelay);
        } else {
            if (onComplete && typeof onComplete === 'function') {
                onComplete();
            }
            
            if (duration > 0) {
                setTimeout(() => {
                    toast.style.opacity = "0";
                    toast.style.transform = "translateY(-20px)";
                    toast.style.transition = "opacity 0.5s, transform 0.5s";
                    setTimeout(() => {
                        toast.remove();
                        if (container.children.length === 0) {
                            container.style.display = "none";
                        }
                    }, 500);
                }, duration);
            }
        }
    };
    
    writeNext();
};

/**
 * Applique le positionnement au toast
 */
function applyPosition(element, position) {
    element.classList.remove('top-4', 'bottom-4', 'left-1/2', 'right-4', 'left-4', 'transform', '-translate-x-1/2', '-translate-y-1/2');
    
    element.style.top = '';
    element.style.bottom = '';
    element.style.left = '';
    element.style.right = '';
    element.style.transform = '';
    element.style.position = '';
    
    if (typeof position === 'object') {
        element.style.position = 'fixed';
        
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
        switch(position) {
            case 'top-left':
                element.classList.add('top-4', 'left-4');
                break;
            case 'top-center':
                element.classList.add('top-4', 'left-1/2', 'transform', '-translate-x-1/2');
                break;
            case 'top-right':
                element.classList.add('top-4', 'right-4');
                break;
            case 'bottom-left':
                element.classList.add('bottom-4', 'left-4');
                break;
            case 'bottom-center':
                element.classList.add('bottom-4', 'left-1/2', 'transform', '-translate-x-1/2');
                break;
            case 'bottom-right':
                element.classList.add('bottom-4', 'right-4');
                break;
            case 'center':
                element.classList.add('top-1/2', 'left-1/2', 'transform', '-translate-x-1/2', '-translate-y-1/2');
                break;
            default:
                element.classList.add('top-4', 'left-1/2', 'transform', '-translate-x-1/2');
        }
    }
}

