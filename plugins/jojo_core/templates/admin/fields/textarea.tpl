{if $readonly}
    <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}" />
    {$value}
{else}
<textarea name="fm_{$fd_field}" id="fm_{$fd_field}" rows="{$rows}" cols="{$cols}" {if $counter > 0}onkeydown="countDown('fm_{$fd_field}','fm_{$fd_field}_counter',{$counter});" onkeyup="countDown('fm_{$fd_field}','fm_{$fd_field}_counter',{$counter});"{/if} class="span5" title="{$fd_help}">{$value}</textarea>
{if $counter > 0}
<input readonly="readonly" type="text" class="counter" name="fm_{$fd_field}_counter" id="fm_{$fd_field}_counter" size="3" maxlength="3" value="{$counterstrlen}" />
{/if}
{/if}