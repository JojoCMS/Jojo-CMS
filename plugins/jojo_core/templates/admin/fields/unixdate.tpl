{if $readonly}
    <input type="hidden" size="10" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$formatteddate}" />
{else}
    <input type="text" size="10" name="fm_{$fd_field}" id="fm_{$fd_field}" {if $error != ""}class="error"{/if} value="{$formatteddate}"  title="{$fd_help}" onblur="magicDate(this)" onfocus="if (this.className != 'error') this.select()" />
    <img src="images/cms/icons/calendar_view_day.png" border="0" alt="Set to Today's date" onclick="magicDateIncrement(document.getElementById('fm_{$fd_field}'),0)" />
    <img src="images/cms/incrementup.gif" border="0" alt="Increase value" onclick="magicDateIncrement(document.getElementById('fm_{$fd_field}'),1)" />
    <img src="images/cms/incrementdown.gif" border="0" alt="Decrease value" onclick="magicDateIncrement(document.getElementById('fm_{$fd_field}'),-1)" />
{/if}
<span id="fm_{$fd_field}Msg">{if $printabledate}{$printabledate}{else}&nbsp;{/if}</span>