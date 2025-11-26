// Recuperation des éléments du DOM
const searchInput = document.getElementById("searchInput");
const paysFilter = document.getElementById("paysFilter");
const typeFilter = document.getElementById("typeFilter");
const millesimeFilter = document.getElementById("millesimeFilter");
const container = document.getElementById("catalogueContainer");
const priceMinFilter = document.getElementById("priceMin");
const priceMaxFilter = document.getElementById("priceMax");
const sortFilter = document.getElementById("sortFilter");
const resetFiltersBtn = document.getElementById("resetFiltersBtn");

// Tooggle des options de tri
const sortOptionsBtn = document.getElementById("sortOptionsBtn");
const filtersContainer = document.getElementById("filtersContainer");
const filtersOverlay = document.getElementById("filtersOverlay");
const dragHandle = document.getElementById("dragHandle");

const suggestionsBox = document.getElementById("suggestionsBox");
let suggestionTimeout = null;

// Fonction de reset des filtres
function resetFilters() {
    paysFilter.value = "";
    typeFilter.value = "";
    millesimeFilter.value = "";
    priceMinFilter.value = "";
    priceMaxFilter.value = "";
    sortFilter.value = "";
    searchInput.value = "";
    fetchCatalogue();
}

// Fonction de toggle des options de tri
function toggleSortOptions() {
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

// Evenements pour toggle les options de tri
sortOptionsBtn.addEventListener("click", toggleSortOptions);
filtersOverlay.addEventListener("click", toggleSortOptions);
dragHandle.addEventListener("click", toggleSortOptions);

// Debouce, pour limiter la fréquence des appels AJAX lors de la saisie rapide. Ajoute un delais avant de fetch.
function debounce(fn, delay = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

// Fonction principale pour fetch le catalogue avec les filtres
function fetchCatalogue(url = "/catalogue/search") {
    // deconstruction des values du filtre sortBy
    const [sortBy, sortDirection] = sortFilter.value.split("-");

    const params = new URLSearchParams({
        search: searchInput.value,
        pays: paysFilter.value,
        type: typeFilter.value,
        millesime: millesimeFilter.value,
        prix_min: priceMinFilter.value,
        prix_max: priceMaxFilter.value,
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
            container.innerHTML = data.html;

            // Re-bind pagination links for AJAX
            bindPaginationLinks();
        });
}

// Rendu des suggestions
function renderSuggestions(items) {
    // Si pas de suggestions, cacher la boite
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
    // Afficher les suggestions
    suggestionsBox.innerHTML = html;
    // Afficher la boîte de suggestions
    suggestionsBox.classList.remove("hidden");

    // Clic sur une suggestion
    document.querySelectorAll(".suggestion-item").forEach((el) => {
        el.addEventListener("click", () => {
            // Mettre à jour l'input de recherche selon le clic
            searchInput.value = el.dataset.value;
            suggestionsBox.classList.add("hidden");
            debouncedFetch(); // relance ton AJAX catalogue
        });
    });
}

// Debounced fetch pour le catalogue
const debouncedFetch = debounce(fetchCatalogue, 300);

// Recherche
searchInput.addEventListener("input", () => debouncedFetch());

millesimeFilter.addEventListener("change", () => debouncedFetch());

// Filtres
paysFilter.addEventListener("change", () => debouncedFetch());
typeFilter.addEventListener("change", () => debouncedFetch());

// Filtres de prix
priceMinFilter.addEventListener("input", () => debouncedFetch());
priceMaxFilter.addEventListener("input", () => debouncedFetch());

sortFilter.addEventListener("change", () => debouncedFetch());

// Reset des filtres
resetFiltersBtn.addEventListener("click", resetFilters);

searchInput.addEventListener("input", function () {
    const query = this.value.trim();

    // Si la requête est trop courte, cacher les suggestions
    if (query.length < 2) {
        suggestionsBox.classList.add("hidden");
        return;
    }
    // Effacer le timeout précédent pour debounce des suggestions
    clearTimeout(suggestionTimeout);

    // Anti spam
    suggestionTimeout = setTimeout(() => {
        fetch(`/catalogue/suggest?search=${encodeURIComponent(query)}`)
            .then((res) => res.json())
            .then((items) => {
                renderSuggestions(items);
            });
    }, 150);
});

// Clic en dehors de la boite de suggestions pour la cacher
document.addEventListener("click", (e) => {
    if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
        suggestionsBox.classList.add("hidden");
    }
});

// AJAX Pagination
function bindPaginationLinks() {
    const links = container.querySelectorAll("a[href*='page=']");
    links.forEach((link) => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            fetchCatalogue(this.href);
        });
    });
}

// Lier les liens de pagination au chargement initial
bindPaginationLinks();
