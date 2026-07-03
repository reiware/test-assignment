import './bootstrap';
import 'bootstrap';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

if (csrfToken) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        }
    });
}

function showAlert(type, message) {
    $('#alert')
        .removeClass('d-none alert-success alert-danger')
        .addClass('alert-' + type)
        .text(message);
}

function getEmptyRowHtml() {
    return $('#empty-row-template').html();
}

$(function () {
    const uploadForm = $('#upload-form');
    const fileError = $('#file-error');

    function showValidationErrors(errors) {
        const messages = [];

        Object.values(errors).forEach((fieldErrors) => {
            fieldErrors.forEach((message) => {
                if (!messages.includes(message)) {
                    messages.push(message);
                }
            });
        });

        fileError.html(messages.join('<br>'));
    }

    uploadForm.on('submit', function (event) {
        event.preventDefault();

        fileError.text('');
        $('#alert').addClass('d-none').text('');

        const formData = new FormData(this);
        const submitButton = uploadForm.find('button[type="submit"]');

        submitButton.prop('disabled', true);

        $.ajax({
            url: uploadForm.data('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                showAlert('success', response.message);
                uploadForm[0].reset();
            },
            error: function (xhr) {
                fileError.text('');

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    showAlert('danger', 'Please fix the upload errors.');
                    showValidationErrors(xhr.responseJSON.errors);
                    return;
                }

                if (xhr.status === 413) {
                    showAlert('danger', 'Upload failed.');
                    fileError.text('The selected files are too large.');
                    return;
                }


                const message = xhr.responseJSON?.message || 'Upload failed. Please try again.';
                showAlert('danger', message);
            },
            complete: function () {
                submitButton.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.delete-file', function () {
        if (!confirm('Delete this file?')) {
            return;
        }

        const button = $(this);
        const url = button.data('url');
        const id = button.data('id');

        button.prop('disabled', true);

        $.ajax({
            url: url,
            method: 'DELETE',
            success: function (response) {
                showAlert('success', response.message);
                $('#file-' + id).remove();

                if ($('#files-table tbody tr').length === 0) {
                    $('#files-table tbody').html(getEmptyRowHtml());
                }
            },
            error: function (xhr) {
                const message = xhr.responseJSON?.message || 'Delete failed.';

                showAlert('danger', message);
                button.prop('disabled', false);
            }
        });
    });
});
