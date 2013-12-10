<div class="col-md-9 input-group">
{if $readonly}  <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}" />
    {$value}
{else}<textarea name="fm_{$fd_field}" id="fm_{$fd_field}" rows="{$rows}" cols="{$cols}" {if $counter > 0}onkeydown="countDown('fm_{$fd_field}','fm_{$fd_field}_counter',{$counter});" onkeyup="countDown('fm_{$fd_field}','fm_{$fd_field}_counter',{$counter});"{/if} class="form-control" title="{$fd_help}">{$value}</textarea>{if $counter > 0}
    <span class="input-group-addon" id="fm_{$fd_field}_counter">{$counterstrlen}</span>{/if}
{/if}</div>