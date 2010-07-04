<span title="{$fd_help}">
{if $readonly}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size}" value="{$value}"  />
{$value}
{else}
<input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size}" value="{$value}" onchange="validate(this,this.value,'{$fd_type}')"  title="{$fd_help}" />
{/if}
{$onlyIfUnits}
</span>