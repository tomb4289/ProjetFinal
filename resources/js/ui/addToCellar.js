document.addEventListener("DOMContentLoaded", () => {
    const addToCellarBtns = document.querySelectorAll(".add-to-cellar-btn");
    const closeBtn = document.getElementById("closeAddWine");
    const cellarSheet = document.getElementById("addWineBtnContainer");

    let preloaded = null;
    let activeBottleId = null;
    let activeQuantity = 1;

    // Charge les celliers une seule fois
    preloadCellars();

    async function preloadCellars() {
        preloaded = await getCellars();
    }

    closeBtn.addEventListener("click", closeSheet);

    // Ouvrir le sheet quand on clique sur Ajouter
    addToCellarBtns.forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();

            const form = btn.closest("form");

            activeBottleId = form.querySelector(
                'input[name="bottle_id"]'
            ).value;
            activeQuantity =
                parseInt(form.querySelector('input[name="quantity"]').value) ||
                1;

            openSheet();
        });
    });

    async function openSheet() {
        cellarSheet.classList.remove("translate-y-full");
        populateCellarOptions(preloaded);
    }

    function closeSheet() {
        cellarSheet.classList.add("translate-y-full");
    }

    async function getCellars() {
        const response = await fetch("/api/celliers");
        return response.json();
    }

    function populateCellarOptions(cellars) {
        const cellarList = document.getElementById("cellar-list");
        cellarList.innerHTML = "";

        cellars.forEach((cellar) => {
            cellarList.innerHTML += `
                <a 
                    href="#"
                    class="cellar-box block p-3 bg-card rounded-lg shadow-md border border-border-base hover:shadow-sm cursor-pointer"
                    data-cellar-id="${cellar.id}"
                    data-bottle-id="${activeBottleId}"
                    data-quantity="${activeQuantity}"
                >
                    <div class="flex justify-between">
                        <div class="flex flex-col gap-1">
                            <h2 class="text-2xl font-semibold">${
                                cellar.nom
                            }</h2>
                            ${
                                cellar.bouteilles_count == 0
                                    ? `<p class="text-gray-400 italic">Aucune bouteille</p>`
                                    : `${cellar.bouteilles_count} Bouteille${
                                          cellar.bouteilles_count > 1 ? "s" : ""
                                      }`
                            }
                        </div>
                    </div>
                </a>
            `;
        });
    }

    // Clique sur un cellier dans le sheet
    document.addEventListener("click", async (e) => {
        const box = e.target.closest(".cellar-box");
        if (!box) return;

        e.preventDefault();

        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;

        const cellarId = box.dataset.cellarId;
        const bottleId = box.dataset.bottleId;
        const quantity = box.dataset.quantity;

        await fetch("/api/ajout/cellier", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                cellar_id: cellarId,
                bottle_id: bottleId,
                quantity: quantity,
            }),
        });

        closeSheet();
    });
});
