{literal}
<script type="text/javascript">
            function togglePerm(perm,def) {
                permImg   = document.getElementById("img_" + perm);
                permField = document.getElementById("fm_{/literal}{$fd_field}{literal}_" + perm + "_");
                switch(permField.value) {
                    case 'Y':
                        permField.value = 'N';
                        permImg.src = 'images/cms/no_active.gif';
                        permImg.alt = 'No';
                        permImg.title = 'No';
                        break;

                    case 'N':
                        permField.value = 'I';
                        if (def == 'Y') {
                          permImg.src = 'images/cms/yes_grey.gif';
                        } else if (def == 'N') {
                          permImg.src = 'images/cms/no_grey.gif';
                        } else {
                          permImg.src = 'images/cms/inherit_active.gif';
                        }
                        permImg.alt = 'Inherited';
                        permImg.title = 'Inherited';
                        break;

                    case 'I':
                    default:
                        permField.value = 'Y';
                        permImg.src = 'images/cms/yes_active.gif';
                        permImg.alt = 'Yes';
                        permImg.title = 'Yes';
                        break;
               }
            }
        </script>
{/literal}
<p><b>Permissions for this Record</b> (these override the inherited permissions)</p>
<table style="border-collapse: collapse;" cellspacing="0">
<tr>
<td style="width:120px">&nbsp;</td>
        {foreach from=$_permOptions key=perm item=name}
            <th style="border: 1px solid #aaa; text-align: center; padding:3px">{$name}</th>
        {/foreach}
</tr>

        <!-- Output permissions for each group  -->
        {if $readonly !="yes"}
            {foreach from=$groups key=group item=groupname}
                <tr><td style="border: 1px solid #aaa; padding:2px;">{$groupname}</td>
                {foreach from=$_permOptions key=perm item=name}
                    <td style="border: 1px solid #aaa; text-align: center; padding:2px;">
                    {if isset($perms[$group]) && isset($perms[$group][$perm])}
                        <img src="images/cms/{if $perms[$group][$perm]}yes_active.gif{else}no_active.gif{/if}" alt="{if $perms[$group][$perm]}Yes{else}No{/if}" title="{if $perms[$group][$perm]}Yes{else}No{/if}" id="img_{$group}.{$perm}" onclick="togglePerm('{$group}.{$perm}','');" />
                        <input type="hidden" name="fm_{$fd_field}[{$group}.{$perm}]" id="fm_{$fd_field}_{$group}.{$perm}_" value="{if $perms[$group][$perm]}Y{else}N{/if}" />
                    {elseif isset($defaultperms[$group]) && isset($defaultperms[$group][$perm])}
                        <img src="images/cms/{if $defaultperms[$group][$perm]}yes_grey.gif{else}no_grey.gif{/if}"
                        alt="{if $defaultperms[$group][$perm]}Yes{else}No{/if}"
                        title="{if $defaultperms[$group][$perm]}Yes{else}No{/if}"
                        id="img_{$group}.{$perm}"
                        onclick="togglePerm('{$group}.{$perm}','{if $defaultperms[$group][$perm]}Y{else}N{/if}');" />
                        <input type="hidden" name="fm_{$fd_field}[{$group}.{$perm}]" id="fm_{$fd_field}_{$group}.{$perm}_" value="I" />
                    {else}
                        <img src="images/cms/no_grey.gif" alt="No" title="No" id="img_{$group}.{$perm}" onclick="togglePerm('{$group}.{$perm}','');" />
                        <input type="hidden" name="fm_{$fd_field}[{$group}.{$perm}]" id="fm_{$fd_field}_{$group}.{$perm}_" value="I" />
                    {/if}
                    </td>
                {/foreach}
                </tr>

            {/foreach}
            </table>
            <em>The greyed-out values indicate the permissions are being inherited from a parent object</em>
         {else}
            {foreach from=$groups key=group item=groupname}
                <tr><td style="border: 1px solid black">{$groupname}</td>
                {foreach from=$_permOptions key=perm item=name}
                    {if isset($perms[$group]) && isset($perms[$group][$perm])}
                        <td style="border: 1px solid #aaa; text-align: center; padding:2px;">
                            <img src="images/cms/{if $perms[$group][$perm]}yes_grey.gif{else}no_grey.gif{/if}" alt="{if $perms[$group][$perm]}Yes{else}No{/if}" title="{if $perms[$group][$perm]}Yes{else}No{/if}" />
                        </td>
                    {else}
                        <td style="border: 1px solid #aaa; text-align: center; padding:2px;">
                            <img src="images/cms/inherit_grey.gif" alt="Inherited" title="Inherited" />
                        </td>
                    {/if}
                {/foreach}
               </tr>
            {/foreach}
            </table>
        {/if}