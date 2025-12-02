// resources/js/ui/search-cellier.js

document.addEventListener("DOMContentLoaded", () => {
    // On cible le composant de filtres du CELLIER
    const root = document.querySelector(
        "[data-search-url][data-target-container='cellarBottlesContainer']"
    );
    if (!root) return; 

    console.log("search-cellier.js chargé");



    const searchInput      = document.getElementById("searchInput");
    const paysFilter       = document.getElementById("paysFilter");
    const typeFilter       = document.getElementById("typeFilter");
    const regionFilter     = document.getElementById("regionFilter");
    const millesimeFilter  = document.getElementById("millesimeFilter");
    const priceMinFilter   = document.getElementById("priceMin");
    const priceMaxFilter   = document.getElementById("priceMax");
    const sortFilter       = document.getElementById("sortFilter");
    const resetFiltersBtn  = document.getElementById("resetFiltersBtn");
    const applyFiltersBtn  = document.getElementById("applyFiltersBtn");

    const sortOptionsBtn   = document.getElementById("sortOptionsBtn");
    const filtersContainer = document.getElementById("filtersContainer");
    const filtersOverlay   = document.getElementById("filtersOverlay");
    const dragHandle       = document.getElementById("dragHandle");

    const baseSearchUrl    = root.dataset.searchUrl;          // route('celliers.search', $cellier)
    const containerId      = root.dataset.targetContainer;    // "cellarBottlesContainer"
    const container        = document.getElementById(containerId);

    if (!baseSearchUrl || !container) {
        console.warn("search-cellier.js : pas d'URL de recherche ou de conteneur.");
        return;
    }

    // -------- Bottom sheet (ouvrir / fermer) --------
    function openFilters() {
        if (!filtersContainer || !filtersOverlay) return;
        filtersOverlay.classList.remove("hidden");
        filtersContainer.classList.remove("hidden");

        requestAnimationFrame(() => {
            filtersOverlay.classList.add("opacity-50");
            filtersContainer.classList.remove("translate-y-[100%]");
            filtersContainer.classList.add("translate-y-0");
        });
    }

    function closeFilters() {
        if (!filtersContainer || !filtersOverlay) return;
        filtersContainer.classList.remove("translate-y-0");
        filtersContainer.classList.add("translate-y-[100%]");
        filtersOverlay.classList.remove("opacity-50");

        setTimeout(() => {
            filtersOverlay.classList.add("hidden");
            filtersContainer.classList.add("hidden");
        }, 300);
    }

    if (sortOptionsBtn) sortOptionsBtn.addEventListener("click", openFilters);
    if (filtersOverlay) filtersOverlay.addEventListener("click", closeFilters);
    if (dragHandle)     dragHandle.addEventListener("click", closeFilters);

    // -------- Debounce --------
    function debounce(fn, delay = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), delay);
        };
    }

    // Traduire "prix-asc", "nom-desc"... pour le backend
    function buildSortForCellar() {
        if (!sortFilter || !sortFilter.value) {
            return { sort: "nom", direction: "asc" };
        }

        const [sortBy, sortDir] = sortFilter.value.split("-"); 
        let sort = "nom";

        switch (sortBy) {
            case "prix":
                sort = "prix";
                break;
            case "millesime":
                sort = "millesime";
                break;
            case "nom":
            default:
                sort = "nom";
        }

        const direction = sortDir === "desc" ? "desc" : "asc";
        return { sort, direction };
    }

    // -------- Appel AJAX vers celliers.search --------
    function fetchCellar(url) {
        if (!container) return;

        const { sort, direction } = buildSortForCellar();

        const params = new URLSearchParams({
            nom:       searchInput?.value || "",
            pays:      paysFilter?.value || "",
            type:      typeFilter?.value || "",
            region:    regionFilter?.value || "",
            millesime: millesimeFilter?.value || "",
            sort,
            direction,
        });

        // filtres de prix si tu les gères dans le controller
        if (priceMinFilter?.value) params.append("prix_min", priceMinFilter.value);
        if (priceMaxFilter?.value) params.append("prix_max", priceMaxFilter.value);

        const baseUrl  = url || baseSearchUrl;
        const finalUrl = baseUrl.includes("?")
            ? `${baseUrl}&${params.toString()}`
            : `${baseUrl}?${params.toString()}`;

        fetch(finalUrl)
            .then((res) => res.json())
            .then((data) => {
                container.innerHTML = data.html;
                bindPaginationLinks(); 
            })
            .catch((err) => console.error("Erreur fetch cellier :", err));
    }

    const debouncedFetch = debounce(fetchCellar, 300);

    // -------- Pagination AJAX (optionnel) --------
    function bindPaginationLinks() {
        const links = container.querySelectorAll("a[href*='page=']");
        links.forEach((link) => {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                fetchCellar(this.href);
            });
        });
    }

    // -------- Écouteurs de filtres --------
    if (searchInput)     searchInput.addEventListener("input", () => debouncedFetch());
    if (paysFilter)      paysFilter.addEventListener("change", () => debouncedFetch());
    if (typeFilter)      typeFilter.addEventListener("change", () => debouncedFetch());
    if (regionFilter)    regionFilter.addEventListener("change", () => debouncedFetch());
    if (millesimeFilter) millesimeFilter.addEventListener("change", () => debouncedFetch());
    if (sortFilter)      sortFilter.addEventListener("change", () => debouncedFetch());
    if (priceMinFilter)  priceMinFilter.addEventListener("input", () => debouncedFetch());
    if (priceMaxFilter)  priceMaxFilter.addEventListener("input", () => debouncedFetch());

    // Bouton "Réinitialiser"
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener("click", () => {
            if (searchInput)     searchInput.value = "";
            if (paysFilter)      paysFilter.value = "";
            if (typeFilter)      typeFilter.value = "";
            if (regionFilter)   regionFilter.value = "";
            if (millesimeFilter) millesimeFilter.value = "";
            if (priceMinFilter)  priceMinFilter.value = "";
            if (priceMaxFilter)  priceMaxFilter.value = "";
            if (sortFilter)      sortFilter.value = "";

            fetchCellar();
        });
    }

    // Bouton "Appliquer les filtres"
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener("click", () => {
            fetchCellar();
            closeFilters();
        });
    }

    // Initialisation 
    bindPaginationLinks();
});
