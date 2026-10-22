(function ($) {
    'use strict';
    if (!$ || !$.fn.dataTable) return;

    $(document).on('preXhr.dt', function (event, settings, data) {
        data.criteria = settings.genzCriteria || {};
    });

    function render(settings, json) {
        if (!json || !Array.isArray(json.criteriaOptions)) return;
        var api = new $.fn.dataTable.Api(settings);
        var $table = $(api.table().node());
        var $container = $(api.table().container());
        var $panel = $container.children('.genz-list-criteria');
        if (!json.criteriaOptions.length) {
            if (!Object.keys(settings.genzCriteria || {}).length) $panel.remove();
            return;
        }
        if (!$panel.length) {
            $panel = $('<form class="genz-list-criteria mb-3" data-no-loading></form>');
            $panel.append($('<div class="genz-list-criteria__heading"></div>')
                .append('<i class="fa-solid fa-filter" aria-hidden="true"></i>')
                .append($('<strong></strong>').text('Filter records')));
            $panel.append('<div class="genz-list-criteria__fields"></div>');
            $container.prepend($panel);
            $panel.on('submit', function (event) {
                event.preventDefault();
                var selection = {};
                $panel.find('select').each(function () {
                    if (this.value !== '') selection[this.name] = this.value;
                });
                settings.genzCriteria = selection;
                api.ajax.reload(null, true);
            });
            $panel.on('click', '[data-reset-criteria]', function () {
                settings.genzCriteria = {};
                $panel.find('select').val('');
                api.ajax.reload(null, true);
            });
        }
        var $fields = $panel.find('.genz-list-criteria__fields').empty();
        json.criteriaOptions.forEach(function (field) {
            var id = $table.attr('id') + '-criteria-' + field.key;
            var $select = $('<select class="form-select"></select>').attr({ id: id, name: field.key });
            $select.append($('<option></option>').val('').text('All'));
            field.options.forEach(function (option) {
                $select.append($('<option></option>').val(option.value).text(option.label));
            });
            var selected = (settings.genzCriteria || {})[field.key] || '';
            if (selected && !$select.find('option').toArray().some(function (option) { return option.value === selected; })) {
                $select.append($('<option></option>').val(selected).text(selected));
            }
            $select.val(selected);
            $fields.append($('<div class="genz-list-criteria__field"></div>')
                .append($('<label></label>').attr('for', id).text(field.label)).append($select));
        });
        $fields.append($('<div class="genz-list-criteria__actions"></div>')
            .append('<button type="submit" class="btn btn-primary">Apply filters</button>')
            .append('<button type="button" class="btn btn-outline-primary" data-reset-criteria>Reset</button>'));
    }

    $(document).on('xhr.dt', function (event, settings, json) {
        // The initial Ajax response can arrive before DataTables builds its wrapper.
        window.setTimeout(function () { render(settings, json); }, 0);
    });
    $(document).on('init.dt', function (event, settings) {
        render(settings, settings.json);
    });
})(window.jQuery);
