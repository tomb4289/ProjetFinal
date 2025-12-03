// Récupération des éléments du DOM
const searchInput = document.getElementById("searchInput");
const paysFilter = document.getElementById("paysFilter");
const typeFilter = document.getElementById("typeFilter");
const millesimeFilter = document.getElementById("millesimeFilter");
const container = document.getElementById("catalogueContainer");
const priceMinFilter = document.getElementById("priceMin");
const priceMaxFilter = document.getElementById("priceMax");
const sortFilter = document.getElementById("sortFilter");
const resetFiltersBtn = document.getElementById("resetFiltersBtn");
const applyFiltersBtn = document.getElementById("applyFiltersBtn");

// Toggle des options de tri / filtres
const sortOptionsBtn = document.getElementById("sortOptionsBtn");
const filtersContainer = document.getElementById("filtersContainer");
const filtersOverlay = document.getElementById("filtersOverlay");
const dragHandle = document.getElementById("dragHandle");

const suggestionsBox = document.getElementById("suggestionsBox");
let suggestionTimeout = null;

// Fonction de reset des filtres
function resetFilters() {
    if (
        !paysFilter ||
        !typeFilter ||
        !millesimeFilter ||
        !priceMinFilter ||
        !priceMaxFilter ||
        !sortFilter ||
        !searchInput
    ) {
        return;
    }

    paysFilter.value = "";
    typeFilter.value = "";
    millesimeFilter.value = "";
    priceMinFilter.value = "";
    priceMaxFilter.value = "";
    sortFilter.value = "date_import-desc";
    searchInput.value = "";

    // On relance le catalogue sans aucun filtre
    fetchCatalogue();
}

// Fonction de toggle des options de tri / filtres (bottom sheet)
function toggleSortOptions() {
    if (!filtersContainer || !filtersOverlay) return;

    if (filtersContainer.classList.contains("hidden")) {
        filtersOverlay.classList.remove("hidden");
        filtersContainer.classList.remove("hidden");

        setTimeout(() => {
            filtersOverlay.classList.add("opacity-50");
            filtersContainer.classList.remove("translate-y-[100%]");
            filtersContainer.classList.add("translate-y-0");
        }, 10);
    } else {
        filtersContainer.classList.remove("translate-y-0");
        filtersContainer.classList.add("translate-y-[100%]");
        filtersOverlay.classList.remove("opacity-50");

        setTimeout(() => {
            filtersOverlay.classList.add("hidden");
            filtersContainer.classList.add("hidden");
        }, 500);
    }
}

// Événements pour ouvrir / fermer le panneau
if (sortOptionsBtn) {
    sortOptionsBtn.addEventListener("click", toggleSortOptions);
}
if (filtersOverlay) {
    filtersOverlay.addEventListener("click", toggleSortOptions);
}
if (dragHandle) {
    dragHandle.addEventListener("click", toggleSortOptions);
}

