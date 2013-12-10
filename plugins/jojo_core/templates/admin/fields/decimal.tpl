<div class="{if $fd_size>=40}col-md-12{elseif $fd_size>10}col-md-8{else}col-md-4{/if}{if $fd_units} input-group{/if}">
{if $readonly}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size}" value="{$value}"  />
{$value}
{else}
    <input class="form-control" type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size}" value="{$value}" onchange="validate(this,this.value,'{$fd_type}')"  title="{$fd_help}" />{if $fd_units}
    <span class="input-group-addon" >{$fd_units}</span>{/if}
{/if}
</div>
