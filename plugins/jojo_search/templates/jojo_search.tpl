<div id="searchresults">
<form name="search1" action="{$searchurl}" method="post" role="form" class="form-inline">
    <div class="form-group">
        <label class="sr-only" for="q">Search Query</label>
        <input class="form-control" name="q" type="text" value="{if $keywords}{$keywords}{/if}" size="29" />
    </div>
    <div class="form-group">
        <select class="form-control" name="type" style="font-size: 90%">
            <option value="" {if $searchtype == ''}selected="selected"{/if}>any</option>
            <option value="all" {if $searchtype == 'all'}selected="selected"{/if}>all</option>
            <option value="phrase" {if $searchtype == 'phrase'}selected="selected"{/if}>exact</option>
        </select>
    </div>
{if $MULTILANGUAGE && (count($languages)>1)}
    <div class="form-group">
        <select class="form-control" name="l" style="font-size: 90%">
            <option value=""{if $language == ''} selected="selected"{/if}>All Languages</option>
    {foreach from=$languages key=code item=name}
            <option value="{$code}"{if $language == $code} selected="selected"{/if}>{$name|escape:"html":$charset}</option>
    {/foreach}
        </select>
    </div>
{/if}
    <button class="btn btn-primary" type="submit" />Search</button>
</form>

{if $results}
    {if $OPTIONS.search_relevance =='yes'}<div class="search-relevance">Relevance</div>{/if}
    <p>{$numresults} Search results for <b>{$displaykeywords}</b>.</p>
    {if $OPTIONS.search_filtering =='yes' && count($resulttypes) > 1}<p class="links" id="search-filter">Filter results by: <a href="#" onclick="filterresults();return false;" id="filter-search-none" class="current-filter">Everything</a>{foreach from=$resulttypes item=cat}{if $cat!='none'} | <a href="#" onclick="filterresults('search-cat-{$cat|strtolower|replace:' ':'-'}');return false;"  id="filter-search-cat-{$cat|strtolower|replace:' ':'-'}">{$cat}</a>&nbsp;{/if}{/foreach}</p>
    {/if}

{foreach item=res from=$results}
  <div class="media search-result search-cat-{$res.type|strtolower|replace:' ':'-'}">
    {if $OPTIONS.search_relevance =='yes'}<div class="search-relevance-display" style="width:{$res.displayrelevance|string_format:"%d"}px;" title="Search relevance: {$res.relevance|string_format:"%.1f"}"></div>{/if}
    {if $res.image && $OPTIONS.search_images =='yes'}<a class="pull-left" href="{$res.url}" title="{$res.title}" rel="nofollow"><img class="media-object"  src="images/{if $OPTIONS.search_image_format}{$OPTIONS.search_image_format}{else}s120{/if}/{$res.image}" class="pull-left" alt="{$res.title}" /></a>{/if}
    <div class="media-body">
        <h3 class="media-heading"><a href="{$res.url}" title="{$res.title}">{$res.title}</a></h3>
        <p>{$res.body}</p>
        {if $res.tags && $searchtags}<p class="links">Tagged with: {foreach from=$res.tags item=tag}<a href="{if $MULTILANGUAGE}{$pg_language}/{/if}tags/{$tag.url}/">{if $tag.cleanword==$keywords}<b>{$tag.cleanword}</b>{else}{$tag.cleanword}{/if}</a> | {/foreach}</p>
        {/if}
        <p class="links">{if $res.type!='none' && $OPTIONS.search_filtering =='yes'}{$res.type}: {/if}<a href="{$res.url}" title="{$res.title}" class="links" rel="nofollow" >&gt; {$res.displayurl}</a></p>
    </div>
  </div>
{/foreach}
<form name="search2" action="{$searchurl}" method="post" role="form" class="form-inline">
    <label class="sr-only" for="q">Search Query</label>
    <div class="form-group">
        <input class="form-control" name="q" type="text" value="{if $keywords}{$keywords}{/if}" size="29" />
    </div>
    <div class="form-group">
        <select class="form-control" name="type" style="font-size: 90%">
            <option value="" {if $searchtype == ''}selected="selected"{/if}>any</option>
            <option value="all" {if $searchtype == 'all'}selected="selected"{/if}>all</option>
            <option value="phrase" {if $searchtype == 'phrase'}selected="selected"{/if}>exact</option>
        </select>
    </div>
{if $MULTILANGUAGE && (count($languages)>1)}
    <div class="form-group">
        <select class="form-control" name="l" style="font-size: 90%">
            <option value=""{if $language == ''} selected="selected"{/if}>All Languages</option>
    {foreach from=$languages key=code item=name}
            <option value="{$code}"{if $language == $code} selected="selected"{/if}>{$name|escape:"html":$charset}</option>
    {/foreach}
        </select>
    </div>
{/if}
    <button class="btn btn-primary" type="submit" />Search</button>
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
