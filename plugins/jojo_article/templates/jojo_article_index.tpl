{if $pg_body && $pagenum==1}
    {$pg_body}
{/if}
<div id="articles">
{foreach from=$jojo_articles item=a key=k}

{if $k<2}
    <h3 class="clear"><a href="{$a.url}" title="{$a.ar_title}">{$a.ar_title}</a></h3>
    <div>
        {if $a.ar_image}<a href="{$a.url}" title="{$a.ar_title}"><img src="images/150/articles/{$a.ar_image}" class="index-thumb" alt="{$a.ar_title}" /></a>{/if}
        <p>{$a.bodyplain|truncate:400}</p>
        <p class="more"><a href="{$a.url}" title="View full article">more...</a></p>
       {if $OPTIONS.article_show_date=='yes'}<div class="article-date">Added: {$a.datefriendly}</div>{/if}
       {if $a.numcomments}<div class="article-numcomments"><img src="images/blog_comment_icon.gif" class="icon-image" />{$a.numcomments} Comment{if $a.numcomments>1}s{/if}</div>{/if}
        <div class="clear"></div>
    </div>

{elseif $k<10}
    <h3 class="clear"><a href="{$a.url}" title="{$a.ar_title}">{$a.ar_title}</a></h3>
    <div>
          {if $a.ar_image}<a href="{$a.url}" title="{$a.ar_title}"><img src="images/150/articles/{$a.ar_image}" class="index-thumb" alt="{$a.ar_title}" /></a>{/if}
          <p>{$a.bodyplain|truncate:300}</p>
        <p class="more"><a href="{$a.url}" title="View full article">more...</a></p>
          {if $OPTIONS.article_show_date=='yes'}<div class="article-date">Added: {$a.datefriendly}</div>{/if}
          <div class="clear"></div>
    </div>

{elseif $k<20}
    <h3 class="clear"><a href="{$a.url}" title="{$a.ar_title}">{$a.ar_title}</a></h3>
    <div>
        {if $a.ar_image}<a href="{$a.url}" title="{$a.ar_title}"><img src="images/150/articles/{$a.ar_image}" class="index-thumb" alt="{$a.ar_title}" /></a>{/if}
        <p>{$a.bodyplain|truncate:200}</p>
        <p class="more"><a href="{$a.url}" title="View full article">more...</a></p>
        {if $OPTIONS.article_show_date=='yes'}<div class="article-date">Added: {$a.datefriendly}</div>{/if}
        <div class="clear"></div>
    </div>

{else}
    <a href="{$a.url}" title="{$a.ar_title}">{$a.ar_title}</a>{if $OPTIONS.article_show_date=='yes'} - {$a.datefriendly}{/if}<br />
{/if}

{/foreach}
</div>
<div class="article-pagination">
{$pagination}
</div>