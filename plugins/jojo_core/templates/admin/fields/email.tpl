{if $readonly}
    <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}"  size="{$fd_size}" value="{$value}" />
    {$value}
{else}
    <input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}"  size="{$fd_size}" value="{$value}" onchange="if ((this.value!='') && (!validate(this.value,'email'))) alert('The email format is not valid');" title="{$fd_help}" />
    {if $value}<a href="mailto:{$value}"><img src="images/cms/icons/email.png" border="0" alt="" title="Mail {$value}" /></a>{/if}
{/if}