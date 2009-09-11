
{** Displays content if available, otherwise displays a list of sub-pages (if available) **}
{if $innertemplate}
    {include file=$innertemplate}
{elseif $content}
    {$content}
{elseif $subpages}
    <br />
    <h3>{$title} - Sub Pages</h3>
    <ul>
    {section name=sub loop=$subpages}
      <li><a href="{$subpages[sub].url}" title="{$subpages[sub].rollover}">{$subpages[sub].name}</a></li>
    {/section}
    </ul>
{/if}