$(document).ready(function () {
    $('#search #q')
        .bind('focus', function() {
            $('#search #q').val($('#search #q').val() == 'Search' ? '' : $('#search #q').val());
        })
        .bind('blur', function() {
            $('#search #q').val($('#search #q').val() == '' ? 'Search' : $('#search #q').val());
        })
        .trigger('blur')
        .bind('keypress', function(e) {
                if (e.keyCode == 13) {
                    $('#search').submit();
                }
        })
        ;
});
