<div class="{if $fd_size>=40}col-md-9{elseif $fd_size>10}col-md-6{else}col-md-3{/if} input-group">
{if $readonly}
     <input type="hidden" size="10" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$formatteddate}" onblur="magicDate(this)" onfocus="if (this.className != 'error') this.select()" />
{else}
   <input class="form-control" type="text" size="10" name="fm_{$fd_field}" id="fm_{$fd_field}"  value="{$formatteddate}" {if $error != ""}class="error"{/if}  title="{$fd_help}" onfocus="if (this.className != 'error') this.select()" />
{/if}
</div>
<span id="fm_{$fd_field}Msg" style="display:none">{if $printabledate}{$printabledate}{else}&nbsp;{/if}</span>