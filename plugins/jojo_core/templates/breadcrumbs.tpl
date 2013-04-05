{if $numbreadcrumbs > 1}

    {if !$sep}{assign "&gt;" sep}{/if}
    {foreach from=$breadcrumbs item=bc name='breadcrumbs'}
        {if $smarty.foreach.breadcrumbs.last}
            <span class="current">{$bc.name|escape:"htmlall":$charset}</span>
        {else}
            <a href="{$bc.url}" title="{$bc.rollover|escape:"html":$charset}">{$bc.name|escape:"html":$charset}</a> {$sep}
        {/if}
    {/foreach}

{/if}
