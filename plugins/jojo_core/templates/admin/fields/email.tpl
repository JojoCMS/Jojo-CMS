{if $readonly}
    <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}"  size="{$fd_size}" value="{$value}" />
    {$value}
{else}
    <div class="{if $fd_size>=40}col-md-7{elseif $fd_size>10}col-md-4{else}col-md-1{/if}">
        <input class="form-control" type="text" name="fm_{$fd_field}{if $confirm}[1]{/if}" id="fm_{$fd_field}"  size="{$fd_size}" value="{$value}" onchange="if ((this.value!='') && (!validate(this.value,'email'))) alert('The email format is not valid');" title="{$fd_help}" />
        {if $confirm}<br />
        <input type="text" name="fm_{$fd_field}[2]" id="fm_{$fd_field}_confirm"  size="{$fd_size}" value="{$value}" onchange="if ((this.value!='') && (!validate(this.value,'email'))) alert('The email format is not valid');" title="{$fd_help}" />
        {/if}
    </div>
{/if}
