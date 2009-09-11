{literal}
if (typeof jQuery !== 'undefined') {
    $(function() {
        if (!$.browser.msie || !(parseInt($.browser.version.substr(0,1))<7)) {
            $("#fm_{/literal}{$fd_field}{literal}").date_input();
        }
    });
} else {
    if (!parent.jQuery.browser.msie || !(parseInt(parent.jQuery.browser.version.substr(0,1))<7)) {
        parent.jQuery("#fm_{/literal}{$fd_field}{literal}").date_input();
    }
}
{/literal}