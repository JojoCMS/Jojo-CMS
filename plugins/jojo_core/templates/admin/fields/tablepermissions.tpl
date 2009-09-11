<table style="border-collapse: collapse" cellspacing="0">
    <tr>
    <td style="width:50px">&nbsp;</td>
        {foreach from=$_permOptions key=perm item=name}
            <th style="border: 1px solid #aaa; text-align: center; padding:3px">{$name}</th>
        {/foreach}
    </tr>
    {foreach from=$groups key=group item=groupname}
        <tr><td style="border: 1px solid #aaa; padding:2px;">{$groupname}</td>
        {foreach from=$_permOptions key=perm item=name}
            <td style="border: 1px solid black; text-align: center">
                <input type="checkbox" name="fm_{$fd_field}[{$group}.{$perm}]" value='1' {if isset($perms[$group]) && isset($perms[$group][$perm]) && $perms[$group][$perm]}checked="checked"{/if} {if $readonly == "yes"}readonly="readonly"{/if} />
            </td>
        {/foreach}
        </tr>
    {/foreach}
</table>