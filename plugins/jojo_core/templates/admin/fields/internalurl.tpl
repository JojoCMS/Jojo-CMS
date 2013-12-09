{if $readonly}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}" />
{$url}{if $value}{$value}/{/if}
{else}
<div class="col-md-7">
{$url}<span class="col-md-7 inline"><input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size|default:20}" value="{if $value}{$value}{/if}" class="form-control" {if $fd_maxsize > 0}maxlength="{$fd_maxsize}" {/if}title="{$fd_help}" /></span>/
</div>
{/if}