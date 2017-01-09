{literal}
if (typeof jQuery !== 'undefined') {
    $(function() {
            $("#fm_{/literal}{$fd_field}{literal}").jdPicker({date_format:"dd MM YYYY"});
    });
} else {
        parent.jQuery("#fm_{/literal}{$fd_field}{literal}").jdPicker({date_format:"dd MM YYYY"});
}
{/literal}