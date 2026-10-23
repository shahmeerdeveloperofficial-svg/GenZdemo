(function ($) {
    "use strict";

    $('#freeConsultationDataTable').DataTable({
        pageLength: 10,
        ordering: true,
        serverSide: true,
        processing: true,
        searching: true,
        responsive: true,
        ajax: $('#free-consultations-route').val(),
        order: [], // The server supplies newest-first until the user chooses a sort.
        language: {
            paginate: {
                previous: "<i class='fa-solid fa-angles-left'></i>",
                next: "<i class='fa-solid fa-angles-right'></i>",
            },
        },
        dom: '<>tr<"tableBottom"<"row align-items-center"<"col-sm-6"<"tableInfo"i>><"col-sm-6"<"tablePagi"p>>>><"clear">',
        columns: [
            {"data": 'DT_RowIndex', "name": 'DT_RowIndex', searchable: false, orderable: false},
            {"data": "name", "name": "name", responsivePriority: 2},
            {"data": "email", "name": "email"},
            {"data": "mobile", "name": "mobile"},
            {"data": "study_level", "name": "study_level.name"},
            {"data": "status", "name": "status", searchable: false},
            {"data": "action", searchable: false, orderable: false, responsivePriority: 2},
        ]
    });

})(jQuery)
