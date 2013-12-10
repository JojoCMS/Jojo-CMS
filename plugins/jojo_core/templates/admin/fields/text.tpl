<div class="{if $fd_size>=40}col-md-12{elseif $fd_size>10}col-md-8{else}col-md-4{/if}{if !$readonly && ($counter > 0 || $fd_units)} input-group{/if}{if $error} has-error{/if}">
{if $readonly}<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}" />
    {if $value}{$value}{/if}{if $fd_units} {$fd_units}{/if}
{else}  <input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$fd_size}" value="{$value}"  class="form-control" {if $fd_maxsize > 0}maxlength="{$fd_maxsize}"{/if}{if $counter > 0} onkeydown="countDown('fm_{$fd_field}', 'fm_{$fd_field}_counter', {$counter});" onkeyup="countDown('fm_{$fd_field}','fm_{$fd_field}_counter', {$counter});"{/if}{if $readonly == "yes"} readonly="readonly"{/if} title="{$fd_help}" />
{if $counter > 0}   <span class="input-group-addon" id="fm_{$fd_field}_counter">{$counterstrlen}</span>{/if}
{if $fd_units}  <span class="input-group-addon" >{$fd_units}</span>{/if}
{if $counter > 0}<script type="text/javascript">countDown('fm_{$fd_field}','fm_{$fd_field}_counter',{$counter});</script>{/if}
{/if}</div>
