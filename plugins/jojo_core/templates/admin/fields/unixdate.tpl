{if $readonly}
    <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$formatteddate}" />
{else}
    <input type="text" size="25" name="fm_{$fd_field}" id="fm_{$fd_field}" {if $error != ""}class="error"{/if} value="{$formatteddate}"  title="{$fd_help}" />
{/if}
<span id="fm_{$fd_field}Msg">{if $printabledate}{$printabledate}{else}&nbsp;{/if}</span>