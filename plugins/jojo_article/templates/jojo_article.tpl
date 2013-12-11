{if $error}<div class="error">{$error}</div>{/if}

{if $jojo_article}
<div id="article"{if $jojo_article.ar_featured} class="featured"{/if}>
    <div id="article-toplinks">
        <ul class="pagination">
            <li><a href="{$jojo_article.pageurl}" title="{$jojo_article.pagetitle}">##Home##</a></li>
            {if $prevarticle}<li><a href="{$prevarticle.url}" title="{$prevarticle.title}">&laquo;</a></li>{/if}
            {if $nextarticle}<li><a href="{$nextarticle.url}" title="{$nextarticle.title}">&raquo;</a></li>{/if}
        </ul>
    </div>
    {if $jojo_article.showdate}<p class="article-date">{$jojo_article.ar_date|date_format}</p>{/if}
    {jojoHook hook="articleBeforeBody"}
    {if $jojo_article.image}<img src="images/{$jojo_article.mainimage}/{$jojo_article.image}" class="float-right" alt="{$jojo_article.title}" />{/if}
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
        <ul class="pagination">
            <li><a href="{$jojo_article.pageurl}" title="{$jojo_article.pagetitle}">##Home##</a></li>
            {if $prevarticle}<li><a href="{$prevarticle.url}" title="{$prevarticle.title}">&laquo;</a></li>{/if}
            {if $nextarticle}<li><a href="{$nextarticle.url}" title="{$nextarticle.title}">&raquo;</a></li>{/if}
        </ul>
    </div>

</div>
{/if}