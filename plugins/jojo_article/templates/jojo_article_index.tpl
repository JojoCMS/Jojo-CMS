{if $pg_body && $pagenum==1}
    {$pg_body}
{/if}
<div id="articles">
{foreach from=$jojo_articles item=a key=k}
{if $k<2 || $a.weighting==0}
    <div class="article_intro">
    <h3 class="clear"><a href="{$a.url}" title="{$a.title}">{$a.title}</a></h3>
    <div>
        {if $a.image}<a href="{$a.url}" title="{$a.title}"><img src="images/{if $a.thumbnail}{$a.thumbnail}{else}s150{/if}/{$a.image}" class="index-thumb" alt="{$a.title}" /></a>{/if}
        <p>{$a.bodyplain|truncate:400} <a href="{$a.url}" title="View full article" class="more">&gt;&nbsp;read&nbsp;more</a></p>
       {if $a.showdate}<div class="article-date">Added: {$a.datefriendly}</div>{/if}
       {if $a.comments && $a.numcomments}<div class="article-numcomments"><img src="images/blog_comment_icon.gif" class="icon-image" />{$a.numcomments} Comment{if $a.numcomments>1}s{/if}</div>{/if}
        <div class="clear"></div>
    </div>
    </div>

{elseif $k<10}
    <div class="article_intro">
    <h3 class="clear"><a href="{$a.url}" title="{$a.title}">{$a.title}</a></h3>
    <div>
          {if $a.image}<a href="{$a.url}" title="{$a.title}"><img src="images/{if $a.thumbnail}{$a.thumbnail}{else}s150{/if}/{$a.image}/{$a.image}" class="index-thumb" alt="{$a.title}" /></a>{/if}
          <p>{$a.bodyplain|truncate:300} <a href="{$a.url}" title="View full article" class="more">&gt;&nbsp;read&nbsp;more</a></p>
          {if $a.showdate}<div class="article-date">Added: {$a.datefriendly}</div>{/if}
          <div class="clear"></div>
    </div>
    </div>
{elseif $k<20}
    <div class="article_intro">
    <h3 class="clear"><a href="{$a.url}" title="{$a.title}">{$a.title}</a></h3>
    <div>
        {if $a.image}<a href="{$a.url}" title="{$a.title}"><img src="images/{if $a.thumbnail}{$a.thumbnail}{else}s150{/if}/{$a.image}/{$a.image}" class="index-thumb" alt="{$a.title}" /></a>{/if}
        <p>{$a.bodyplain|truncate:200} <a href="{$a.url}" title="View full article" class="more">&gt;&nbsp;read&nbsp;more</a></p>
        {if $a.showdate}<div class="article-date">Added: {$a.datefriendly}</div>{/if}
        <div class="clear"></div>
    </div>
    </div>
{else}
    <a href="{$a.url}" title="{$a.title}">{$a.title}</a>{if $OPTIONS.article_show_date=='yes'} - {$a.datefriendly}{/if}<br />
{/if}

{/foreach}
</div>
<div class="article-pagination">
{$pagination}
</div>