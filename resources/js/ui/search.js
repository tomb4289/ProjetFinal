// Champs du DOM
const searchInput = document.getElementById("searchInput");
const paysFilter = document.getElementById("paysFilter");
const typeFilter = document.getElementById("typeFilter");
const regionFilter = document.getElementById("regionFilter");
const millesimeFilter = document.getElementById("millesimeFilter");
const priceMinFilter = document.getElementById("priceMin");
const priceMaxFilter = document.getElementById("priceMax");
const sortFilter = document.getElementById("sortFilter");

// Boutons
const resetFiltersBtn = document.getElementById("resetFiltersBtn");
const applyFiltersBtn = document.getElementById("applyFiltersBtn");
const searchWrapper = document.querySelector("[data-container]");

// Bottom sheet filtres
const sortOptionsBtn = document.getElementById("sortOptionsBtn");
const filtersContainer = document.getElementById("filtersContainer");
const filtersOverlay = document.getElementById("filtersOverlay");
const dragHandle = document.getElementById("dragHandle");

// Conteneur des cartes (catalogue ou liste d’achat)
const containerId = searchWrapper?.dataset.container || "catalogueContainer";
const container = document.getElementById(containerId);

// URL API (catalogue ou liste d’achat)
const baseUrl = searchWrapper?.dataset.url || "/catalogue/search";
const suggestionUrl =
    searchWrapper?.dataset.suggestionUrl || "/catalogue/suggest";

// Suggestions
const suggestionsBox = document.getElementById("suggestionsBox");
let suggestionTimeout = null;

let isListeAchat = false;
if (containerId === "listeAchatContainer") {
    isListeAchat = true;
}

// Reset des filtres
function resetFilters() {
    if (searchInput) searchInput.value = "";
    if (paysFilter) paysFilter.value = "";
    if (typeFilter) typeFilter.value = "";
    if (regionFilter) regionFilter.value = "";
    if (millesimeFilter) millesimeFilter.value = "";
    if (priceMinFilter) priceMinFilter.value = "";
    if (priceMaxFilter) priceMaxFilter.value = "";
    if (sortFilter)
        sortFilter.value = isListeAchat
            ? "date_ajout-desc"
            : "date_import-desc";
    fetchCatalogue(); // Call sans arg → baseUrl utilisé
}

// Ouverture / fermeture panel
function toggleSortOptions() {
    if (!filtersContainer || !filtersOverlay) return;
    const isHidden = filtersContainer.classList.contains("hidden");

    if (isHidden) {
        filtersOverlay.classList.remove("hidden");
        filtersContainer.classList.remove("hidden");
        setTimeout(() => {
            filtersOverlay.classList.add("opacity-50");
            filtersContainer.classList.remove("translate-y-[100%]");
        }, 10);
    } else {
        filtersContainer.classList.add("translate-y-[100%]");
        filtersOverlay.classList.remove("opacity-50");
        setTimeout(() => {
            filtersOverlay.classList.add("hidden");
            filtersContainer.classList.add("hidden");
        }, 500);
    }
}

if (sortOptionsBtn) sortOptionsBtn.addEventListener("click", toggleSortOptions);
if (filtersOverlay) filtersOverlay.addEventListener("click", toggleSortOptions);
if (dragHandle) dragHandle.addEventListener("click", toggleSortOptions);

// Debounce
function debounce(fn, delay = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

// Fetch catalogue / liste d’achat
function fetchCatalogue(customUrl = baseUrl) {
    // customUrl = lien de pagination OU baseUrl
    if (!container) return;

    // Tri
    let sortBy = "";
    let sortDirection = "";

    if (sortFilter && sortFilter.value) {
        const [field, dir] = sortFilter.value.split("-");
        sortBy = field || "";
        sortDirection = dir || "";
    }

    // Params de la requête
    const params = new URLSearchParams({
        search: searchInput?.value || "",
        pays: paysFilter?.value || "",
        type: typeFilter?.value || "",
        region: regionFilter?.value || "",
        millesime: millesimeFilter?.value || "",
        prix_min: priceMinFilter?.value || "",
        prix_max: priceMaxFilter?.value || "",
        sort_by: sortBy,
        sort_direction: sortDirection,
    });

    // URL finale
    const finalUrl = customUrl.includes("?")
        ? `${customUrl}&${params.toString()}`
        : `${customUrl}?${params.toString()}`;

    // Requête AJAX
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

            // Masquer l'overlay de chargement après le chargement AJAX
            const overlay = document.getElementById("page-loading-overlay");
            if (overlay) {
                overlay.classList.add("hidden");
                overlay.setAttribute("aria-hidden", "true");
                overlay.innerHTML = "";
            }
        })
        .catch((err) => {
            console.error("Erreur lors du fetch catalogue :", err);

            // Masquer l'overlay même en cas d'erreur
            const overlay = document.getElementById("page-loading-overlay");
            if (overlay) {
                overlay.classList.add("hidden");
                overlay.setAttribute("aria-hidden", "true");
                overlay.innerHTML = "";
            }
        });
}

// Suggestions recherche
function renderSuggestions(items) {
    if (!suggestionsBox) return;

    if (!items.length) {
        suggestionsBox.classList.add("hidden");
        return;
    }

    suggestionsBox.innerHTML = items
        .map(
            (item) => `
        <div class="px-3 py-2 cursor-pointer hover:bg-gray-100 suggestion-item"
             data-value="${item.nom}">
            ${item.nom}
        </div>`
        )
        .join("");

    suggestionsBox.classList.remove("hidden");

    document.querySelectorAll(".suggestion-item").forEach((el) => {
        el.addEventListener("click", () => {
            searchInput.value = el.dataset.value;
            suggestionsBox.classList.add("hidden");
            debouncedFetch(); // Relance fetchCatalogue()
        });
    });
}

// DebouncedFetch sans param → fetchCatalogue() utilisera baseUrl
const debouncedFetch = debounce(() => fetchCatalogue(), 300);

// Écoute entrée recherche
if (searchInput) {
    searchInput.addEventListener("input", () => debouncedFetch());

    searchInput.addEventListener("input", (e) => {
        const query = e.target.value.trim();
        if (query.length < 1) {
            if (suggestionsBox) suggestionsBox.classList.add("hidden");
            return;
        }

        clearTimeout(suggestionTimeout);
        suggestionTimeout = setTimeout(() => {
            fetch(`${suggestionUrl}?search=${encodeURIComponent(query)}`)
                .then((res) => res.json())
                .then((items) => renderSuggestions(items));
        }, 150);
    });
}

// Boutons appliquer / reset
if (applyFiltersBtn) {
    applyFiltersBtn.addEventListener("click", () => {
        fetchCatalogue(); // BaseUrl + filtres
        toggleSortOptions(); // Ferme le panneau
    });
}

if (resetFiltersBtn) {
    resetFiltersBtn.addEventListener("click", resetFilters);
}

// Cacher suggestions au clic
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

// Pagination AJAX
function bindPaginationLinks() {
    if (!container) return;
    const links = container.querySelectorAll("a[href*='page=']");
    links.forEach((link) => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            fetchCatalogue(link.href); // Ici on passe l’URL de la page
        });
    });
}

// Bind initial
if (container) {
    bindPaginationLinks();
}
