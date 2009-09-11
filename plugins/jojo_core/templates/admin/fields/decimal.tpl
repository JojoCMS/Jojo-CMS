<span title="{$fd_help}">
    <input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$size}" value="{$value}" onchange="validate(this,this.value,'{$fd_type}')" {if $readonly == "yes"}readonly="readonly"{/if} title="{$fd_help}" />
    {$onlyIfUnits}
</span>