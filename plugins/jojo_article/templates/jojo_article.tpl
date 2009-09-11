{if $error}<div class="error">{$error}</div>{/if}

{if $jojo_article}
<div id="article">
    <div id="article-toplinks">
    {if $prevarticle}<a href="{$prevarticle.url}" class="prev-article" title="Previous">&lt;&lt; {$prevarticle.title}</a>{/if}
    {if $nextarticle}<a href="{$nextarticle.url}" class="next-article" title="Next">{$nextarticle.title} &gt;&gt;</a>{/if}
    {if $nextarticle || $prevarticle}<div class="clear"></div>{/if}
    </div>
    {if $OPTIONS.article_show_date=='yes'}<p class="article-date">{$jojo_article.ar_date|date_format}</p>{/if}

    {jojoHook hook="articleBeforeBody"}
    {$jojo_article.ar_body}
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
    <div id="article-bottomlinks">
        <p class="links">&lt;&lt; <a href="{if $multilangstring}{$multilangstring}{/if}{if $pg_url}{$pg_url}/{else}{$pageid}/{$pg_title|strtolower}{/if}" title="{$pg_title}">{$pg_title}</a>&nbsp; {if $prevarticle}&lt; <a href="{$prevarticle.url}" title="Previous">{$prevarticle.title}</a>{/if}{if $nextarticle} | <a href="{$nextarticle.url}" title="Next">{$nextarticle.title}</a> &gt;{/if}</p>
    </div>
{if $tags}
    <p class="tags"><strong>Tags: </strong>
{if $itemcloud}
        {$itemcloud}
{else}
{foreach from=$tags item=tag}
        <a href="{if $multilangstring}{$multilangstring}{/if}tags/{$tag|replace:" ":"-"}/">{$tag}</a>
{/foreach}
    </p>
{/if}
{/if}

{if $jojo_articlecomments}
<a name="comments" href="#"></a>
    <div id="article-comments">
        <h3>{if $jojo_article.numcomments}{$jojo_article.numcomments}{/if} Comments</h3>
        {counter start=1 skip=1 assign="i"}
{foreach from=$jojo_articlecomments item=ac}
        <div class="comment{if $ac.ac_authorcomment=='yes'} author{/if}" id="article-comment-wrap-{$ac.articlecommentid}">
        {assign var=commentid value=$ac.articlecommentid}
        {assign var=ac_body value=$ac.ac_body}
        {assign var=ac_website value=$ac.ac_website}
        {assign var=ac_anchortext value=$ac.ac_anchortext}
        {assign var=ac_useanchortext value=$ac.ac_useanchortext}
        {assign var=ac_name value=$ac.ac_name}
        {assign var=ac_nofollow value=$ac.ac_nofollow}
        {assign var=ac_timestamp value=$ac.ac_timestamp}

        {include file="jojo_article_comment.tpl"}
        </div>
{/foreach}
    </div>
{/if}
{if $jojo_articlecommentsenabled}
    <br />
<a name="add-comment" href="#"></a>
{if $OPTIONS.article_show_comment_form == 'no'}<a href="#" id="post-comment-link" onclick="showregion('post-comment'); hideregion('post-comment-link'); return false;">{if $commentbutton}<img src="images/post-comment.gif" alt="Post Comment" style="border: 0;" />{else}post a comment{/if}</a>{/if}
{include file='jojo_article_post_comment.tpl'}
{/if}
</div>
{/if}