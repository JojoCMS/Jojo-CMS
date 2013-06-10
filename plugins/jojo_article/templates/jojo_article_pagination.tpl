{if $numpages == 2 && $pagenum == 2}<a href="{$pageurl}/p1/">previous...</a>
{elseif $numpages == 2 && $pagenum == 1}<a href="{$pageurl}/p2/">more...</a>
{else}
<ul>
    {for p 1 $numpages}{if $p == $pagenum}<li>&gt; Page {$p}</li>
    {else}<li>&gt; <a href="{$pageurl}/{if $p > 1}p{$p}/{/if}">Page {$p}</a></li>
    {/if}
    {/for}
</ul>
{/if}
