<div id="searchresults">
<form name="search1" action="{$searchurl}" method="post">
    <input name="q" type="text" value="{if $keywords}{$keywords}{/if}" size="29" />
    <select name="type" style="font-size: 90%">
        <option value="" {if $searchtype == ''}selected="selected"{/if}>any</option>
        <option value="all" {if $searchtype == 'all'}selected="selected"{/if}>all</option>
        <option value="phrase" {if $searchtype == 'phrase'}selected="selected"{/if}>exact</option>
    </select>
{if $MULTILANGUAGE && (count($languages)>1)}
    <select name="l" style="font-size: 90%">
        <option value=""{if $language == ''} selected="selected"{/if}>All Languages</option>
{foreach from=$languages key=code item=name}
        <option value="{$code}"{if $language == $code} selected="selected"{/if}>{$name|escape:"html":$charset}</option>
{/foreach}
    </select>
{/if}
    <input class="button btn" type="submit" value="Search" />
</form>
<br/>

{if $results}
    {if $OPTIONS.search_relevance =='yes'}<div class="search-relevance">Relevance</div>{/if}
   <p>{$numresults} Search results for <b>{$displaykeywords}</b>.</p>
{if $OPTIONS.search_filtering =='yes' && count($resulttypes) > 1}
    <p class="links" id="search-filter">Filter results by: <a href="#" onclick="filterresults();return false;" id="filter-search-none" class="current-filter">None</a>
{foreach from=$resulttypes item=cat}
   | <a href="#" onclick="filterresults('search-cat-{$cat|strtolower|replace:' ':'-'}');return false;"  id="filter-search-cat-{$cat|strtolower|replace:' ':'-'}">{$cat}</a>&nbsp;
{/foreach}
    </p>
{/if}

{foreach item=res from=$results}
  <div class="search-result search-cat-{$res.type|strtolower|replace:' ':'-'}">
    {if $OPTIONS.search_relevance =='yes'}<div class="search-relevance-display" style="width:{$res.displayrelevance|string_format:"%d"}px;" title="Search relevance: {$res.relevance|string_format:"%.1f"}"></div>{/if}
    <h3><a href="{$res.url}" title="{$res.title}">{$res.title}</a></h3>
    {if $res.image && $OPTIONS.search_images =='yes'}<a href="{$res.url}" title="{$res.title}" rel="nofollow"><img src="images/{if $OPTIONS.search_image_format}{$OPTIONS.search_image_format}{else}v6000{/if}/{$res.image}" class="float-right" alt="{$res.title}" /></a>{/if}
    <p>{$res.body}</p>
    {if $res.tags}<p class="links">Tagged with:
{foreach from=$res.tags item=tag}
<a href="{if $MULTILANGUAGE}{$pg_language}/{/if}tags/{$tag.url}/">{if $tag.cleanword==$keywords}<b>{$tag.cleanword}</b>{else}{$tag.cleanword}{/if}</a> |
{/foreach}
    </p>{/if}
    <p class="links">{$res.type}: <a href="{$res.url}" title="{$res.title}" class="links" rel="nofollow" >&gt; {$res.displayurl}</a></p>
  </div>
{/foreach}
<form name="search2" action="{$searchurl}" method="post">
    <input type="text" name="q" value="{if $keywords}{$keywords}{/if}" size="29" />
    <select name="type" style="font-size: 90%">
        <option value=""{if $searchtype == ''} selected="selected"{/if}>any</option>
        <option value="all"{if $searchtype == 'all'} selected="selected"{/if}>all</option>
        <option value="phrase"{if $searchtype == 'phrase'} selected="selected"{/if}>exact</option>
    </select>
{if $MULTILANGUAGE && (count($languages)>1)}
    <select name="l">
        <option value=""{if $language == ''} selected="selected"{/if}>All Languages</option>
{foreach from=$languages key=code item=name}
        <option value="{$code}"{if $language == $code} selected="selected"{/if}>{$name|escape:"html":$charset}</option>
{/foreach}
    </select>
{/if}
    <input class="button" type="submit" value="Search" />
</form>

<script type="text/javascript">
/*<![CDATA[*/
function filterresults(cat) {literal}{
    if (cat) {
        $('#search-filter a').removeClass('current-filter');
        var filter = '#filter-'+cat;
        $(filter).addClass('current-filter');
        $(".search-result").hide();
        $("."+cat).show();
    } else {
       $(".search-result").show();
        $('#search-filter a').removeClass('current-filter');
        $('#filter-search-none').addClass('current-filter');
    }
}{/literal}
/*]]>*/
</script>



{elseif $keywords}
    <p>There were no search results for <b>{$displaykeywords}</b>.</p>
{else}
{$pg_body}
{/if}
</div>
