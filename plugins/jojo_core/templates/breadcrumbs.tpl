{if $numbreadcrumbs > 1}

{foreach from=$breadcrumbs item=bc name='breadcrumbs'}
{if $smarty.foreach.breadcrumbs.last}
                {$bc.name|escape:"htmlall":$charset}
{else}
                <a href="{$bc.url}" title="{$bc.rollover|escape:"html":$charset}">{$bc.name|escape:"html":$charset}</a> &gt;
{/if}
{/foreach}

{/if}