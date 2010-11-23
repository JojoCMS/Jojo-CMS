{if $readonly}
    <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$formatteddate}" />
{else}
    <input type="text" size="25" name="fm_{$fd_field}" id="fm_{$fd_field}" class="date{if $error != ""} error{/if}" value="{$formatteddate}"  title="{$fd_help}" /> <a href="#" onclick="$('#fm_{$fd_field}').val('');return false;">clear</a> 
{/if}
 &nbsp;<span id="fm_{$fd_field}Msg">{if $printabledate}{$printabledate}{else}&nbsp;{/if}</span>