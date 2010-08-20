<a name="comments"></a>
<div id="comments">
    <h3>Comments</h3>
{foreach from=$comments item=c}
    <div class="comment{if $c.authorcomment} author{/if}" id="comment-wrap-{$c.commentid}">
        {include jojo_comment_inner.tpl}
    </div>
{/foreach}
{if $jojo_articlecommentsenabled}
    <br />
<a name="add-comment" href="#"></a>
{if $OPTIONS.comment_show_form == 'no'}<a href="#" id="post-comment-link" onclick="showregion('post-comment'); hideregion('post-comment-link'); return false;">{if $commentbutton}<img src="images/post-comment.gif" alt="Post Comment" style="border: 0;" />{else}post a comment{/if}</a>{/if}
{include file='jojo_post_comment.tpl'}
{/if}
</div>
