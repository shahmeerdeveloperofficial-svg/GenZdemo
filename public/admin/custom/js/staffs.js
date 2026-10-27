(function ($) {
    "use strict";

    $('#toggle-staff-role-creator').on('click', function () {
        $('#staff-role-creator').toggleClass('d-none');
    });

    $('#cancel-staff-role-creator').on('click', function () {
        $('#staff-role-creator').addClass('d-none');
        $('#staff-role-creator-form')[0].reset();
        $('#staff-role-creator-form .error-message').remove();
        $('#staff-role-creator-form .is-invalid').removeClass('is-invalid');
    });

    window.staffRoleCreatedHandler = function (response) {
        var $form = $('#staff-role-creator-form');
        var $submit = $form.find(':submit');
        $submit.prop('disabled', false).find('.spinner-border').addClass('d-none');
        $form.find('.error-message').remove();
        $form.find('.is-invalid').removeClass('is-invalid');

        if (response && response.status === true && response.data && response.data.role) {
            var role = response.data.role;
            var inputId = 'staff-role-' + role.id;
            var $input = $('<input>', {
                type: 'checkbox',
                class: 'form-check-input',
                name: 'roles[]',
                id: inputId,
                value: role.name,
                role: 'switch',
                checked: true
            });
            var $label = $('<label>', {
                class: 'zForm-label-alt',
                for: inputId,
                text: role.name
            });
            var $option = $('<div>', { class: 'staff-role-option' })
                .append($('<div>', { class: 'zCheck form-switch' }).append($input, $label));

            $('#staff-role-options').append($option);
            $('#staff-no-roles-note').remove();
            $form[0].reset();
            $('#staff-role-creator').addClass('d-none');
            if (window.toastr) toastr.success(response.message || 'Role created and selected.');
            return;
        }

        var xhr = response && response.responseJSON ? response : null;
        if (xhr && xhr.responseJSON.errors && window.getValidationError) {
            toastr.error(getValidationError(xhr.responseJSON.errors));
        } else {
            toastr.error((xhr && xhr.responseJSON && xhr.responseJSON.message) || (response && response.message) || 'Could not create the role.');
        }
    };

    $('#staffDataTable').DataTable({
        pageLength: 10,
        ordering: true,
        serverSide: true,
        processing: true,
        searching: true,
        responsive: true,
        ajax: $('#staff-route').val(),
        language: {
            paginate: {
                previous: "<i class='fa-solid fa-angles-left'></i>",
                next: "<i class='fa-solid fa-angles-right'></i>",
            },
        },
        dom: '<>tr<"tableBottom"<"row align-items-center"<"col-sm-6"<"tableInfo"i>><"col-sm-6"<"tablePagi"p>>>><"clear">',
        columns: [
            {"data": 'DT_RowIndex', "name": 'DT_RowIndex', searchable: false},
            {"data": "name", "name": "name", responsivePriority: 1},
            {"data": "email", "name": "email"},
            {"data": "mobile", "name": "mobile"},
            {"data": "roles", "name": "roles"},
            {"data": "status", "name": "status", searchable: false},
            {"data": "action", searchable: false, responsivePriority: 2},
        ]
    });

})(jQuery)
