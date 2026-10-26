(function () {
    'use strict';

    window.ictNotify = function (options) {
        return window.Swal ? Swal.fire({
            ...options,
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            customClass: { popup: 'ict-toast' },
        }) : Promise.resolve();
    };

    function applyTableFilters(table) {
        const search = document.querySelector(`[data-table-search="${table.id}"]`)?.value.toLowerCase().trim() ?? '';
        const filters = [...document.querySelectorAll(`[data-table-filter="${table.id}"]`)];
        let visible = 0;
        table.querySelectorAll('tbody tr').forEach((row) => {
            if (row.classList.contains('empty-row')) return;
            const matchesSearch = search === '' || row.textContent.toLowerCase().includes(search);
            const matchesFilters = filters.every((select) => {
                if (select.value === '') return true;
                const cellValue = row.cells[Number(select.dataset.column)]?.textContent.toLowerCase().trim() ?? '';
                const filterValue = select.value.toLowerCase();
                return select.dataset.exact === 'true' ? cellValue === filterValue : cellValue.includes(filterValue);
            });
            row.hidden = !matchesSearch || !matchesFilters;
            if (!row.hidden) visible++;
        });
        document.querySelectorAll(`[data-filter-count="${table.id}"], [data-table-count="${table.id}"]`)
            .forEach((count) => { count.textContent = `${visible} shown`; });
    }

    document.querySelectorAll('[data-table-search]').forEach((input) => {
        const table = document.getElementById(input.dataset.tableSearch);
        if (table) input.addEventListener('input', () => applyTableFilters(table));
    });
    document.querySelectorAll('[data-table-filter]').forEach((select) => {
        const table = document.getElementById(select.dataset.tableFilter);
        if (table) select.addEventListener('change', () => applyTableFilters(table));
    });
    document.querySelectorAll('[data-apply-filters]').forEach((button) => {
        button.addEventListener('click', () => {
            const table = document.getElementById(button.dataset.applyFilters);
            if (table) applyTableFilters(table);
        });
    });
    document.querySelectorAll('[data-reset-filters]').forEach((button) => {
        button.addEventListener('click', () => {
            const table = document.getElementById(button.dataset.resetFilters);
            if (!table) return;
            document.querySelectorAll(`[data-table-filter="${table.id}"]`).forEach((filter) => { filter.value = ''; });
            const search = document.querySelector(`[data-table-search="${table.id}"]`);
            if (search) search.value = '';
            applyTableFilters(table);
        });
    });

    document.querySelectorAll('[data-modal-open]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const modal = document.getElementById(trigger.dataset.modalOpen);
            if (modal && !modal.open) modal.showModal();
        });
    });
    document.querySelectorAll('[data-modal-close]').forEach((trigger) => {
        trigger.addEventListener('click', () => trigger.closest('dialog')?.close());
    });
    document.querySelectorAll('dialog.modal').forEach((modal) => {
        modal.addEventListener('click', (event) => { if (event.target === modal) modal.close(); });
    });
}());
