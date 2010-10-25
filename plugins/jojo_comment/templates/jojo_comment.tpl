{if $comments}
<a name="comments"></a>
<div id="comments">
    <h3>Comments</h3>
{foreach from=$comments item=c}
    <div class="comment{if $c.authorcomment} author{/if}" id="comment-wrap-{$c.commentid}">
        {include file="jojo_comment_inner.tpl"}
    </div>
{/foreach}
</div>
{/if}