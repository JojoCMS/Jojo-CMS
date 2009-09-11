{if $readonly != "yes"}
    <input type="text" size="10" name="fm_{$fd_field}" id="fm_{$fd_field}"  value="{$formatteddate}" {if $error != ""}class="error"{/if}  title="{$fd_help}" onfocus="if (this.className != 'error') this.select()" />
    {if $fd_units} {$fd_units}{/if}
{else}
    <input type="hidden" size="10" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$formatteddate}" onblur="magicDate(this)" onfocus="if (this.className != 'error') this.select()" {if $readonly == "yes"}readonly="readonly"{/if} />
{/if}
<span id="fm_{$fd_field}Msg" style="display:none">{if $printabledate}{$printabledate}{else}&nbsp;{/if}</span>