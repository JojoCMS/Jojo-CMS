{include file="admin/header.tpl"}
{if $content}{$content}{/if}
{if $pagecontent}{$pagecontent}{/if}
{if $subpages}
<br />
<h3>{if $title}{$title} - {/if}Sub Pages</h3>
<ul>
{foreach from=$subpages item=sub}
  <li><a href="{$sub.url}" title="{$sub.rollover}">{$sub.name}</a></li>
{/foreach}
</ul>
{/if}

{include file="admin/footer.tpl"}