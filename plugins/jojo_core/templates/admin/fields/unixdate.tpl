<div class="input-group">
    {if $readonly}<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$formatteddate}" />
    {else}<input class="form-control date" type="text" size="25" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$formatteddate}" title="{$fd_help}" /><span class="input-group-addon"><a href="#" onclick="$('#fm_{$fd_field}').val('');return false;">clear</a></span>
    {/if}
</div>