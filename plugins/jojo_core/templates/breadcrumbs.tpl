{if $numbreadcrumbs > 1}

{section name=bc loop=$breadcrumbs}
{if $smarty.section.bc.index == ($numbreadcrumbs-1)}
                {$breadcrumbs[bc].name|escape:"htmlall":$charset}
{else}
                <a href="{$breadcrumbs[bc].url}" title="{$breadcrumbs[bc].rollover|escape:"html":$charset}">{$breadcrumbs[bc].name|escape:"html":$charset}</a> &gt;
{/if}
{/section}

{/if}