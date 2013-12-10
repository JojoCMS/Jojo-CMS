{if $readonly}
    <input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size}" value="{if $value}{$value}{/if}" />
    {if $value}{$value}{/if}{if $units}{$units}{/if}
{else}
<div class="{if $fd_size>=40}col-md-9{elseif $fd_size>10}col-md-6{else}col-md-3{/if} input-group">
    <input class="form-control" type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size}" value="{if $value}{$value}{/if}" onchange="if (!validate(this.value,'integer')) alert('Please enter an integer value');" title="{$fd_help}" />
    <span class="input-group-addon order">
        <img src="images/cms/incrementup.gif" style="border:0" alt="Increase value" title="Increase value" onclick="if (isNaN(document.getElementById('fm_{$fd_field}').value)) {literal}{{/literal}document.getElementById('fm_{$fd_field}').value = '0';{literal}}{/literal} if (document.getElementById('fm_{$fd_field}').value == '') {literal}{{/literal}document.getElementById('fm_{$fd_field}').value = '0';{literal}}{/literal} document.getElementById('fm_{$fd_field}').value = parseInt(document.getElementById('fm_{$fd_field}').value) + 1;" />
        <img src="images/cms/incrementdown.gif" style="border:0" alt="Decrease value" title="Decrease value" onclick="document.getElementById('fm_{$fd_field}').value = document.getElementById('fm_{$fd_field}').value - 1;" />
    </span>{if $units}<span class="input-group-addon" >{$units}</span>{/if}
</div>
{/if}