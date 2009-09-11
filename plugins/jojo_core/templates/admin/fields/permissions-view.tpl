{foreach from=$groups key=group item=groupname} {$groupname}
{foreach from=$_permOptions key=perm item=name}   {$name}:{if isset($perms[$group]) && isset($perms[$group][$perm])}{if $perms[$group][$perm]} Yes {else} No {/if}{else} Inherited {/if}
{/foreach}
{/foreach}