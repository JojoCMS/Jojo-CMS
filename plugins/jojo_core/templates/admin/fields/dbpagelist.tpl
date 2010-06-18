{if $readonly}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}">
{if $readonlydisplay}{$readonlydisplay}{/if}
{else}
<select style="width:280px" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$rows}" {if $error != ""}class="error"{/if} {if $readonly}disabled{/if}  title="{$fd_help}">
{$hktree}
</select>
{/if}
