
<h2>Article</h2>

	{foreach from=$articles item=a}
	<div id="rightsidebartext">
    <h3>{$a.date|date_format:"%B %e, %Y"}</h3>
    <div id="rightsidebartext"><p>{$a.bodyplain|truncate:100:"..."}</p>
    <p class="more"><a href="{$a.url}">Read More</a><br /></p></div></div>
	{/foreach}