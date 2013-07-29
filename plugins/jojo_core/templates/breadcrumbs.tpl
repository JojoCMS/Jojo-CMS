{if $numbreadcrumbs > 1}
<ul class="breadcrumb">
    {if !$sep}{assign "&gt;" sep}{/if}
    {foreach from=$breadcrumbs item=bc name='breadcrumbs'}
        {if $smarty.foreach.breadcrumbs.last}
            <li class="active">{$bc.name}</li>
        {else}
            <li><a href="{$bc.url}" title="{$bc.rollover}">{$bc.name}</a> <span class="divider">{$sep}</span></li>
        {/if}
    {/foreach}
</ul>
{/if}
