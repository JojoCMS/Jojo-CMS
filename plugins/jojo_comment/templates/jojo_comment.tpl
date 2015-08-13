{if $comments}
{foreach from=$comments item=c}
    <div class="comment clearfix{if $c.authorcomment} author{/if}" id="comment-wrap-{$c.commentid}">
        {include file="jojo_comment_inner.tpl"}
    </div>
{/foreach}
{/if}