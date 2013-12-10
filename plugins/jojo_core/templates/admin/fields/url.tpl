<div class="col-md-9">
{if $readonly}  <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}"  size="{$fd_size}" value="{$value}" />
    {$value}
{else}
    <input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}"  size="{$fd_size}" class="form-control" value="{$value}" onchange="validate(this.value,'url')"  title="{$fd_help}" />
{/if}
</div>
