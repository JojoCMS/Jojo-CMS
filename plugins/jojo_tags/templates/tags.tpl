{if $openingparagraph}<p>{$openingparagraph}</p>{elseif $tag}<p>The following results and pages have been tagged as <strong>{$tag}</strong>.</p>{/if}
{if $results}{foreach from=$results item=result}
<div class="media search-result">
    {if $result.image}<a class="pull-left" href="{$SITEURL}/{$result.url}" title="{$result.title}"><img class="media-object"  src="{$SITEURL}/images/{if $OPTIONS.tag_image_format}{$OPTIONS.tag_image_format}{else}s120{/if}/{$result.image}" class="pull-left" alt="{$result.title}" /></a>{/if}
    <div class="media-body">
        <h3 class="media-heading"><a href="{$SITEURL}/{$result.url}" title="{$result.title}">{$result.title}</a></h3>
        <p>{$result.text|truncate:350}</p>
        <p class="links"><a href="{if $result.absoluteurl}{$result.absoluteurl}{else}{$SITEURL}/{$result.url}{/if}" title="{$result.displayurl}" class="links" rel="nofollow" >{$result.displayurl}</a></p>
    </div>
</div>
{/foreach}{/if}
{if $tags}{if $article_tag_cloud_related != "no"}
<h3 style='clear: both'>{if $tag}Tags related to {$tag}{else}Related Tags{/if}</h3>
[[tagcloud:{$tags}]]
{/if}
{else}{if $pg_body}{$pg_body}
<h3>Tags</h3>
{/if}
[[tagcloud]]
{/if}