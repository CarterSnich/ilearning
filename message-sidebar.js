$(function() {

    var url = new URL(window.location.href);
    if (url.searchParams.get("chat")) {
        $('body').addClass('body-mod');
        $('#message-sidebar-wrapper').addClass('message-sidebar-show');
        $('#message-sidebar textarea').focus();
    }

    $('#message-sidebar').animate({ scrollTop: $('#message-sidebar').prop("scrollHeight") }, 1000);

    $('#view-chat-sidebar').on('click', function(e) {
        e.preventDefault();
        $('body').addClass('body-mod');
        $('#message-sidebar-wrapper').addClass('message-sidebar-show');
        $('#message-sidebar textarea').focus();
    })

    $('#close-message-sidebar').on('click', function() {
        $('body').removeClass('body-mod');
        $('#message-sidebar-wrapper').removeClass('message-sidebar-show');
    })


    $('form#chat-box-reply-form').on('submit', function(e) {
        if ($('form#chat-box-reply-form>textarea').val() == '') e.preventDefault();
    })

})