{if !$readonly && $fd_units}<div class="input-group{if $error} has-error{/if}">{/if}
{if $readonly}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size}" value="{$value}"  />
{$value}
{else}
    <input class="form-control" type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size}" value="{$value}" title="{$fd_help}" />{if $fd_units}
    <span class="input-group-addon" >{$fd_units}</span>{/if}
{/if}
{if !$readonly && $fd_units}</div>{/if}