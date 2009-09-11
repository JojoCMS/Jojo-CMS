{$xmlhead}
{$googlexmlstyle}
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{foreach from=$sitemap item=s}
    <url>
        <loc>{$s.0}</loc>
{if $s.1}        <lastmod>{$s.1|date_format:"%Y-%m-%d"}</lastmod>
{/if}
{if $s.2}        <changefreq>{$s.2}</changefreq>
{/if}
        <priority>{$s.3|string_format:"%0.1f"}</priority>
    </url>
{/foreach}
</urlset>