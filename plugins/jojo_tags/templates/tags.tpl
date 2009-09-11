<p>{if $openingparagraph}{$openingparagraph}{elseif $tag}The following results and pages have been tagged as <strong>{$tag}{$tag}</strong>.{/if}</p>

{section name=a loop=$results}

<h3 style="clear: both"><a href="{$results[a].url}">{$results[a].title|escape:"html"}</a></h3>
<div>
  <p>{if $results[a].image}<a href="{$results[a].url}" title="{$results[a].title}"><img src="images/v12000/{$results[a].image}" class="right-image" alt="{$results[a].title}{if $tag} - {$tag}{/if}" /></a>{/if}
  {$results[a].text|truncate:350}
  <br /><a href="{if $results[a].absoluteurl}{$results[a].absoluteurl}{else}{$results[a].url}{/if}" title="Read more" class="links">&gt; {$results[a].displayurl}</a></p>
</div>
{/section}

{if $tags}
{if $article_tag_cloud_related != "no"}
<h3 style='clear: both'>{if $tag}Tags related to {$tag}{else}Related Tags{/if}</h3>
[[tagcloud:{$tags}]]{/if}{else}
{$pg_body}
<h3>Tags</h3>
[[tagcloud]]
{/if}