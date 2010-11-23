{if $error}<div class="error">{$error}</div>{/if}

{if $jojo_article}
<div id="article">
    <div id="article-toplinks">
    {if $prevarticle}<a href="{$prevarticle.url}" class="prev-article" title="Previous">&lt;&lt; {$prevarticle.title}</a>{/if}
    {if $nextarticle}<a href="{$nextarticle.url}" class="next-article" title="Next">{$nextarticle.title} &gt;&gt;</a>{/if}
    {if $nextarticle || $prevarticle}<div class="clear"></div>{/if}
    </div>
    {if $jojo_article.showdate}<p class="article-date">{$jojo_article.ar_date|date_format}</p>{/if}
    {jojoHook hook="articleBeforeBody"}
    {if $jojo_article.image}<img src="images/v60000/{$jojo_article.image}" class="float-right" alt="{$jojo_article.title}" />{/if}
    {$jojo_article.ar_body}
    {if $jojo_article.author}<div id="article-author"><p>{$jojo_article.author}</p></div>{/if}
    {jojoHook hook="articleAfterBody"}

{if $related}
    <div class="related">
        <h4>Related Articles</h4>
        <ul>
    {foreach from=$related item=rel}
            <li>{if $rel.url}<a href="{$rel.url}">{/if}{$rel.title}{if $rel.url}</a>{/if}</li>
    {/foreach}
        </ul>
    </div>
{/if}
{if $tags}
    <p class="tags"><strong>Tags: </strong>
    {if $itemcloud}
        {$itemcloud}
    {else}
        {foreach from=$tags item=tag}<a href="{if $multilangstring}{$multilangstring}{/if}tags/{$tag.url}/">{$tag.cleanword}</a>{/foreach}
    </p>
    {/if}
{/if}

{if $commenthtml}{$commenthtml}{/if}
    <div id="article-bottomlinks">
        <p class="links">&lt;&lt; <a href="{if $multilangstring}{$multilangstring}{/if}{if $pg_url}{$pg_url}/{else}{$pageid}/{$pg_title|strtolower}{/if}" title="{$pg_title}">{$pg_title}</a>&nbsp; {if $prevarticle}&lt; <a href="{$prevarticle.url}" title="Previous">{$prevarticle.title}</a>{/if}{if $nextarticle} | <a href="{$nextarticle.url}" title="Next">{$nextarticle.title}</a> &gt;{/if}</p>
    </div>

</div>
{/if}