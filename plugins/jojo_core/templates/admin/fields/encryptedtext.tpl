{if $readonly}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$fd_size}" value="{$value}"/>
{$value}
{else}
<input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$fd_size}" value="{$value}" {if $error != ""}class="error"{/if}  title="{$fd_help}" />
{/if}