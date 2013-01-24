{if $readonly}
    <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}"  size="{$fd_size}" value="{$value}" />
    {$value}
{else}
    <input type="text" name="fm_{$fd_field}{if $confirm}[1]{/if}" id="fm_{$fd_field}"  size="{$fd_size}" value="{$value}" onchange="if ((this.value!='') && (!validate(this.value,'email'))) alert('The email format is not valid');" title="{$fd_help}" />
    {if $confirm}<br />
    <input type="text" name="fm_{$fd_field}[2]" id="fm_{$fd_field}_confirm"  size="{$fd_size}" value="{$value}" onchange="if ((this.value!='') && (!validate(this.value,'email'))) alert('The email format is not valid');" title="{$fd_help}" />{/if}
    {*{if $value}<a href="mailto:{$value}"><img src="images/cms/icons/email.png" border="0" alt="" title="Mail {$value}" /></a>{/if}*}
{/if}
