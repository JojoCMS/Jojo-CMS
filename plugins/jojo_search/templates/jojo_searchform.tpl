    <form class="search" method="post" action="{$SITEURL}/search/">
    <label for="q">Search</label><input type="text" class="q" name="q" value="{if $keywords}{$keywords}{else}Search{/if}" />
    <input type="hidden" class="l" name="l" value="en" style="display:none" />
    <a href='#' onclick="$(this).parents('.search').submit();return false;">Go</a><input type="submit" name="submitbutton" value="submit" style="display:none" />
    </form>
