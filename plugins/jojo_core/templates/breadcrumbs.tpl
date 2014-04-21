{if $numbreadcrumbs > 1}
<ul class="breadcrumb">
    {foreach from=$breadcrumbs item=bc name='breadcrumbs'}{if $smarty.foreach.breadcrumbs.last}<li class="active">{$bc.name}</li>
    {else}<li><a href="{$bc.url}" title="{$bc.rollover}">{$bc.name}</a>{if $sep} <span class="divider">{$sep}</span>{/if}</li>
    {/if}{/foreach}
</ul>
{/if}
