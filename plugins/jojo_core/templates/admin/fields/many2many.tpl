{if $readonly}
    <p>{count($selections)} linked</p>
    {foreach $records r}
        {if in_array($r.id, $selections)}<li>{$r.display}</li>{/if}
    {/foreach}
{else}
    <input type="hidden" value="1" name="fm_{$fieldname}">
    {foreach $records r}
        <label><input type='checkbox' name='fm_{$fieldname}_{$r.id}' id='fm_{$fieldname}_{$r.id}' value='{$r.id}' onchange='fullsave = true;'{if in_array($r.id, $selections)} checked="checked"{/if} /> {$r.display}</label><br />
    {/foreach}
{/if}