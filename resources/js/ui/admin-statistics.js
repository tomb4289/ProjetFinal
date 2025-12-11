
document.addEventListener('DOMContentLoaded', () => {
    const dataUrl = window.ADMIN_STATS_DATA_URL;

    // Si on n'est pas sur la page des stats, on ne fait rien
    if (!dataUrl || !document.getElementById('stat-total-users')) {
        return;
    }

    // --- Sélecteurs DOM ---

    const periodSelect   = document.getElementById('stats-period');
    const customRange    = document.getElementById('stats-custom-range');
    const startInput     = document.getElementById('stats-start');
    const endInput       = document.getElementById('stats-end');
    const applyBtn       = document.getElementById('stats-apply');
    const refreshBtn     = document.getElementById('stats-refresh');

    const loader         = document.getElementById('stats-loader');
    const errorBox       = document.getElementById('stats-error');
    const periodRangeEl  = document.getElementById('stat-period-range');

    // Cartes globales
    const totalUsersEl           = document.getElementById('stat-total-users');
    const totalCellarsEl         = document.getElementById('stat-total-cellars');
    const totalBottlesEl         = document.getElementById('stat-total-bottles');
    const avgCellarsPerUserEl    = document.getElementById('stat-avg-cellars-per-user');
    const avgBottlesPerCellarEl  = document.getElementById('stat-avg-bottles-per-cellar');
    const avgBottlesPerUserEl    = document.getElementById('stat-avg-bottles-per-user');

    // Cartes période
    const bottlesAddedEl   = document.getElementById('stat-bottles-added');
    const newUsersEl       = document.getElementById('stat-new-users');
    const bottlesSharedEl  = document.getElementById('stat-bottles-shared');

    // Valeurs & graphiques
    const totalValueEl     = document.getElementById('stat-total-value');
    const usersChartEl     = document.getElementById('chart-values-users');
    const cellarsChartEl   = document.getElementById('chart-values-cellars');

    let usersChart  = null;
    let cellarsChart = null;

    // --- Helpers UI ---

    function toggleLoader(show) {
        if (!loader) return;
        loader.classList.toggle('hidden', !show);
    }

    function showError(message) {
        if (!errorBox) return;
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
    }

    function clearError() {
        if (!errorBox) return;
        errorBox.textContent = '';
        errorBox.classList.add('hidden');
    }

    function formatMoney(value) {
        return new Intl.NumberFormat('fr-CA', {
            style: 'currency',
            currency: 'CAD',
            minimumFractionDigits: 2
        }).format(value || 0);
    }

    // --- Chargement des stats depuis l’API ---

    function loadStats(options = {}) {
        const params = new URLSearchParams();
        const period = options.period || (periodSelect ? periodSelect.value : 'month');

        params.set('period', period);

        if (period === 'custom') {
            if (startInput && startInput.value) {
                params.set('start_date', startInput.value);
            }
            if (endInput && endInput.value) {
                params.set('end_date', endInput.value);
            }
        }

        clearError();
        toggleLoader(true);

        fetch(`${dataUrl}?${params.toString()}`, {
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors du chargement des statistiques.');
                }
                return response.json();
            })
            .then(data => {
                toggleLoader(false);
                updateUI(data);
            })
            .catch(err => {
                console.error(err);
                toggleLoader(false);
                showError(err.message || 'Une erreur est survenue.');
            });
    }

    // --- Mise à jour de l’UI ---

    function updateUI(data) {
        const global = data.global || {};
        const period = data.period || {};
        const values = data.values || {};

        // Global
        if (totalUsersEl)          totalUsersEl.textContent          = global.total_users             ?? 0;
        if (totalCellarsEl)        totalCellarsEl.textContent        = global.total_cellars           ?? 0;
        if (totalBottlesEl)        totalBottlesEl.textContent        = global.total_bottles_units     ?? 0;
        if (avgCellarsPerUserEl)   avgCellarsPerUserEl.textContent   = global.avg_cellars_per_user    ?? 0;
        if (avgBottlesPerCellarEl) avgBottlesPerCellarEl.textContent = global.avg_bottles_per_cellar  ?? 0;
        if (avgBottlesPerUserEl)   avgBottlesPerUserEl.textContent   = global.avg_bottles_per_user    ?? 0;

        // Période
        if (periodRangeEl) {
            if (period.start && period.end) {
                periodRangeEl.textContent = `${period.start} → ${period.end}`;
            } else {
                periodRangeEl.textContent = '—';
            }
        }

        if (bottlesAddedEl)  bottlesAddedEl.textContent  = period.bottles_added  ?? 0;
        if (newUsersEl)      newUsersEl.textContent      = period.new_users      ?? 0;
        if (bottlesSharedEl) bottlesSharedEl.textContent = period.bottles_shared ?? 0;

        // Valeur globale
        if (totalValueEl) {
            totalValueEl.textContent = formatMoney(values.total_value || 0);
        }

        // Graphiques
        updateCharts(values);
    }

    function updateCharts(values) {
        const perUser   = values.per_user   || [];
        const perCellar = values.per_cellar || [];

        // Graphique par usager
        if (usersChart) {
            usersChart.destroy();
            usersChart = null;
        }

        if (usersChartEl && perUser.length && typeof Chart !== 'undefined') {
            const labels = perUser.map(item => item.name);
            const data   = perUser.map(item => item.total_value);

            usersChart = new Chart(usersChartEl, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Valeur par usager',
                        data
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Graphique par cellier
        if (cellarsChart) {
            cellarsChart.destroy();
            cellarsChart = null;
        }

        if (cellarsChartEl && perCellar.length && typeof Chart !== 'undefined') {
            const labels = perCellar.map(item => item.nom);
            const data   = perCellar.map(item => item.total_value);

            cellarsChart = new Chart(cellarsChartEl, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Valeur par cellier',
                        data
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    // --- Gestion du sélecteur de période ---

    if (periodSelect) {
        periodSelect.addEventListener('change', () => {
            if (!customRange) return;

            if (periodSelect.value === 'custom') {
                customRange.classList.remove('hidden');
            } else {
                customRange.classList.add('hidden');
                loadStats(); // recharger avec la nouvelle période
            }
        });
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', () => {
            loadStats({ period: 'custom' });
        });
    }

    if (refreshBtn) {
        refreshBtn.addEventListener('click', () => {
            loadStats();
        });
    }

    // Chargement initial des stats
    loadStats();
});
