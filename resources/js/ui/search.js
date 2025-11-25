const searchInput = document.getElementById("searchInput");
const paysFilter = document.getElementById("paysFilter");
const typeFilter = document.getElementById("typeFilter");
const container = document.getElementById("catalogueContainer");

// Debouce, pour limiter la fréquence des appels AJAX lors de la saisie rapide. Ajoute un delais avant de fetch.
function debounce(fn, delay = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

function fetchCatalogue(url = "/catalogue/search") {
    const params = new URLSearchParams({
        search: searchInput.value,
        pays: paysFilter.value,
        type: typeFilter.value,
    });

    // Construire l'URL finale avec les paramètres de requête
    const finalUrl = url.includes("?")
        ? `${url}&${params.toString()}`
        : `${url}?${params.toString()}`;

    fetch(finalUrl)
        .then((res) => res.json())
        .then((data) => {
            container.innerHTML = data.html;

            // Re-bind pagination links for AJAX
            bindPaginationLinks();
        });
}

const debouncedFetch = debounce(fetchCatalogue, 300);

// Search input
searchInput.addEventListener("input", () => debouncedFetch());

// Filters
paysFilter.addEventListener("change", () => debouncedFetch());
typeFilter.addEventListener("change", () => debouncedFetch());

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

// Bind on load
bindPaginationLinks();
