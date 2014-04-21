<div class="{if $fd_size>=40}col-md-12{elseif $fd_size>10}col-md-8{else}col-md-4{/if} input-group">
    {if $readonly}<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size}" value="{$value}" />
    {$value}
    {else}
    <input class="form-control" type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size}" value="{$value}" onchange="validate(this,this.value,'{$fd_type}')"  title="{$fd_help}" />
    <span class="input-group-addon order">
        <img src="images/cms/incrementup.gif" style="border: 0; cursor: pointer;" alt="Increase value" onclick="$('#fm_{$fd_field}').val(isNaN($('#fm_{$fd_field}').val()) || $('#fm_{$fd_field}').val()=='' ? '0' : parseInt($('#fm_{$fd_field}').val())+1);" />
        <img src="images/cms/incrementdown.gif" style="border: 0; cursor: pointer;" alt="Decrease value" onclick="$('#fm_{$fd_field}').val(isNaN($('#fm_{$fd_field}').val()) || $('#fm_{$fd_field}').val()=='' || $('#fm_{$fd_field}').val()=='0' ? '0' : parseInt($('#fm_{$fd_field}').val())-1);" />
    </span>
    {/if}
</div>
