{if $subpages}
<ul class="subpages">
    {foreach from=$subpages item=s}<li><a href="{$s.url}" title="{$s.title}">{$s.label}</a></li>
    {/foreach}
</ul>
{/if}
