{if $readonly}
     <input type="hidden" size="10" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$formatteddate}" onblur="magicDate(this)" onfocus="if (this.className != 'error') this.select()" />
{else}
<div class="{if $fd_size>=40}col-md-9{elseif $fd_size>10}col-md-5{else}col-md-2{/if} input-group">
   <input class="form-control" type="text" size="10" name="fm_{$fd_field}" id="fm_{$fd_field}"  value="{$formatteddate}" {if $error != ""}class="error"{/if}  title="{$fd_help}" onfocus="if (this.className != 'error') this.select()" />
</div>
{/if}
<span id="fm_{$fd_field}Msg" style="display:none">{if $printabledate}{$printabledate}{else}&nbsp;{/if}</span>