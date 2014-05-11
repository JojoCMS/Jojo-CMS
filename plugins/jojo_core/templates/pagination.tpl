<ul class="pagination">
{if $pagenum > 5 && $numpages > 10}{if $pagenum < ($numpages-5)}{$start = $pagenum - 4}{else}{$start = $numpages-8}{/if}{else}{$start = 2}{/if}
{if $pagenum < ($numpages-5) && $numpages > 10}{if $pagenum > 6}{$end = $pagenum + 4}{else}{$end = 9}{/if}{else}{$end = $numpages-1}{/if}

{if $pagenum > 1}
    <li><a href="{$pageurl}/p{$pagenum -1}/" title="##Previous##">&laquo;</a></li>
{/if}
    <li{if $pagenum == 1} class="active"{/if}><a href="{$pageurl}/" title="##First##">1</a></li>
{if $pagenum > 6 && $numpages > 10}
    <li class="ellip">&hellip;</li>
{/if}
{if $numpages >2}
    {for p $start $end}
    <li{if $p == $pagenum} class="active"{/if}><a href="{$pageurl}/{if $p > 1}p{$p}/{/if}">{$p}</a></li>
    {/for}
{/if}
{if $pagenum < ($numpages-5) && $numpages > 10}
    <li class="ellip">&hellip;</li>
{/if}
{if $numpages >1}
    <li{if $pagenum == $numpages} class="active"{/if}><a href="{$pageurl}/p{$numpages}/" title="##Last##">{$numpages}</a></li>
{/if}
{if $pagenum < $numpages}
    <li><a href="{$pageurl}/p{$pagenum +1}/" title="##Next##">&raquo;</a></li>
{/if}
</ul>
