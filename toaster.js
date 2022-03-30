setTimeout(() => {
    $('#toaster-wrapper>.toast').fadeOut();
}, 10000);

$(document).ready(function () {
    $('.toaster .toaster-head').on('click', '.toaster-close', function () {
        $(this).parent().parent().fadeOut();
    })
})