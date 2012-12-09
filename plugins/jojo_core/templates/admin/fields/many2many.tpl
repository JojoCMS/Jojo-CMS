{if !$parent}{$parent=0}{$indent=""}{/if}
{if $readonly}
    <p>{count($selections)} linked</p>
    {foreach $records r}
        {if in_array($r.id, $selections)}<li>{$r.display}</li>{/if}
    {/foreach}
{else}
    {if !$parent}<input type="hidden" value="1" name="fm_{$fieldname}">{/if}
    {foreach $records r}{if $r.parent == $parent}
        <label>{$indent}<input type='checkbox' name='fm_{$fieldname}_{$r.id}' id='fm_{$fieldname}_{$r.id}' value='{$r.id}' onchange='fullsave = true;'{if in_array($r.id, $selections)} checked="checked"{/if} /> {$r.display}</label><br />
            {include file="admin/fields/many2many.tpl" parent=$r.id indent="$indent&nbsp;&nbsp;&nbsp;&nbsp;"}
    {/if}{/foreach}
{/if}
