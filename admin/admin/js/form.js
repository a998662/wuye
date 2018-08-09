

function formCheck(rules, messages) {
    $('.form-horizontal').validate({
        ignore: [],
        errorClass: 'help-block animated fadeInDown',
        errorElement: 'div',
        focusInvalid: false,
        errorPlacement: function (error, e) {
            jQuery(e).parents('.form-group > div').append(error);
        },
        highlight: function (e) {
            var elem = jQuery(e);
            elem.closest('.form-group').removeClass('has-error').addClass('has-error');
            elem.closest('.help-block').remove();
        },
        success: function (e) {
            var elem = jQuery(e);
            elem.closest('.form-group').removeClass('has-error').addClass('has-success');
            elem.closest('.help-block').remove();
        },
        rules: rules,
        messages: messages,
        submitHandler: function () {
            sub();
        }
    });
}

// 清空图标
$('.delete-icon').click(function (event) {
    event.stopPropagation();
    if ($(this).prev().is(':disabled')) {
        return;
    }
    $(this).prev().val('');
    $(this).prev().prev().html('<i class="fa fa-fw fa-plus-circle"></i>');
});


