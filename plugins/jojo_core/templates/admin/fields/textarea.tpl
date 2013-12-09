{if $readonly}
    <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}" />
    {$value}
{else}
<div class="col-md-7">
<textarea name="fm_{$fd_field}" id="fm_{$fd_field}" rows="{$rows}" cols="{$cols}" {if $counter > 0}onkeydown="countDown('fm_{$fd_field}','fm_{$fd_field}_counter',{$counter});" onkeyup="countDown('fm_{$fd_field}','fm_{$fd_field}_counter',{$counter});"{/if} class="form-control" title="{$fd_help}">{$value}</textarea>
{if $counter > 0}
</div>
<div class="col-md-2">
<input readonly="readonly" type="text" class="counter form-control" name="fm_{$fd_field}_counter" id="fm_{$fd_field}_counter" size="3" maxlength="3" value="{$counterstrlen}" />
{/if}
</div>
{/if}