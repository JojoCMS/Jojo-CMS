{literal}
if (typeof jQuery !== 'undefined' && $("#fm_{/literal}{$fd_field}{literal}").length>0) {
    $(function() {
            $("#fm_{/literal}{$fd_field}{literal}").jdPicker({date_format:"dd MM YYYY"});
    });
} else if (parent.jQuery("#fm_{/literal}{$fd_field}{literal}").length > 0) {
        parent.jQuery("#fm_{/literal}{$fd_field}{literal}").jdPicker({date_format:"dd MM YYYY"});
}
{/literal}