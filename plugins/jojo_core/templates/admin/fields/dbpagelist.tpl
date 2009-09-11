{if $readonly == "yes"}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}">
{/if}

<select style="width:280px" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$rows}" {if $error != ""}class="error"{/if} {if $readonly == "yes"}readonly="readonly"{/if}  title="{$fd_help}">
{$hktree}
</select>