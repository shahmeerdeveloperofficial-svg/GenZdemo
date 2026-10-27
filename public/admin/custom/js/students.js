(function ($) {
    "use strict";

    function notifyStatus(type, message) {
        if (window.GenzToast && typeof window.GenzToast[type] === 'function') {
            window.GenzToast[type](message);
        } else if (window.toastr && typeof window.toastr[type] === 'function') {
            window.toastr[type](message);
        }
    }

    $(document).on('click', '#studentDataTable [data-student-status-toggle]', function (event) {
        event.preventDefault();

        var $button = $(this);
        if ($button.prop('disabled')) {
            return;
        }

        var currentStatus = Number($button.attr('data-current-status'));
        var activeStatus = Number($button.attr('data-active-status'));
        var nextStatus = currentStatus === activeStatus
            ? Number($button.attr('data-inactive-status'))
            : activeStatus;

        $button.prop('disabled', true).attr({ 'aria-busy': 'true', 'aria-live': 'polite' });

        $.ajax({
            type: 'POST',
            url: $button.attr('data-status-url'),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { status: nextStatus }
        }).done(function (response) {
            if (!response || response.status !== true) {
                notifyStatus('error', response && response.message ? response.message : 'Could not update student status.');
                return;
            }

            var isActive = nextStatus === activeStatus;
            var label = isActive ? $button.attr('data-active-label') : $button.attr('data-inactive-label');
            var hint = isActive ? $button.attr('data-deactivate-hint') : $button.attr('data-activate-hint');

            $button.attr({
                'data-current-status': nextStatus,
                'aria-pressed': isActive ? 'true' : 'false',
                'aria-label': label + '. ' + hint,
                'title': hint
            })
                .toggleClass('zBadge-active', isActive)
                .toggleClass('zBadge-deactive', !isActive)
                .find('span').text(label);

            notifyStatus('success', response.message || 'Student status updated.');
        }).fail(function (xhr) {
            var message = xhr.responseJSON && xhr.responseJSON.message
                ? xhr.responseJSON.message
                : 'Could not update student status. Please try again.';
            notifyStatus('error', message);
        }).always(function () {
            $button.prop('disabled', false).removeAttr('aria-busy');
        });
    });

    $('#studentDataTable').DataTable({
        pageLength: 10,
        ordering: true,
        serverSide: true,
        processing: true,
        searching: true,
        responsive: true,
        ajax: $('#student-route').val(),
        language: {
            paginate: {
                previous: "<i class='fa-solid fa-angles-left'></i>",
                next: "<i class='fa-solid fa-angles-right'></i>",
            },
        },
        dom: '<>tr<"tableBottom"<"row align-items-center"<"col-sm-6"<"tableInfo"i>><"col-sm-6"<"tablePagi"p>>>><"clear">',
        columns: [
            {"data": "image", "name": "image", searchable: false, responsivePriority: 1},
            {"data": "name", "name": "name", responsivePriority: 2},
            {"data": "email", "name": "email"},
            {"data": "mobile", "name": "mobile"},
            {"data": "status", "name": "status", searchable: false},
            {"data": "action", searchable: false, responsivePriority: 2},
        ]
    });

})(jQuery)
