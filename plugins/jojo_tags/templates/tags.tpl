{if $openingparagraph}<p>{$openingparagraph}</p>{elseif $tag}<p>The following results and pages have been tagged as <strong>{$tag}</strong>.</p>{/if}
{if $results}
{foreach from=$results item=result}
<h3 style="clear: both"><a href="{$result.url}">{$result.title}</a></h3>
<div>
  <p>{if $result.image}<a href="{$result.url}" title="{$result.title}"><img src="images/v12000/{$result.image}" class="float-right" alt="{$result.title}{if $tag} - {$tag}{/if}" /></a>{/if}
  {$result.text|truncate:350}
  <br /><a href="{if $result.absoluteurl}{$result.absoluteurl}{else}{$result.url}{/if}" title="Read more" class="links">&gt; {$result.displayurl}</a></p>
</div>
{/foreach}
{/if}
{if $tags}
{if $article_tag_cloud_related != "no"}
<h3 style='clear: both'>{if $tag}Tags related to {$tag}{else}Related Tags{/if}</h3>
[[tagcloud:{$tags}]]
{/if}
{else}
{if $pg_body}{$pg_body}
<h3>Tags</h3>
{/if}
[[tagcloud]]
{/if}