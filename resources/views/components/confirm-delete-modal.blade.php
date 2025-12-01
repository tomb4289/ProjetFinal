{{-- Modal pour la confirmation de suppression --}}
<div id="confirmModal" 
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden"
     role="alertdialog"
     aria-modal="true"
     aria-labelledby="confirmMessage"
     aria-hidden="true">

    <div class="bg-card rounded-lg shadow-xl p-6 w-[90%] max-w-md border border-border-base" role="document">

        <h2 class="text-lg font-bold mb-4" id="confirmMessage">
            Êtes-vous sûr ? Veuillez confirmer la suppression.
        </h2>

        <div class="flex justify-end gap-3">
            <button 
                id="confirmCancel"
                class="px-4 py-2 rounded-lg bg-body hover:shadow-none border-border-base border shadow-sm transition cursor-pointer"
                aria-label="Annuler l'action">
                Annuler
            </button>
            {{-- Formulaire de confirmation --}}
            <form method="POST" id="confirmForm" aria-label="Formulaire de confirmation">
                @csrf
                @method('DELETE')
                <button 
                    class="px-4 py-2 rounded-lg bg-danger text-white hover:bg-danger-hover active:bg-danger-active transition cursor-pointer"
                    aria-label="Confirmer la suppression définitive">
                    Confirmer
                </button>
            </form>
        </div>

    </div>
</div>