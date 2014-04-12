    {if $readonly}<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}" />
    {$value}
    {else}<input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$fd_size}" title="{$fd_help}" value="{$value}"  />
    {/if}