// Debounce pour limiter la fréquence des appels AJAX lors de la saisie
function debounce(fn, delay = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

// Fonction principale pour fetch le catalogue avec les filtres
function fetchCatalogue(url = "/catalogue/search") {
    if (!container || !searchInput || !sortFilter) return;

    let sortBy = "";
    let sortDirection = "";

    if (sortFilter.value) {
        const parts = sortFilter.value.split("-");
        sortBy = parts[0] || "";
        sortDirection = parts[1] || "";
    }

    const params = new URLSearchParams({
        search: searchInput.value || "",
        pays: paysFilter?.value || "",
        type: typeFilter?.value || "",
        millesime: millesimeFilter?.value || "",
        prix_min: priceMinFilter?.value || "",
        prix_max: priceMaxFilter?.value || "",
        sort_by: sortBy,
        sort_direction: sortDirection,
    });

    // Construire l'URL finale avec les paramètres de requête
    const finalUrl = url.includes("?")
        ? `${url}&${params.toString()}`
        : `${url}?${params.toString()}`;

    // Faire la requête AJAX
    fetch(finalUrl)
        .then((res) => res.json())
        .then((data) => {
            if (container) {
                container.innerHTML = data.html;
                window.dispatchEvent(new CustomEvent("catalogueReloaded"));

                // Re-bind pagination links pour AJAX
                bindPaginationLinks();

                // Rebind les boutons wishlist
            window.dispatchEvent(new CustomEvent("catalogueReloaded"));
        
            }
        })
        .catch((err) => {
            console.error("Erreur lors du fetch catalogue :", err);
        });
}

// Rendu des suggestions (auto-complétion)
function renderSuggestions(items) {
    if (!suggestionsBox) return;

    // Si pas de suggestions, cacher la boîte
    if (items.length === 0) {
        suggestionsBox.classList.add("hidden");
        return;
    }

    let html = "";
    items.forEach((item) => {
        html += `
            <div 
                class="px-3 py-2 cursor-pointer hover:bg-gray-100 suggestion-item"
                data-value="${item.nom}">
                ${item.nom}
            </div>`;
    });

    suggestionsBox.innerHTML = html;
    suggestionsBox.classList.remove("hidden");

    // Clic sur une suggestion
    document.querySelectorAll(".suggestion-item").forEach((el) => {
        el.addEventListener("click", () => {
            searchInput.value = el.dataset.value;
            suggestionsBox.classList.add("hidden");
            debouncedFetch(); // relance le catalogue avec le texte choisi
        });
    });
}

// Debounced fetch pour la recherche texte
const debouncedFetch = debounce(fetchCatalogue, 300);

// Recherche texte (input)
if (searchInput) {
    searchInput.addEventListener("input", () => debouncedFetch());

    searchInput.addEventListener("input", function () {
        const query = this.value.trim();

        // Si la requête est trop courte, cacher les suggestions
        if (query.length < 2) {
            if (suggestionsBox) {
                suggestionsBox.classList.add("hidden");
            }
            return;
        }

        clearTimeout(suggestionTimeout);

        // Auto-complétion (suggestions)
        suggestionTimeout = setTimeout(() => {
            fetch(`/catalogue/suggest?search=${encodeURIComponent(query)}`)
                .then((res) => res.json())
                .then((items) => {
                    renderSuggestions(items);
                });
        }, 150);
    });
}

// on enlève les fetch automatiques sur changement de filtres,
// et on passe par le bouton "Appliquer les filtres"

// Filtres (on ne fait plus de fetch ici)
// if (paysFilter)      paysFilter.addEventListener("change", () => debouncedFetch());
// if (typeFilter)      typeFilter.addEventListener("change", () => debouncedFetch());
// if (millesimeFilter) millesimeFilter.addEventListener("change", () => debouncedFetch());
// if (priceMinFilter)  priceMinFilter.addEventListener("input", () => debouncedFetch());
// if (priceMaxFilter)  priceMaxFilter.addEventListener("input", () => debouncedFetch());
// if (sortFilter)      sortFilter.addEventListener("change", () => debouncedFetch());

//  bouton "Appliquer les filtres"
if (applyFiltersBtn) {
    applyFiltersBtn.addEventListener("click", () => {
        fetchCatalogue(); // on applique tous les filtres en même temps
        toggleSortOptions(); // on ferme le panneau après application
    });
}

// Reset des filtres
if (resetFiltersBtn) {
    resetFiltersBtn.addEventListener("click", resetFilters);
}

// Clic en dehors de la boîte de suggestions pour la cacher
if (searchInput && suggestionsBox) {
    document.addEventListener("click", (e) => {
        if (
            !searchInput.contains(e.target) &&
            !suggestionsBox.contains(e.target)
        ) {
            suggestionsBox.classList.add("hidden");
        }
    });
}

// AJAX Pagination
function bindPaginationLinks() {
    if (!container) return;
    const links = container.querySelectorAll("a[href*='page=']");
    links.forEach((link) => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            fetchCatalogue(this.href);
        });
    });
}

// Lier les liens de pagination au chargement initial
if (container) {
    bindPaginationLinks();
}
