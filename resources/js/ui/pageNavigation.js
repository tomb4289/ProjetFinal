/**
 * Gestion du chargement de page lors de la navigation
 * 
 * Affiche un overlay de chargement avec spinner lors de la navigation entre les pages
 * pour remplacer l'état de "stuck loading" par une page grise avec spinner.
 */

const overlay = document.getElementById("page-loading-overlay");

if (overlay) {
    // Fonction pour afficher l'overlay
    function showLoading() {
        // Utiliser le template loading-spinner-template si disponible
        const template = document.getElementById("loading-spinner-template");
        if (template && overlay.children.length === 0) {
            const clone = template.content.cloneNode(true);
            overlay.appendChild(clone);
        } else if (overlay.children.length === 0) {
            // Fallback si le template n'existe pas
            overlay.innerHTML = `
                <div class="flex items-center justify-center w-full h-full min-h-full">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                </div>
            `;
        }
        overlay.classList.remove("hidden");
        overlay.setAttribute("aria-hidden", "false");
    }

    // Fonction pour masquer l'overlay
    function hideLoading() {
        overlay.classList.add("hidden");
        overlay.setAttribute("aria-hidden", "true");
        // Nettoyer le contenu pour permettre la réutilisation du template
        overlay.innerHTML = "";
    }

    // Fonction pour mettre à jour l'état actif des nav items
    function updateActiveNavItem(clickedLink) {
        // Trouver tous les nav items dans la navigation
        const nav = document.querySelector('nav[aria-label="Menu principal"]');
        if (!nav) return;

        // Vérifier si le lien cliqué est dans la navigation
        if (!nav.contains(clickedLink)) return;

        // Retirer l'état actif de tous les nav items
        nav.querySelectorAll('a').forEach(navLink => {
            navLink.setAttribute('aria-current', 'false');
            
            // Trouver l'icône SVG (les icônes lucide sont des SVG)
            const icon = navLink.querySelector('svg');
            // Trouver le label (le span avec le texte)
            const label = navLink.querySelector('span.text-sm');
            
            if (icon) {
                // Retirer stroke-primary et s'assurer que stroke-icon est présent
                icon.classList.remove('stroke-primary');
                if (!icon.classList.contains('stroke-icon')) {
                    icon.classList.add('stroke-icon');
                }
            }
            
            if (label) {
                // Retirer text-primary et s'assurer que text-icon est présent
                label.classList.remove('text-primary');
                if (!label.classList.contains('text-icon')) {
                    label.classList.add('text-icon');
                }
            }
        });

        // Ajouter l'état actif au lien cliqué
        clickedLink.setAttribute('aria-current', 'page');
        const icon = clickedLink.querySelector('svg');
        const label = clickedLink.querySelector('span.text-sm');
        
        if (icon) {
            icon.classList.remove('stroke-icon');
            icon.classList.add('stroke-primary');
        }
        
        if (label) {
            label.classList.remove('text-icon');
            label.classList.add('text-primary');
        }
    }

    // Intercepter tous les clics sur les liens de navigation
    document.addEventListener("click", function (e) {
        const link = e.target.closest("a");
        
        // Vérifier si c'est un lien de navigation valide
        if (!link) return;
        
        const href = link.getAttribute("href");
        
        // Ignorer les liens externes, les ancres, les liens avec target="_blank", et les liens JavaScript
        if (
            !href ||
            link.hostname !== window.location.hostname ||
            href.startsWith("javascript:") ||
            href === "#" ||
            href.startsWith("#") ||
            link.target === "_blank" ||
            link.hasAttribute("download") ||
            href.startsWith("mailto:") ||
            href.startsWith("tel:")
        ) {
            return;
        }

        // Ignorer les liens qui ont des attributs spéciaux (comme les formulaires AJAX)
        if (link.hasAttribute("data-no-loading") || link.closest("form")) {
            return;
        }

        // Mettre à jour l'état actif du nav item avant de charger
        updateActiveNavItem(link);

        // Afficher l'overlay de chargement immédiatement
        showLoading();
    });

    // Afficher l'overlay lors du beforeunload (quand la page se décharge)
    window.addEventListener("beforeunload", function () {
        showLoading();
    });

    // Masquer l'overlay quand la page est complètement chargée
    window.addEventListener("load", function () {
        hideLoading();
    });

    // Masquer l'overlay si la page est déjà chargée (pour éviter qu'il reste visible)
    if (document.readyState === "complete") {
        hideLoading();
    } else {
        // Masquer l'overlay quand la page est complètement chargée
        window.addEventListener("load", function () {
            hideLoading();
        });
    }
}
