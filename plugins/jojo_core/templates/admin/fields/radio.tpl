{if $readonly}
    <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}" />
{/if}
{assign var=first value=true}
<div class="col-md-9">
{foreach from=$vals key=k item=val}
    <label for="fm_{$fd_field}{if !$first}_{$val}{/if}" class="radio-inline">
        <input type="radio" name="fm_{$fd_field}" id="fm_{$fd_field}{if !$first}_{$val}{/if}" value="{$val}" {if $value == $val}checked = "checked"{/if} {if $readonly}readonly="readonly"{/if} title="{$fd_help}" {if $allextras}onclick="{foreach from=$allextras item=ae}$('#row_{$ae}').hide();{/foreach}{if $extras.$k}$('#row_{$extras.$k}').show();{/if}" {/if}/>{$displayvals.$k}
    </label>
{assign var=first value=false}
{/foreach}
</div>