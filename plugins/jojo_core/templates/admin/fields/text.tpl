{if $readonly}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}" />
{if $value}{$value}{/if}{if $fd_units} {$fd_units}{/if}
{else}
<div class="{if $fd_size>=40}col-md-5{elseif $fd_size>10}col-md-3{else}col-md-1{/if}">
{if $counter > 0}
<input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$fd_size}" value="{$value}"  class="form-control" {if $fd_maxsize > 0}maxlength="{$fd_maxsize}"{/if} {if $counter > 0}onkeydown="countDown('fm_{$fd_field}', 'fm_{$fd_field}_counter', {$counter});" onkeyup="countDown('fm_{$fd_field}','fm_{$fd_field}_counter', {$counter});"{/if} {if $readonly == "yes"}readonly="readonly"{/if} title="{$fd_help}" />{if $fd_units} {$fd_units}{/if}
</div>
<div class="col-md-1">
<input readonly="readonly" type="text" class="counter form-control" name="fm_{$fd_field}_counter" id="fm_{$fd_field}_counter" size="3" maxlength="3" value="{$counterstrlen}" />
<script type="text/javascript">countDown('fm_{$fd_field}','fm_{$fd_field}_counter',{$counter});</script>
{else}
<input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$fd_size}" value="{$value}" class="form-control"  title="{$fd_help}" />{if $fd_units} {$fd_units}{/if}
{/if}
</div>
{/if}