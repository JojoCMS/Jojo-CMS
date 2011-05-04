{if $numbreadcrumbs > 1}

{foreach $breadcrumbs key bc 'breadcrumbs'}
{if $.foreach.breadcrumbs.last}
                {$bc.name|escape:"htmlall":$charset}
{else}
                <a href="{$bc.url}" title="{$bc.rollover|escape:"html":$charset}">{$bc.name|escape:"html":$charset}</a> &gt;
{/if}
{/foreach}

{/if}
