$(document).ready(function () {

    // Enable/disable send button based on textarea input
    $('#msg').on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';

        if ($(this).val().trim().length > 0) {
            $('#sendBtn').removeAttr('disabled');
        } else {
            $('#sendBtn').attr('disabled', 'disabled');
        }
    });

    // Automatically focus and resize textarea on focus
    $('#msg').focus(function () {
        $(this).css({ height: 'auto' });
    }).blur(function () {
        if (!$(this).val().trim()) {
            $(this).css({ Height: 'auto' });
        }
    });
});
