<ul class="pagination">
    {for p 1 $numpages}
    {if $p == 1 && $pagenum > 1}<li><a href="{$pageurl}/p{$pagenum -1}/">&laquo;</a></li>{/if}
    <li{if $p == $pagenum} class="active"{/if}><a href="{$pageurl}/{if $p > 1}p{$p}/{/if}">{$p}</a></li>
    {if $p == $numpages && $pagenum < $numpages}<li><a href="{$pageurl}/p{$pagenum +1}/">&raquo;</a></li>{/if}
    {/for}
</ul>
