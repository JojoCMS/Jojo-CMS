<ul>
{foreach from=$mainnav item=n}
    <li {if $n.pageid == $pageid} id="current"{/if}><a href="{$n.url}" title="{$n.title|escape:"html"}"{if $n.pg_followto=='no'} rel="nofollow"{/if}>{$n.label}</a></li>
{/foreach}
</ul>