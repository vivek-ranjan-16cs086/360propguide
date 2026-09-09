$("#loginEye").click(function () {
    $(this).toggleClass('text-primary');
    ($("#password").attr('type') == 'text') ? $("#password").prop('type', 'password') : $("#password").prop('type', 'text');
});