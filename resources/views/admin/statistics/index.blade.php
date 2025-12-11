{{-- resources/views/admin/statistics/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Statistiques administrateur')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <x-back-btn
        route="admin.users.index"
        label="Retour à la liste"
        class="mb-2 pt-15" />

    {{-- En-tête --}}
    <x-page-header
        title="Statistiques administrateur"
        undertitle="Consulter les statistiques sur les usagers, les celliers et les bouteilles." />

    {{-- Messages de chargement / erreur --}}
    <div id="stats-loader"
        class="hidden bg-card border border-border-base rounded-xl px-4 py-2 text-xs text-text-muted">
        Chargement des statistiques...
    </div>

    <div id="stats-error"
        class="hidden bg-card border border-danger rounded-xl px-4 py-2 text-xs text-danger">
    </div>

    {{-- Cartes globales --}}
    <div class="grid gap-4 md:grid-cols-3">
        <div class="bg-card border border-border-base rounded-xl shadow-sm p-4">
            <p class="text-xs text-text-muted">Nombre total d’usagers</p>
            <p class="mt-2 text-2xl font-semibold text-text-main" id="stat-total-users">0</p>
        </div>

        <div class="bg-card border border-border-base rounded-xl shadow-sm p-4">
            <p class="text-xs text-text-muted">Nombre total de celliers</p>
            <p class="mt-2 text-2xl font-semibold text-text-main" id="stat-total-cellars">0</p>
        </div>

        <div class="bg-card border border-border-base rounded-xl shadow-sm p-4">
            <p class="text-xs text-text-muted">Bouteilles (unités)</p>
            <p class="mt-2 text-2xl font-semibold text-text-main" id="stat-total-bottles">0</p>
        </div>

        <div class="bg-card border border-border-base rounded-xl shadow-sm p-4">
            <p class="text-xs text-text-muted">Celliers / usager (moyenne)</p>
            <p class="mt-2 text-2xl font-semibold text-text-main" id="stat-avg-cellars-per-user">0</p>
        </div>

        <div class="bg-card border border-border-base rounded-xl shadow-sm p-4">
            <p class="text-xs text-text-muted">Bouteilles / cellier (moyenne)</p>
            <p class="mt-2 text-2xl font-semibold text-text-main" id="stat-avg-bottles-per-cellar">0</p>
        </div>

        <div class="bg-card border border-border-base rounded-xl shadow-sm p-4">
            <p class="text-xs text-text-muted">Bouteilles / usager (moyenne)</p>
            <p class="mt-2 text-2xl font-semibold text-text-main" id="stat-avg-bottles-per-user">0</p>
        </div>
    </div>

    {{-- Période + filtres --}}
    <div class="bg-card border border-border-base rounded-xl shadow-sm p-4 space-y-4">

        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-text-main">
                    Activité sur la période sélectionnée
                </h2>
                <p class="text-xs text-text-muted">
                    Période : <span id="stat-period-range">–</span>
                </p>
            </div>

            <div class="flex flex-col gap-2 md:flex-row md:items-center">
                <div class="flex items-center gap-2">
                    <label for="stats-period" class="text-xs text-text-muted">
                        Période
                    </label>
                    <select id="stats-period"
                        class="border border-border-base bg-card text-xs rounded-md px-2 py-1 text-text-main">
                        <option value="day">Aujourd’hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month" selected>Ce mois-ci</option>
                        <option value="year">Cette année</option>
                        <option value="custom">Personnalisée</option>
                    </select>
                </div>

                <div id="stats-custom-range" class="hidden md:flex md:items-center md:gap-2">
                    <input type="date" id="stats-start"
                        class="border border-border-base bg-card text-xs rounded-md px-2 py-1 text-text-main" />
                    <input type="date" id="stats-end"
                        class="border border-border-base bg-card text-xs rounded-md px-2 py-1 text-text-main" />
                    <button id="stats-apply"
                        class="inline-flex items-center justify-center rounded-md px-3 py-1 text-xs font-medium text-white bg-primary hover:bg-primary-hover transition-colors">
                        Appliquer
                    </button>
                </div>

                <button id="stats-refresh"
                    class="inline-flex items-center justify-center rounded-md px-3 py-1 text-xs font-medium border border-border-base bg-muted hover:bg-card-hover transition-colors">
                    Rafraîchir
                </button>
            </div>
        </div>

        {{-- Cartes activité période --}}
        <div class="grid gap-3 md:grid-cols-3">
            <div class="bg-card border border-primary rounded-xl shadow-sm p-4">
                <p class="text-xs text-text-muted">Bouteilles ajoutées</p>
                <p class="mt-2 text-2xl font-semibold text-text-main" id="stat-bottles-added">0</p>
            </div>

            <div class="bg-card border border-green-400 rounded-xl shadow-sm p-4">
                <p class="text-xs text-text-muted">Nouveaux usagers</p>
                <p class="mt-2 text-2xl font-semibold text-text-main" id="stat-new-users">0</p>
            </div>

            <div class="bg-card border border-yellow-400 rounded-xl shadow-sm p-4">
                <p class="text-xs text-text-muted">Bouteilles partagées</p>
                <p class="mt-2 text-2xl font-semibold text-text-main" id="stat-bottles-shared">0</p>
            </div>
        </div>
    </div>

    {{-- Valeur des bouteilles --}}
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="bg-card border border-border-base rounded-xl shadow-sm p-4 lg:col-span-1">
            <p class="text-xs text-text-muted">Valeur totale (tous usagers & celliers)</p>
            <p class="mt-2 text-2xl font-semibold text-text-main" id="stat-total-value">0 $</p>
            <p class="mt-1 text-[11px] text-text-muted">
                Somme de <code>prix × quantite</code> sur toutes les bouteilles.
            </p>
        </div>

        <div class="bg-card border border-border-base rounded-xl shadow-sm p-4 lg:col-span-1">
            <p class="mb-2 text-xs text-text-muted">Valeur par usager</p>
            <div class="h-40">
                <canvas id="chart-values-users"></canvas>
            </div>
        </div>

        <div class="bg-card border border-border-base rounded-xl shadow-sm p-4 lg:col-span-1">
            <p class="mb-2 text-xs text-text-muted">Valeur par cellier</p>
            <div class="h-40">
                <canvas id="chart-values-cellars"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- SCRIPTS spécifiques à la page, inclus directement ici --}}
<script>
    window.ADMIN_STATS_DATA_URL = "{{ route('admin.statistics.data') }}";
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@vite('resources/js/ui/admin-statistics.js')
@endsection