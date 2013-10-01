{if $readonly}
    <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}" />{* this has not been tested. Hidden fields may not exist *}
{/if}
{assign var=first value=true}
<input type="hidden" name="fm_{$fd_field}" value="" />{* this field must exist in order for the setValue function to run *}
{foreach name=checkboxes from=$vals key=k item=val}
    <label class="checkbox-inline">
        <input type="checkbox" name="fm_{$fd_field}_{$smarty.foreach.checkboxes.index}" id="fm_{$fd_field}_{$smarty.foreach.checkboxes.index}" value="{$val}"{if $checked.$k} checked = "checked"{/if} {if $readonly}readonly="readonly"{/if} title="{$fd_help}" />{$displayvals.$k}
    </label>
{assign var=first value=false}
{/foreach}