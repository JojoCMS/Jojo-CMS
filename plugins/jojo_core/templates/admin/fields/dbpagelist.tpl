{if $readonly}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}" />
{if $readonlydisplay}{$readonlydisplay}{/if}
{else}
<select name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$rows}"  class="span5" {if $readonly}disabled{/if}  title="{$fd_help}">
{$hktree}
</select>
{/if}
