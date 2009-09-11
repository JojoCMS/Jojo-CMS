{if $readonly == "yes"}
    <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}" />
{/if}

<textarea name="fm_{$fd_field}" id="fm_{$fd_field}" rows="{$rows}" cols="{$cols}" {if $counter > 0}onkeydown="countDown('fm_{$fd_field}','fm_{$fd_field}_counter',{$counter});" onkeyup="countDown('fm_{$fd_field}','fm_{$fd_field}_counter',{$counter});"{/if}{if $readonly == "yes"}readonly="readonly"{/if} title="{$fd_help}">{$value}</textarea>

{if $counter > 0}
<input readonly="readonly" type="text" class="counter" name="fm_{$fd_field}_counter" id="fm_{$fd_field}_counter" size="3" maxlength="3" value="{$counterstrlen}" />
{/if}