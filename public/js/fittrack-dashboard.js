/**
 * FitTrack Pro — Dashboard interactivity
 * Continues the Lab 4 dataManager.js pattern, but now the data comes from
 * the Laravel backend (WorkoutController@data) instead of hardcoded JSON.
 */
(function () {
    'use strict';

    // ---- Configuration -----------------------------------------------
    const CONFIG = {
        dataUrl: window.FT_WORKOUTS_DATA_URL,
        showUrlBase: window.FT_WORKOUT_SHOW_URL_BASE,
        colors: {
            teal: '#0b6e73',
            tealLight: '#14a3ad',
            orange: '#ff6b35',
            purple: '#7a4fd4',
            amber: '#f2b134',
            green: '#1f9d55',
            red: '#e0524d',
        },
        simulateIntervalMs: 15000,
    };

    // ---- State ----------------------------------------------------------
    const state = {
        workouts: [],
        filters: { type: '', status: '', minCalories: null, maxCalories: null, search: '' },
        charts: {},
    };

    // ---- Data management --------------------------------------------------
    async function initializeData() {
        try {
            const res = await fetch(CONFIG.dataUrl, { headers: { Accept: 'application/json' } });
            if (!res.ok) throw new Error('Failed to load workouts');
            state.workouts = await res.json();
        } catch (err) {
            console.error('Could not load workout data:', err);
            state.workouts = [];
        }
        render();
    }

    function getProducts() {
        return state.workouts;
    }

    function getProductById(id) {
        return state.workouts.find((w) => w.id === id);
    }

    function getLowStockProducts() {
        // "Low stock" equivalent for a fitness dashboard: missed workouts.
        return state.workouts.filter((w) => w.status === 'missed');
    }

    // ---- Filtering & search ------------------------------------------------
    function applyFilters() {
        const { type, status, minCalories, maxCalories, search } = state.filters;

        return state.workouts.filter((w) => {
            if (type && w.type !== type) return false;
            if (status && w.status !== status) return false;
            if (minCalories !== null && w.calories < minCalories) return false;
            if (maxCalories !== null && w.calories > maxCalories) return false;
            if (search && !w.name.toLowerCase().includes(search.toLowerCase())) return false;
            return true;
        });
    }

    function searchProducts(query) {
        state.filters.search = query;
        render();
    }

    // ---- DOM rendering (table) ---------------------------------------------
    function renderActivityTable() {
        const tbody = document.getElementById('activity-table-body');
        const noResults = document.getElementById('no-results');
        if (!tbody) return;

        const rows = applyFilters();
        tbody.innerHTML = '';

        if (rows.length === 0) {
            noResults?.classList.remove('d-none');
        } else {
            noResults?.classList.add('d-none');
        }

        rows.forEach((w) => {
            const tr = document.createElement('tr');

            const badgeClass = w.status === 'completed' ? 'badge-completed'
                : w.status === 'missed' ? 'badge-missed'
                : 'badge-scheduled';

            tr.innerHTML = `
                <td>${escapeHtml(w.name)}</td>
                <td>${escapeHtml(w.type)}</td>
                <td>${formatDate(w.workout_date)}</td>
                <td>${w.duration_minutes} min</td>
                <td>${w.calories} kcal</td>
                <td><span class="ft-badge ${badgeClass}">${capitalize(w.status)}</span></td>
                <td class="text-end">
                    <a href="${CONFIG.showUrlBase}/${w.id}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            `;

            if (w.status === 'missed') {
                tr.classList.add('table-danger', 'bg-opacity-10');
            }

            tbody.appendChild(tr);
        });
    }

    function renderMissedAlert() {
        const banner = document.getElementById('missed-alert');
        const list = document.getElementById('missed-list');
        const count = document.getElementById('missed-count');
        if (!banner || !list || !count) return;

        const missed = getLowStockProducts().slice(0, 5);
        count.textContent = missed.length;
        list.innerHTML = '';

        missed.forEach((w) => {
            const li = document.createElement('li');
            li.textContent = `${w.name} (${w.type}, ${formatDate(w.workout_date)})`;
            list.appendChild(li);
        });

        banner.style.display = missed.length > 0 ? 'flex' : 'none';
    }

    // ---- Charts (Chart.js) --------------------------------------------------
    function getCaloriesByType(rows) {
        const totals = {};
        rows.forEach((w) => {
            totals[w.type] = (totals[w.type] || 0) + Number(w.calories || 0);
        });
        return totals;
    }

    function getStatusDistribution(rows) {
        const totals = { completed: 0, scheduled: 0, missed: 0 };
        rows.forEach((w) => {
            totals[w.status] = (totals[w.status] || 0) + 1;
        });
        return totals;
    }

    function renderCharts() {
        const rows = applyFilters();
        renderCaloriesChart(rows);
        renderStatusChart(rows);
    }

    function renderCaloriesChart(rows) {
        const ctx = document.getElementById('caloriesByTypeChart');
        if (!ctx || typeof Chart === 'undefined') return;

        const totals = getCaloriesByType(rows);
        const labels = Object.keys(totals);
        const values = Object.values(totals);
        const palette = [CONFIG.colors.teal, CONFIG.colors.orange, CONFIG.colors.tealLight, CONFIG.colors.purple, CONFIG.colors.amber];

        if (state.charts.calories) {
            state.charts.calories.data.labels = labels;
            state.charts.calories.data.datasets[0].data = values;
            state.charts.calories.update();
            return;
        }

        state.charts.calories = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Calories burned',
                    data: values,
                    backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } },
            },
        });
    }

    function renderStatusChart(rows) {
        const ctx = document.getElementById('statusDistributionChart');
        if (!ctx || typeof Chart === 'undefined') return;

        const totals = getStatusDistribution(rows);
        const labels = ['Completed', 'Scheduled', 'Missed'];
        const values = [totals.completed || 0, totals.scheduled || 0, totals.missed || 0];
        const colors = [CONFIG.colors.green, CONFIG.colors.amber, CONFIG.colors.red];

        if (state.charts.status) {
            state.charts.status.data.datasets[0].data = values;
            state.charts.status.update();
            return;
        }

        state.charts.status = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data: values, backgroundColor: colors }],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
            },
        });
    }

    // ---- CSV export ---------------------------------------------------------
    function exportToCSV() {
        const rows = applyFilters();
        const header = ['Activity', 'Type', 'Date', 'Duration (min)', 'Calories', 'Status'];
        const lines = [header.join(',')];

        rows.forEach((w) => {
            lines.push([
                csvEscape(w.name),
                csvEscape(w.type),
                formatDate(w.workout_date),
                w.duration_minutes,
                w.calories,
                w.status,
            ].join(','));
        });

        downloadCSV(lines.join('\n'), `workouts-export-${new Date().toISOString().slice(0, 10)}.csv`);
    }

    function downloadCSV(csvContent, filename) {
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    // ---- Simulated real-time updates ----------------------------------------
    function simulateInventoryUpdate() {
        if (state.workouts.length === 0) return;

        const idx = Math.floor(Math.random() * state.workouts.length);
        const workout = state.workouts[idx];

        // Nudge calories slightly to mimic a live feed, then re-render.
        workout.calories = Math.max(0, workout.calories + (Math.random() > 0.5 ? 5 : -5));

        render();
        showToast(`Updated: ${workout.name} — ${workout.calories} kcal`);
    }

    function showToast(message) {
        let toast = document.getElementById('ft-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'ft-toast';
            toast.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#0b6e73;color:#fff;padding:10px 16px;border-radius:8px;font-size:0.85rem;z-index:2000;opacity:0;transition:opacity .3s;';
            document.body.appendChild(toast);
        }
        toast.textContent = message;
        toast.style.opacity = '1';
        clearTimeout(toast._hideTimeout);
        toast._hideTimeout = setTimeout(() => { toast.style.opacity = '0'; }, 2500);
    }

    // ---- Event wiring ---------------------------------------------------------
    function wireEvents() {
        document.getElementById('filter-type')?.addEventListener('change', (e) => {
            state.filters.type = e.target.value;
            render();
        });

        document.getElementById('status-pills')?.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-status]');
            if (!btn) return;
            state.filters.status = btn.dataset.status;
            document.querySelectorAll('#status-pills button').forEach((b) => b.classList.remove('active-pill'));
            btn.classList.add('active-pill');
            render();
        });

        document.getElementById('filter-min-calories')?.addEventListener('input', (e) => {
            state.filters.minCalories = e.target.value === '' ? null : Number(e.target.value);
            render();
        });

        document.getElementById('filter-max-calories')?.addEventListener('input', (e) => {
            state.filters.maxCalories = e.target.value === '' ? null : Number(e.target.value);
            render();
        });

        document.getElementById('reset-filters')?.addEventListener('click', () => {
            state.filters = { type: '', status: '', minCalories: null, maxCalories: null, search: '' };
            document.getElementById('filter-type').value = '';
            document.getElementById('filter-min-calories').value = '';
            document.getElementById('filter-max-calories').value = '';
            document.getElementById('search-input').value = '';
            document.querySelectorAll('#status-pills button').forEach((b) => b.classList.remove('active-pill'));
            document.querySelector('#status-pills button[data-status=""]')?.classList.add('active-pill');
            render();
        });

        document.getElementById('search-input')?.addEventListener('input', (e) => {
            searchProducts(e.target.value);
        });

        document.getElementById('export-csv')?.addEventListener('click', exportToCSV);
    }

    // ---- Helpers --------------------------------------------------------------
    function formatDate(dateStr) {
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function csvEscape(value) {
        const str = String(value ?? '');
        return /[",\n]/.test(str) ? `"${str.replace(/"/g, '""')}"` : str;
    }

    function render() {
        renderActivityTable();
        renderMissedAlert();
        renderCharts();
    }

    // ---- Boot -------------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', () => {
        wireEvents();
        initializeData();
        setInterval(simulateInventoryUpdate, CONFIG.simulateIntervalMs);
    });
})();
