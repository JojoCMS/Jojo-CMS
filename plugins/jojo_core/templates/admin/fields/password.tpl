<div class="{if $error}has-error{/if}">
    <input type="hidden" name="fm_{$fd_field}[orig]" id="fm_{$fd_field}[orig]" value="{$value}" readonly="readonly" />
    <input class="form-control" type="password" name="fm_{$fd_field}[1]" id="fm_{$fd_field}[1]"  size="{$fd_size}" value="" {if $readonly}readonly="readonly"{/if} title="{$fd_help}" />
</div>
<div class="input-group">
    <input class="form-control" type="password" name="fm_{$fd_field}[2]" id="fm_{$fd_field}[2]"  size="{$fd_size}" value="" {if $readonly}readonly="readonly"{/if} title="{$fd_help}" /><span class="input-group-addon">confirm</span>
</div>