// Inventory dashboard: refreshes stats every 30 seconds (AJAX polling)
const InventoryDashboard = (() => {
    const REFRESH_MS = 30000;
    const urls = window.FT_INVENTORY_API;

    const badgeClass = {
        danger: 'badge-missed',
        warning: 'badge-scheduled',
        success: 'badge-completed',
    };

    const typeBadge = {
        stock_in: 'badge-completed',
        stock_out: 'badge-missed',
        adjustment: 'badge-scheduled',
    };

    // number formats
    const formatInt = n => Math.round(n).toLocaleString();
    const formatMoney = n => '$' + Number(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // stop product names from breaking the HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    // GET a URL and return its JSON
    async function getJson(url) {
        const response = await fetch(url, { headers: { Accept: 'application/json' } });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return response.json();
    }

    // count up/down from the old number to the new one
    function animateNumber(el, to, format) {
        const from = Number(el.dataset.value ?? 0);
        el.dataset.value = to;

        if (from === to && el.textContent !== '—') {
            return;
        }

        const start = performance.now();
        const duration = 600;

        function step(now) {
            const progress = Math.min((now - start) / duration, 1);
            el.textContent = format(from + (to - from) * progress);

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = format(to);
                el.classList.add('ft-flash');
                setTimeout(() => el.classList.remove('ft-flash'), 800);
            }
        }

        requestAnimationFrame(step);
    }

    // update the 4 stat cards
    function renderStats(stats) {
        animateNumber(document.getElementById('inv-total-products'), stats.total_products, formatInt);
        animateNumber(document.getElementById('inv-low-stock'), stats.low_stock, formatInt);
        animateNumber(document.getElementById('inv-out-of-stock'), stats.out_of_stock, formatInt);
        animateNumber(document.getElementById('inv-value'), stats.inventory_value, formatMoney);

        document.getElementById('inv-total-units').textContent = `${formatInt(stats.total_units)} units in stock`;
        document.getElementById('inv-transactions-today').textContent = `${stats.transactions_today} transactions today`;
    }

    // update the low stock list
    function renderLowStock(products) {
        const list = document.getElementById('low-stock-list');
        document.getElementById('low-stock-count').textContent = products.length;

        if (products.length === 0) {
            list.innerHTML = '<li class="list-group-item text-muted">All products are above their reorder level.</li>';
            return;
        }

        list.innerHTML = products.map(p => `
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <div>
                    <a href="${p.url}" class="text-decoration-none fw-semibold">${escapeHtml(p.name)}</a>
                    <div class="small text-muted">${escapeHtml(p.sku)} · reorder at ${p.reorder_level}</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold">${p.quantity}</div>
                    <span class="ft-badge ${badgeClass[p.stock_status_color]} text-nowrap">${p.stock_status}</span>
                </div>
            </li>
        `).join('');
    }

    // update the recent transactions table
    function renderTransactions(transactions) {
        const body = document.getElementById('recent-transactions-body');

        if (transactions.length === 0) {
            body.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No transactions yet.</td></tr>';
            return;
        }

        body.innerHTML = transactions.map(t => `
            <tr>
                <td class="text-muted small">${t.created_at_human}</td>
                <td><a href="${t.product.url}" class="text-decoration-none">${escapeHtml(t.product.name)}</a></td>
                <td><span class="ft-badge ${typeBadge[t.type]}">${t.type_label}</span></td>
                <td class="text-end fw-semibold ${t.quantity > 0 ? 'text-success' : 'text-danger'}">${t.quantity > 0 ? '+' : ''}${t.quantity}</td>
                <td>${escapeHtml(t.user)}</td>
            </tr>
        `).join('');
    }

    // show live/error status and last updated time
    function setStatus(ok, message) {
        document.getElementById('live-dot').classList.toggle('error', !ok);
        document.getElementById('refresh-status').textContent = message;
    }

    // fetch all 3 endpoints and redraw
    async function refresh() {
        try {
            const [stats, lowStock, recent] = await Promise.all([
                getJson(urls.stats),
                getJson(urls.lowStock),
                getJson(urls.recent),
            ]);

            renderStats(stats);
            renderLowStock(lowStock.data);
            renderTransactions(recent.data);

            setStatus(true, `Last updated: ${new Date().toLocaleTimeString()}`);
        } catch (error) {
            // keep the old numbers on screen and try again next time
            console.error('Dashboard refresh failed:', error);
            setStatus(false, `Couldn't refresh (${error.message}). Retrying in 30s...`);
        }
    }

    // load now, then every 30 seconds
    function start() {
        refresh();

        setInterval(() => {
            // skip polling while the tab is hidden
            if (!document.hidden) {
                refresh();
            }
        }, REFRESH_MS);

        document.getElementById('refresh-now').addEventListener('click', refresh);

        // refresh right away when the user comes back to the tab
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                refresh();
            }
        });
    }

    return { start, refresh };
})();

document.addEventListener('DOMContentLoaded', InventoryDashboard.start);