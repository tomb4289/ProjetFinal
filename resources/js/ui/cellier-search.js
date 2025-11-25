// Recherche dynamique dans un cellier

const cellarSearchInput      = document.getElementById("cellarSearchInput");
const cellarPaysFilter       = document.getElementById("cellarPaysFilter");
const cellarTypeFilter       = document.getElementById("cellarTypeFilter");
const cellarMillesimeFilter  = document.getElementById("cellarMillesimeFilter");
const cellarContainer        = document.getElementById("cellarBottlesContainer");
const cellarSearchBar        = document.getElementById("cellar-search-bar");

// On vérifie que l'on est bien sur la page du cellier
if (
    cellarSearchInput &&
    cellarPaysFilter &&
    cellarTypeFilter &&
    cellarMillesimeFilter &&
    cellarContainer &&
    cellarSearchBar
) {
    const baseSearchUrl    = cellarSearchBar.dataset.searchUrl;   
    const currentSort      = cellarSearchBar.dataset.sort;       
    const currentDirection = cellarSearchBar.dataset.direction;   

    // Même idée que le debounce du catalogue
    function debounceCellar(fn, delay = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), delay);
        };
    }

    function fetchCellar(url) {
        const params = new URLSearchParams({
            nom:       cellarSearchInput.value,
            pays:      cellarPaysFilter.value,
            type:      cellarTypeFilter.value,
            millesime: cellarMillesimeFilter.value,
            sort:      currentSort,
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

    const debouncedCellarFetch = debounceCellar(fetchCellar, 300);

    // Événements sur les champs de recherche
    cellarSearchInput.addEventListener("input", () => debouncedCellarFetch());
    cellarPaysFilter.addEventListener("input", () => debouncedCellarFetch());
    cellarTypeFilter.addEventListener("input", () => debouncedCellarFetch());
    cellarMillesimeFilter.addEventListener("input", () => debouncedCellarFetch());

    // Pagination AJAX dans le cellier (si un jour on ajoute de la pagination)
    function bindCellarPaginationLinks() {
        const links = cellarContainer.querySelectorAll("a[href*='page=']");
        links.forEach((link) => {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                fetchCellar(this.href);
            });
        });
    }

    bindCellarPaginationLinks();
}
