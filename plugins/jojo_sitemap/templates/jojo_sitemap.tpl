{if $content}{$content}{/if}
<div class="sitemap">
    {foreach from=$sitemap item=s}
    <!-- [Sitemap section {$s.title}] -->
    {if $sitemap_show_headings}<h3>{$s.title}</h3>{/if}
    {$s.header}
    {$s.htmlTree}
    {$s.footer}
{/foreach}
</div>