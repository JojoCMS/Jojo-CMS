<div class="col-md-12">
{if $readonly}
    <p>{count($selections)} linked</p>
    <ul class="unstyled">
    {foreach $records r}
        {if in_array($r.id, $selections)}<li>{$r.display}</li>{/if}
    {/foreach}
    </ul>
{else}
    {if !$parent}{$parent=0}{$indent=""}<input type="hidden" value="1" name="fm_{$fieldname}">{/if}
    {foreach $records r}{if $r.parent == $parent}
        <label class="checkbox">{$indent}<input type='checkbox' name='fm_{$fieldname}_{$r.id}' id='fm_{$fieldname}_{$r.id}' value='{$r.id}' onchange='fullsave = true;'{if in_array($r.id, $selections)} checked="checked"{/if} />{$r.display}</label>
            {include file="admin/fields/many2many.tpl" parent=$r.id indent="$indent&nbsp;&nbsp;&nbsp;&nbsp;"}
    {/if}{/foreach}
{/if}
</div>
