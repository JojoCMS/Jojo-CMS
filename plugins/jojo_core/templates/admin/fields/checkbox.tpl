<input type="checkbox"
       value=""
{if $value == $trueoption}       checked="checked" {/if}
{if $readonly == "yes"}       readonly="readonly" {/if}
       title="{$fd_help}"
       onclick="document.getElementById('fm_{$fd_field}').value = (this.checked) ? '{$trueoption}' : '{$falseoption}';{if $show_on_true || $show_on_false}if ($(this).attr('checked')) {ldelim}{foreach from=$show_on_true item=t}$('#row_{$t}').show();{/foreach}{foreach from=$show_on_false item=f}$('#row_{$f}').hide();{/foreach}{rdelim} else {ldelim}{foreach from=$show_on_true item=t}$('#row_{$t}').hide();{/foreach}{foreach from=$show_on_false item=t}$('#row_{$f}').show();{/foreach}{rdelim}{/if}"
        />
<input type="hidden" id="fm_{$fd_field}" name="fm_{$fd_field}" value="{$value}" />