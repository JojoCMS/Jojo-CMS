<h2>Article</h2>
<div id="rightsidebartext">
    {foreach from=$articles item=a}
    <h3>{$a.date|date_format:"%B %e, %Y"}</h3>
    <p>{$a.bodyplain|truncate:100:"..."}</p>
    <p class="more"><a href="{$a.url}">Read More</a><br /></p>
    {/foreach}
</div>