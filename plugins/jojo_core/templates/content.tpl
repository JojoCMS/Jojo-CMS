{** Displays content if available, otherwise displays a list of sub-pages (if available) **}
{if $innertemplate}
    {include file=$innertemplate}
{elseif $content}
    {$content}
{elseif $subpages}<br />
    <h3>{$title} - Sub Pages</h3>
    <ul>
    {foreach from=$subpages item=p}
      <li><a href="{$p.url}" title="{$p.rollover}">{$p.name}</a></li>
    {/foreach}
    </ul>
{/if}