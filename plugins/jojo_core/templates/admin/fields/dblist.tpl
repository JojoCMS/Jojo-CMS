{if $readonly}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}">
{/if}
<select name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$rows}" class="form-control" {if $readonly}disabled{/if}  title="{$fd_help}">
{$hktree}
</select>