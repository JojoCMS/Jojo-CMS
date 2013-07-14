        {if $readonly}
            <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}">
        {/if}


        <select name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$rows}" class="span5" {if $readonly}readonly="readonly"{/if} title="{$fd_help}">
            <option value=""{if !$value} selected="selected"{/if}>No Plugin</option>
        {foreach from=$_types key=id item=name}
            <option value="{$id}"{if $id == $value}selected="selected"{/if}>{$name}</option>
        {/foreach}
        </select>