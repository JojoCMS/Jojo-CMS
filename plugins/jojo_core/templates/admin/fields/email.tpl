<div class="{if $fd_size>=40}col-md-12{else}col-md-6{/if}">
{if $readonly}
    <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}"  size="{$fd_size}" value="{$value}" />
    {$value}
{else}
    <input class="form-control" type="text" name="fm_{$fd_field}{if $confirm}[1]{/if}" id="fm_{$fd_field}"  size="{$fd_size}" value="{$value}" onchange="if ((this.value!='') && (!validate(this.value,'email'))) alert('The email format is not valid');" title="{$fd_help}" />
{if $confirm}
</div>
<div class="{if $fd_size>=40}col-md-12{else}col-md-6{/if} input-group">
     <input class="form-control"type="text" name="fm_{$fd_field}[2]" id="fm_{$fd_field}_confirm"  size="{$fd_size}" value="{$value}" onchange="if ((this.value!='') && (!validate(this.value,'email'))) alert('The email format is not valid');" title="{$fd_help}" /><span class="input-group-addon">confirm</span>
{/if}{/if}
</div>
