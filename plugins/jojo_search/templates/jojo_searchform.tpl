<div id="search">
    <form method="post" action="{$SITEURL}/search/">
        <label for="q" style="display:none;">Search</label><input type="text" id="q" name="q" value="{if $keywords}{$keywords}{else}Search{/if}" /><input type="hidden" id="l" name="l" value="en" style="display:none" />
        <a href='#' onclick="document.getElementById('search').submit();return false;"></a><input type="submit" name="submitbutton" value="submit" style="display:none" />
    </form>
</div>
