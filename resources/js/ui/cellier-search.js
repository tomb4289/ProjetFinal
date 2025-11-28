// Recherche + filtres dans le CELLER

const cellarSearchBar = document.getElementById("cellar-search-bar");
const cellarContainer = document.getElementById("cellarBottlesContainer");

const cellarSearchInput = document.getElementById("cellarSearchInput");
const cellarPaysFilter = document.getElementById("cellarPaysFilter");
const cellarTypeFilter = document.getElementById("cellarTypeFilter");
const cellarMillesimeFilter = document.getElementById("cellarMillesimeFilter");

// Panneau de filtres (UI façon catalogue)
const cellarSortOptionsBtn = document.getElementById("cellarSortOptionsBtn");
const cellarFiltersContainer = document.getElementById(
    "cellarFiltersContainer"
);
const cellarFiltersOverlay = document.getElementById("cellarFiltersOverlay");
const cellarDragHandle = document.getElementById("cellarDragHandle");
const cellarResetFiltersBtn = document.getElementById("resetCellarFilters");
const closeCellarFiltersBtn = document.getElementById("closeCellarFilters");

if (cellarSearchBar && cellarContainer) {
    console.log("cellier-search.js chargé ✅");

    const baseSearchUrl = cellarSearchBar.dataset.searchUrl;
    const currentSort = cellarSearchBar.dataset.sort || "nom";
    const currentDirection = cellarSearchBar.dataset.direction || "asc";

    // --- TOGGLE PANNEAU FILTRES (simple : juste hidden / pas hidden) ---
    function openCellarFilters() {
        if (cellarFiltersOverlay)
            cellarFiltersOverlay.classList.remove("hidden");
        if (cellarFiltersContainer)
            cellarFiltersContainer.classList.remove("hidden");
    }

    function closeCellarFilters() {
        if (cellarFiltersOverlay) cellarFiltersOverlay.classList.add("hidden");
        if (cellarFiltersContainer)
            cellarFiltersContainer.classList.add("hidden");
    }

    if (cellarSortOptionsBtn) {
        cellarSortOptionsBtn.addEventListener("click", openCellarFilters);
    }
    if (cellarDragHandle) {
        cellarDragHandle.addEventListener("click", closeCellarFilters);
    }
    if (cellarFiltersOverlay) {
        cellarFiltersOverlay.addEventListener("click", closeCellarFilters);
    }
    if (closeCellarFiltersBtn) {
        closeCellarFiltersBtn.addEventListener("click", closeCellarFilters);
    }

    // --- DEBOUNCE ---
    function debounce(fn, delay = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), delay);
        };
    }

    // --- FETCH DES BOUTEILLES DU CELLIER ---
    function fetchCellar(url) {
        const params = new URLSearchParams({
            nom: cellarSearchInput ? cellarSearchInput.value : "",
            pays: cellarPaysFilter ? cellarPaysFilter.value : "",
            type: cellarTypeFilter ? cellarTypeFilter.value : "",
            millesime: cellarMillesimeFilter ? cellarMillesimeFilter.value : "",
            sort: currentSort,
            direction: currentDirection,
        });

        const baseUrl = url || baseSearchUrl;
        const finalUrl = baseUrl.includes("?")
            ? `${baseUrl}&${params.toString()}`
            : `${baseUrl}?${params.toString()}`;

        fetch(finalUrl)
            .then((res) => res.json())
            .then((data) => {
                cellarContainer.innerHTML = data.html;
                bindCellarPaginationLinks();
            });
    }

    const debouncedFetchCellar = debounce(fetchCellar, 300);

    // --- Écouteurs sur les champs de recherche / filtres ---
    if (cellarSearchInput) {
        cellarSearchInput.addEventListener("input", () =>
            debouncedFetchCellar()
        );
    }
    if (cellarPaysFilter) {
        cellarPaysFilter.addEventListener("input", () =>
            debouncedFetchCellar()
        );
    }
    if (cellarTypeFilter) {
        cellarTypeFilter.addEventListener("input", () =>
            debouncedFetchCellar()
        );
    }
    if (cellarMillesimeFilter) {
        cellarMillesimeFilter.addEventListener("input", () =>
            debouncedFetchCellar()
        );
    }

    // --- Reset des filtres ---
    function resetCellarFilters() {
        if (cellarSearchInput) cellarSearchInput.value = "";
        if (cellarPaysFilter) cellarPaysFilter.value = "";
        if (cellarTypeFilter) cellarTypeFilter.value = "";
        if (cellarMillesimeFilter) cellarMillesimeFilter.value = "";
        fetchCellar();
    }

    if (cellarResetFiltersBtn) {
        cellarResetFiltersBtn.addEventListener("click", resetCellarFilters);
    }

    // --- Pagination AJAX (si un jour vous ajoutez page= dans l'URL) ---
    function bindCellarPaginationLinks() {
        const links = cellarContainer.querySelectorAll("a[href*='page=']");
        links.forEach((link) => {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                fetchCellar(this.href);
            });
        });
    }

    // Premier binding
    bindCellarPaginationLinks();
}
