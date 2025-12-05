document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".wishlist-transfer-btn").forEach((btn) => {
        if (btn.dataset.jsBound === "true") return;
        btn.dataset.jsBound = "true";

        btn.addEventListener("click", async () => {
            try {
                // Charger celliers
                const response = await fetch("/api/celliers");

                if (!response.ok) {
                    showToast(
                        "Erreur lors du chargement des celliers",
                        "error"
                    );
                    return;
                }

                const celliers = await response.json();

                if (!celliers || !Array.isArray(celliers) || !celliers.length) {
                    showToast("Aucun cellier disponible", "error");
                    return;
                }

                // Créer une modal pour sélectionner le cellier
                showCellierSelectionModal(celliers, (selectedCellierId) => {
                    if (!selectedCellierId) return;

                    // FORM DATA
                    const formData = new FormData();
                    formData.append("cellier_id", selectedCellierId);

                    fetch(btn.dataset.url, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                            Accept: "application/json",
                        },
                        body: formData,
                    })
                        .then(async (res) => {
                            let data = {};

                            try {
                                data = await res.json();
                            } catch (e) {
                                console.error("Erreur parsing JSON:", e);
                                showToast("Erreur lors du transfert", "error");
                                return;
                            }

                            if (res.ok && data.success) {
                                showToast(
                                    data.message || "Transfert réussi",
                                    "success"
                                );
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                showToast(
                                    data.message || "Erreur lors du transfert",
                                    "error"
                                );
                            }
                        })
                        .catch((error) => {
                            console.error("Erreur:", error);
                            showToast("Erreur réseau", "error");
                        });
                });
            } catch (error) {
                console.error("Erreur:", error);
                showToast("Erreur réseau", "error");
            }
        });
    });

    /**
     * Affiche une modal pour sélectionner un cellier
     */
    function showCellierSelectionModal(celliers, onSelect) {
        // Créer l'overlay
        const overlay = document.createElement("div");
        overlay.className =
            "fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4";
        overlay.id = "cellier-selection-modal";

        // Créer la modal
        const modal = document.createElement("div");
        modal.className = "bg-white rounded-lg shadow-xl max-w-md w-full p-6";

        modal.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Transférer vers quel cellier ?</h3>
                <button class="cellier-modal-close text-gray-400 hover:text-gray-600 transition-colors" aria-label="Fermer">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="space-y-2 overflow-y-auto max-h-[80vh]">
                ${celliers
                    .map(
                        (cellier) => `
                    <button 
                        class="cellier-option w-full text-left px-4 py-3 rounded-lg border border-gray-200 hover:border-primary hover:bg-primary/5 transition-all"
                        data-cellier-id="${cellier.id}"
                    >
                        <div class="font-medium text-gray-900">${cellier.nom}</div>
                    </button>
                `
                    )
                    .join("")}
            </div>
        `;

        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        // Fonction pour fermer la modal
        const closeModal = () => {
            overlay.remove();
        };

        // Gérer la fermeture
        modal
            .querySelector(".cellier-modal-close")
            .addEventListener("click", closeModal);
        overlay.addEventListener("click", (e) => {
            if (e.target === overlay) {
                closeModal();
            }
        });

        // Gérer la sélection
        modal.querySelectorAll(".cellier-option").forEach((option) => {
            option.addEventListener("click", () => {
                const cellierId = option.dataset.cellierId;
                closeModal();
                onSelect(cellierId);
            });
        });

        // Fermer avec Échap
        const handleEscape = (e) => {
            if (e.key === "Escape") {
                closeModal();
                document.removeEventListener("keydown", handleEscape);
            }
        };
        document.addEventListener("keydown", handleEscape);
    }
});
