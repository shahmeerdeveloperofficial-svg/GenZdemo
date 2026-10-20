(function ($) {
    ("use strict");
    var programTable = $("#programDataTable");

    // Replace DataTables' blocking browser alert with a useful, retryable GenZ notification.
    $.fn.dataTable.ext.errMode = 'none';
    programTable.on('error.dt', function (event, settings, techNote, message) {
        var xhr = settings.jqXHR;
        var status = xhr ? xhr.status : 0;
        var sessionIssue = status === 401 || status === 419 || status === 302;
        var accessDenied = status === 403;

        console.warn('Programs table request failed', { status: status, technicalNote: techNote, message: message });

        if (window.GenzToast) {
            window.GenzToast[sessionIssue ? 'warning' : 'error']({
                title: sessionIssue ? 'Please sign in again' : (accessDenied ? 'Access denied' : 'Programs could not be loaded'),
                message: sessionIssue
                    ? 'Your session or access may have expired. Reload the page and try again.'
                    : (accessDenied
                        ? 'Your account is not allowed to view this program list. Contact an administrator if this seems incorrect.'
                        : 'The server could not return the program list' + (status ? ' (HTTP ' + status + ')' : '') + '. Check your connection and retry.'),
                action: {
                    label: sessionIssue || accessDenied ? 'Reload page' : 'Retry',
                    onClick: function () {
                        if (sessionIssue || accessDenied) {
                            window.location.reload();
                        } else {
                            programTable.DataTable().ajax.reload();
                        }
                    }
                }
            });
        }
    });

    programTable.DataTable({
        pageLength: 10,
        ordering: true,
        serverSide: true,
        processing: true,
        searching: true,
        responsive: true,
        language: {
            paginate: {
                previous: "<i class='fa-solid fa-angles-left'></i>",
                next: "<i class='fa-solid fa-angles-right'></i>",
            }
        },
        ajax: $('#programIndexRoute').val(),
        dom: '<>tr<"tableBottom"<"row align-items-center"<"col-sm-6"<"tableInfo"i>><"col-sm-6"<"tablePagi"p>>>><"clear">',
        columns: [
            {"data": 'DT_RowIndex', "name": 'DT_RowIndex', searchable: false},
            {"data": "title", "name": "title"},
            {"data": "status", "name": "status", searchable: false},
            {"data": "action", searchable: false, responsivePriority: 2},
        ]
    });
})(jQuery);
