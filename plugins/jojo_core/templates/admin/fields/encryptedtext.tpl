<div class="{if $fd_size>=40}col-md-12{elseif $fd_size>10}col-md-8{else}col-md-4{/if}">
    {if $readonly}
    <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$fd_size}" value="{$value}"/>
    {$value}
    {else}
    <input class="form-control" type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$fd_size}" value="{$value}" {if $error != ""}class="error"{/if}  title="{$fd_help}" />
    {/if}
</div>