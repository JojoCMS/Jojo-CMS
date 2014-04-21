{if $readonly}<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}">
{/if}
<div class="col-md-12">
    <select class="form-control{if $error!=''} error{/if}" style="width:280px" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$rows}" {if $readonly}readonly="readonly"{/if} title="{$fd_help}">
    {foreach from=$_types key=id item=name}
        <option value="{$id}"{if $id == $value}selected="selected"{/if}>{$name}</option>
    {/foreach}
    </select>
</div>