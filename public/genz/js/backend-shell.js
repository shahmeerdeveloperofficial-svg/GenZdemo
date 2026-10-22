(function () {
    'use strict';

    var root = document.documentElement;
    var body = document.body;
    if (!body) return;

    // The text lockup stays visible until the supplied logo has really loaded.
    // If the asset is missing or broken, the branded fallback remains in place.
    document.querySelectorAll('.genz-brand__image').forEach(function (image) {
        var fallback = image.parentElement.querySelector('.genz-brand__fallback');
        function showFallback() {
            image.style.visibility = 'hidden';
            if (fallback) fallback.hidden = false;
        }
        function showImage() {
            if (image.naturalWidth < 1) return showFallback();
            image.style.visibility = 'visible';
            if (fallback) fallback.hidden = true;
        }

        image.addEventListener('load', showImage, { once: true });
        image.addEventListener('error', showFallback, { once: true });
        if (image.complete) image.naturalWidth > 0 ? showImage() : showFallback();
    });

    if (!body.classList.contains('genz-admin-shell')) return;

    // Show a branded skeleton only when an actual page navigation takes long
    // enough to notice. The short delay avoids flashing it on fast loads.
    var pageLoadingNode = document.getElementById('genz-page-loading');
    var hasPageLoading = !!pageLoadingNode;
    var loadingTimer = null;
    function clearPageLoading() {
        window.clearTimeout(loadingTimer);
        loadingTimer = null;
        root.classList.remove('genz-page-is-loading');
        body.removeAttribute('aria-busy');
        if (pageLoadingNode) pageLoadingNode.setAttribute('aria-hidden', 'true');
    }
    function schedulePageLoading(event) {
        window.clearTimeout(loadingTimer);
        loadingTimer = window.setTimeout(function () {
            if (event.defaultPrevented) return;
            root.classList.add('genz-page-is-loading');
            body.setAttribute('aria-busy', 'true');
            if (pageLoadingNode) pageLoadingNode.setAttribute('aria-hidden', 'false');
        }, 100);
    }

    document.addEventListener('click', function (event) {
        if (!hasPageLoading) return;
        if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        var link = event.target.closest('a[href]');
        if (!link || link.target && link.target !== '_self' || link.hasAttribute('download') || link.hasAttribute('data-no-loading')) return;
        if (link.matches('[data-bs-toggle], [data-toggle], [href^="#"], [href^="javascript:"]')) return;
        var destination;
        try { destination = new URL(link.href, window.location.href); } catch (error) { return; }
        if (destination.origin !== window.location.origin || (destination.pathname === window.location.pathname && destination.search === window.location.search && destination.hash)) return;
        schedulePageLoading(event);
    }, true);

    document.addEventListener('submit', function (event) {
        if (!hasPageLoading) return;
        var form = event.target;
        if (!form || form.matches('[data-no-loading], [data-ajax], [target="_blank"]')) return;
        schedulePageLoading(event);
    }, true);

    window.addEventListener('pageshow', clearPageLoading);
    window.addEventListener('pagehide', function () { window.clearTimeout(loadingTimer); });

    // Server-side tables are sortable; generated controls and presentation-only
    // columns must never be sent to the database as ORDER BY fields.
    if (window.jQuery && window.jQuery.fn.dataTable) {
        window.jQuery(document).on('preInit.dt.genzSort', function (event, settings) {
            if (!settings || !settings.aoColumns || !settings.oClasses) return;

            var presentationKeys = /^(?:action|actions|dt_rowindex|image|thumbnail|banner_image|flag|avatar|photo|picture|countryname|universityname|studylevelsname|blogcategoryname|student|consultant|consulter|service|roles|order_board|orderid|payment_info|item_name|actual_price|commission_percentage|username|study_levels|date_time)$/i;
            var presentationLabels = /^(?:action|actions|manage|options|select|#|no\.?|s\.?no\.?|sr\.?|serial)$/i;
            var computedColumnsByTable = {
                studentDataTable: ['name'],
                consulterDataTable: ['name', 'off_days'],
                affiliateDataTable: ['name'],
                affiliateDashboardTable: ['date', 'item_name'],
                payoutDataTableAll: ['transaction_id', 'user_name', 'email', 'gateway', 'transaction_date'],
                payoutDataTablePending: ['user_name', 'email', 'gateway', 'transaction_date'],
                payoutDataTableCompleted: ['transaction_id', 'user_name', 'email', 'gateway', 'transaction_date'],
                payoutDataTableRejected: ['transaction_id', 'user_name', 'email', 'gateway', 'transaction_date']
            };
            var tableId = settings.sTableId || '';
            var tableSpecificKeys = computedColumnsByTable[tableId] || [];
            if (/^orderDataTable/.test(tableId)) tableSpecificKeys = ['date', 'amount', 'payment_info', 'status', 'gatewayname'];
            var disabledSortClasses = [settings.oClasses.sSortable, settings.oClasses.sSortableAsc, settings.oClasses.sSortableDesc, settings.oClasses.sSortableNone].filter(Boolean).join(' ');

            settings.aoColumns.forEach(function (column) {
                var key = typeof column.mData === 'string' ? column.mData : '';
                var sortName = column.sName || '';
                var header = column.nTh;
                var label = header ? (header.textContent || '').replace(/\s+/g, ' ').trim() : '';
                var isComputedColumn = tableSpecificKeys.indexOf(key.toLowerCase()) !== -1 || tableSpecificKeys.indexOf(sortName.toLowerCase()) !== -1;
                if (!isComputedColumn && !presentationKeys.test(key) && !presentationKeys.test(sortName) && !presentationLabels.test(label)) return;

                column.bSortable = false;
                column.sSortingClass = settings.oClasses.sSortableNone;
                if (header) {
                    window.jQuery(header).removeClass(disabledSortClasses).addClass(settings.oClasses.sSortableNone).removeAttr('tabindex').attr('aria-label', label);
                }
            });
        });
    }

    var themeButton = document.getElementById('genz-theme-toggle');
    var theme = root.getAttribute('data-genz-theme') === 'dark' ? 'dark' : 'light';

    function setTheme(nextTheme, persist) {
        theme = nextTheme === 'dark' ? 'dark' : 'light';
        root.setAttribute('data-genz-theme', theme);
        body.style.colorScheme = theme;

        if (persist !== false) {
            try {
                localStorage.setItem('genz-admin-theme', theme);
            } catch (error) {}
        }

        if (themeButton) {
            var icon = themeButton.querySelector('i');
            var label = theme === 'dark' ? themeButton.dataset.labelLight : themeButton.dataset.labelDark;
            if (icon) icon.className = 'fa-solid ' + (theme === 'dark' ? 'fa-sun' : 'fa-moon');
            themeButton.setAttribute('aria-label', label || 'Toggle color theme');
            themeButton.setAttribute('title', label || 'Toggle color theme');
            themeButton.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
            var screenReaderLabel = themeButton.querySelector('.visually-hidden');
            if (screenReaderLabel) screenReaderLabel.textContent = label || 'Toggle color theme';
        }

        document.dispatchEvent(new CustomEvent('genz:theme-change', { detail: { theme: theme } }));
    }

    setTheme(theme, false);
    if (themeButton) {
        themeButton.addEventListener('click', function () {
            setTheme(theme === 'dark' ? 'light' : 'dark');
        });
    }

    var sidebar = document.querySelector('.zSidebar');
    var sidebarButton = document.querySelector('[data-genz-sidebar-toggle]');
    if (!sidebar || !sidebarButton) return;

    function setSidebarCollapsed(collapsed, persist) {
        root.classList.toggle('genz-sidebar-collapsed', collapsed);
        sidebarButton.setAttribute('aria-expanded', collapsed ? 'false' : 'true');

        var label = collapsed ? sidebarButton.dataset.expandLabel : sidebarButton.dataset.collapseLabel;
        sidebarButton.setAttribute('aria-label', label || 'Toggle navigation width');
        sidebarButton.setAttribute('title', label || 'Toggle navigation width');
        var screenReaderLabel = sidebarButton.querySelector('.visually-hidden');
        var icon = sidebarButton.querySelector('i');
        if (screenReaderLabel) screenReaderLabel.textContent = label || 'Toggle navigation width';
        if (icon) icon.className = 'fa-solid ' + (collapsed ? 'fa-angles-right' : 'fa-angles-left');

        if (persist !== false) {
            try {
                localStorage.setItem('genz-sidebar-collapsed', collapsed ? 'true' : 'false');
            } catch (error) {}
        }
    }

    setSidebarCollapsed(root.classList.contains('genz-sidebar-collapsed'), false);
    sidebarButton.addEventListener('click', function () {
        setSidebarCollapsed(!root.classList.contains('genz-sidebar-collapsed'));
    });

    sidebar.querySelectorAll('.zSidebar-menu a').forEach(function (link) {
        var label = link.querySelector('span');
        if (label && !link.hasAttribute('title')) link.setAttribute('title', label.textContent.trim());
    });

    sidebar.addEventListener('click', function (event) {
        var submenuTrigger = event.target.closest('.zSidebar-menu a[data-bs-toggle="collapse"]');
        if (submenuTrigger && root.classList.contains('genz-sidebar-collapsed')) {
            setSidebarCollapsed(false);
        }
    });
}());
