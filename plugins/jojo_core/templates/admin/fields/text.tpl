{if $readonly}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}" />
{if $value}{$value}{/if}{if $fd_units} {$fd_units}{/if}
{else}
<div class="{if $fd_size>=40}col-md-9{elseif $fd_size>10}col-md-5{else}col-md-2{/if}{if $fd_units || $counter > 0} input-group{/if}">
{if $counter > 0}
    <input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$fd_size}" value="{$value}"  class="form-control" {if $fd_maxsize > 0}maxlength="{$fd_maxsize}"{/if} {if $counter > 0}onkeydown="countDown('fm_{$fd_field}', 'fm_{$fd_field}_counter', {$counter});" onkeyup="countDown('fm_{$fd_field}','fm_{$fd_field}_counter', {$counter});"{/if} {if $readonly == "yes"}readonly="readonly"{/if} title="{$fd_help}" />{if $fd_units} {$fd_units}{/if}
    <span class="input-group-addon" id="fm_{$fd_field}_counter">{$counterstrlen}</span>
</div>
<script type="text/javascript">countDown('fm_{$fd_field}','fm_{$fd_field}_counter',{$counter});</script>
{else}
    <input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$fd_size}" value="{$value}" class="form-control" title="{$fd_help}" />{if $fd_units}<span class="input-group-addon">{$fd_units}</span>{/if}
</div>
{/if}
{/if}