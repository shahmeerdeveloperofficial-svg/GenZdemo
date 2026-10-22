(function ($, window, document) {
    'use strict';

    if (!$ || !$.fn.dataTable || !$.fn.dataTable.Buttons) {
        return;
    }

    function exportOptions(api) {
        return {
            columns: function (index) {
                var header = api.column(index).header();
                var label = header ? (header.textContent || '').replace(/\s+/g, ' ').trim() : '';
                return api.column(index).visible() && !/^actions?$/i.test(label);
            },
            modifier: {
                page: 'current',
                search: 'applied',
                order: 'applied'
            },
            stripHtml: true,
            stripNewlines: true,
            trim: true,
            format: {
                body: function (value) {
                    var text = document.createElement('span');
                    text.innerHTML = value == null ? '' : String(value);
                    return (text.textContent || '').replace(/\s+/g, ' ').trim();
                }
            }
        };
    }

    function safeFileName(value) {
        return (value || 'GenZ-list')
            .replace(/\s+/g, '-')
            .replace(/[^a-z0-9_-]/gi, '')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '') || 'GenZ-list';
    }

    function escapeHtml(value) {
        var node = document.createElement('span');
        node.textContent = value == null ? '' : String(value);
        return node.innerHTML;
    }

    function printTable(api) {
        var data = api.buttons.exportData(exportOptions(api));
        if (document.getElementById('genz-table-print-root')) return;
        var headers = (data.header || []).map(escapeHtml);
        var rows = (data.body || []).map(function (row) {
            return '<tr>' + row.map(function (cell) {
                return '<td>' + escapeHtml(cell) + '</td>';
            }).join('') + '</tr>';
        }).join('');
        var title = document.title || 'GenZ Consultants';
        var html = '<!doctype html><html><head><meta charset="utf-8"><title>' + escapeHtml(title) + '</title>' +
            '<style>@page{size:auto;margin:14mm}body{font:14px Arial,sans-serif;color:#183451}h1{font-size:20px;margin:0 0 8px}p{color:#60758d;font-size:12px;margin:0 0 18px}table{width:100%;border-collapse:collapse}th,td{padding:9px 10px;border:1px solid #d9e3ee;text-align:left;vertical-align:top}th{background:#edf5fc;color:#073579;font-weight:700}tr:nth-child(even){background:#f8fbff}</style>' +
            '</head><body><h1>' + escapeHtml(title) + '</h1><p>GenZ Consultants · ' + escapeHtml(new Date().toLocaleString()) + '</p><table><thead><tr>' +
            headers.map(function (heading) { return '<th>' + heading + '</th>'; }).join('') +
            '</tr></thead><tbody>' + rows + '</tbody></table></body></html>';

        var printRoot = document.createElement('section');
        printRoot.id = 'genz-table-print-root';
        printRoot.setAttribute('aria-label', 'Printable table');
        printRoot.innerHTML = '<h1>' + escapeHtml(title) + '</h1><p>GenZ Consultants &middot; ' + escapeHtml(new Date().toLocaleString()) + '</p><table><thead><tr>' +
            headers.map(function (heading) { return '<th>' + heading + '</th>'; }).join('') +
            '</tr></thead><tbody>' + rows + '</tbody></table>';
        document.body.appendChild(printRoot);
        document.documentElement.classList.add('genz-table-printing');

        var cleaned = false;
        var cleanup = function () {
            if (cleaned) return;
            cleaned = true;
            document.documentElement.classList.remove('genz-table-printing');
            printRoot.remove();
            window.removeEventListener('afterprint', cleanup);
        };
        window.addEventListener('afterprint', cleanup, { once: true });
        try {
            window.print();
            // Safety net for embedded browsers that omit the afterprint event.
            window.setTimeout(cleanup, 60000);
        } catch (error) {
            cleanup();
            if (window.GenzToast) window.GenzToast.error('Printing is not available in this browser.');
        }
    }

    function toggleColumnMenu(api, buttonNode) {
        var $button = $(buttonNode);
        var $container = $button.closest('.dt-buttons');
        var $existingMenu = $container.children('.genz-column-menu');

        if ($existingMenu.length) {
            var willOpen = !$existingMenu.is(':visible');
            $existingMenu.toggle(willOpen);
            $button.attr('aria-expanded', willOpen ? 'true' : 'false');
            return;
        }

        var $menu = $('<div class="genz-column-menu" role="group" aria-label="Show or hide columns"></div>');
        for (var index = 0; index < api.columns().count(); index++) {
            (function (columnIndex) {
                var column = api.column(columnIndex);
                var header = column.header();
                var label = header ? (header.textContent || '').replace(/\s+/g, ' ').trim() : '';
                var $checkbox = $('<input type="checkbox">').prop('checked', column.visible());
                var $item = $('<label class="genz-column-menu__item"></label>')
                    .append($checkbox)
                    .append($('<span></span>').text(label || ('Column ' + (columnIndex + 1))));

                $checkbox.on('change', function () {
                    api.column(columnIndex).visible(this.checked, false);
                    api.columns.adjust().draw(false);
                });
                $menu.append($item);
            }(index));
        }

        $container.append($menu);
        $button.attr({ 'aria-expanded': 'true', 'aria-haspopup': 'true' });
    }

    function buildButtons(api) {
        var available = $.fn.dataTable.ext.buttons || {};
        var options = exportOptions(api);
        var definitions = [
            { extend: 'copyHtml5', text: '<i class="fa-solid fa-copy" aria-hidden="true"></i><span class="visually-hidden">Copy</span>', className: 'genz-export-copy', titleAttr: 'Copy visible rows', exportOptions: options },
            { extend: 'excelHtml5', text: '<i class="fa-solid fa-file-excel" aria-hidden="true"></i><span class="visually-hidden">Excel</span>', className: 'genz-export-excel', titleAttr: 'Export visible rows to Excel', filename: function () { return safeFileName(document.title); }, exportOptions: options },
            { extend: 'csvHtml5', text: '<i class="fa-solid fa-file-csv" aria-hidden="true"></i><span class="visually-hidden">CSV</span>', className: 'genz-export-csv', titleAttr: 'Export visible rows to CSV', filename: function () { return safeFileName(document.title); }, exportOptions: options }
        ];

        definitions = definitions.filter(function (definition) {
            return available[definition.extend];
        });

        if (available.pdfHtml5 && window.pdfMake && typeof window.pdfMake.createPdf === 'function') {
            definitions.push({
                extend: 'pdfHtml5',
                text: '<i class="fa-solid fa-file-pdf" aria-hidden="true"></i><span class="visually-hidden">PDF</span>',
                className: 'genz-export-pdf',
                titleAttr: 'Export visible rows to PDF',
                filename: function () { return safeFileName(document.title); },
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: options
            });
        } else {
            // Keep PDF available where pdfMake is not bundled. The same
            // filtered list opens in this tab's print dialog, with Save as PDF.
            definitions.push({
                text: '<i class="fa-solid fa-file-pdf" aria-hidden="true"></i><span class="visually-hidden">PDF</span>',
                className: 'genz-export-pdf',
                titleAttr: 'Save this list as PDF from the browser print dialog',
                action: function (event, tableApi) { printTable(tableApi); }
            });
        }

        definitions.push({
            text: '<i class="fa-solid fa-print" aria-hidden="true"></i><span class="visually-hidden">Print</span>',
            className: 'genz-export-print',
            titleAttr: 'Print visible rows',
            action: function (event, tableApi) { printTable(tableApi); }
        });
        definitions.push({
            text: '<i class="fa-solid fa-table-columns" aria-hidden="true"></i><span class="visually-hidden">Columns</span>',
            className: 'genz-export-columns',
            titleAttr: 'Choose visible columns',
            action: function (event, tableApi, node) { toggleColumnMenu(tableApi, node); }
        });

        return definitions;
    }

    function hasPageSearch($content) {
        return $content.find([
            'input[id*="search"]',
            'input[id="search-key"]',
            'input[id="search_key"]',
            'input[id="search-keyword"]',
            'input[name="search"]',
            'input[name="search_key"]',
            'form[id*="search"] input'
        ].join(',')).filter(function () {
            return $(this).is(':visible');
        }).length > 0;
    }

    function addTableSearch($toolbar, $content, api, settings) {
        if (!settings.oFeatures || !settings.oFeatures.bFilter || hasPageSearch($content)) {
            return;
        }

        var $search = $('<label class="genz-table-search"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><span class="visually-hidden">Search this list</span><input type="search" autocomplete="off" aria-label="Search this list" placeholder="Search..."></label>');
        var $input = $search.find('input').val(api.search());
        var searchTimer;

        $input.on('input.genzTableSearch', function () {
            var value = this.value;
            window.clearTimeout(searchTimer);
            searchTimer = window.setTimeout(function () {
                if (api.search() !== value) {
                    api.search(value).draw();
                }
            }, 250);
        });

        $toolbar.append($search);
    }

    function attachTools(settings) {
        var api = new $.fn.dataTable.Api(settings);
        var $wrapper = $(api.table().container());
        var $content = $(settings.nTable).closest('.zMainContent');

        if (!$wrapper.length || $wrapper.data('genzTableTools')) {
            return;
        }

        // Replace the few legacy per-table button bars with the shared toolbar.
        $wrapper.find('.dt-buttons').remove();

        var buttons = new $.fn.dataTable.Buttons(api, { buttons: buildButtons(api) });
        var $toolbar = $('<div class="genz-table-toolbar" role="toolbar" aria-label="Table export and display tools"></div>');
        addTableSearch($toolbar, $content, api, settings);
        $toolbar.append($(buttons.container()).addClass('genz-table-tools'));
        $wrapper.prepend($toolbar).data('genzTableTools', true);
    }

    $(document).on('init.dt', function (event, settings) {
        if (settings && settings.nTable && $(settings.nTable).closest('.zMainContent').length) {
            attachTools(settings);
        }
    });

    $(document).on('destroy.dt', function (event, settings) {
        if (!settings || !settings.nTableWrapper) {
            return;
        }
        $(settings.nTableWrapper).find('.genz-table-toolbar').remove().end().removeData('genzTableTools');
    });

    $(document).on('click.genzTableTools', function (event) {
        if (!$(event.target).closest('.genz-table-toolbar').length) {
            $('.genz-column-menu').hide();
            $('.genz-export-columns').attr('aria-expanded', 'false');
        }
    });

    $(document).on('keydown.genzTableTools', function (event) {
        if (event.key === 'Escape') {
            $('.genz-column-menu').hide();
            $('.genz-export-columns').attr('aria-expanded', 'false');
        }
    });
}(window.jQuery, window, document));
