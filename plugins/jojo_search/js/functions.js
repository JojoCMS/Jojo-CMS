$(document).ready(function () {
    $('.search .q').each( function(index) {
        $(this).bind('focus', function() {
            $(this).val($(this).val() == 'Search' ? '' : $(this).val()).css('font-style', 'normal');
        })
        .bind('blur', function() {
            $(this).val($(this).val() == '' ? 'Search' : $(this).val());
        })
        .trigger('blur')
        .bind('keypress', function(e) {
                if (e.keyCode == 13) {
                    $(this).parent().submit();
                }
        });
    });
});
