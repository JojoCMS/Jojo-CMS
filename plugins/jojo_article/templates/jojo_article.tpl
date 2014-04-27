{if $error}<div class="error">{$error}</div>{/if}
{if $jojo_article}
<div id="article"{if $jojo_article.ar_featured} class="featured"{/if}>
    {if $jojo_article.showdate}<p class="date">{$jojo_article.ar_date|date_format}</p>{/if}
    {jojoHook hook="articleBeforeBody"}
    {if $jojo_article.image}<img src="images/{$jojo_article.mainimage}/{$jojo_article.image}" class="float-right" alt="{$jojo_article.title}" />{/if}
    {$jojo_article.ar_body}
    {if $jojo_article.author}<p class="author">{$jojo_article.author}</p>{/if}
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
    <ul class="pager">
        {if $prevarticle}<li><a href="{$prevarticle.url}" title="{$prevarticle.title}">&laquo; ##Previous##</a></li>{/if}
        {if $nextarticle}<li><a href="{$nextarticle.url}" title="{$nextarticle.title}">##Next## &raquo;</a></li>{/if}
    </ul>

</div>
{/if}