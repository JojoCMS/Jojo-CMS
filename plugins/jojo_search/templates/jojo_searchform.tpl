    <form class="search form" role="search" method="post" action="{$SITEURL}/search/">
        <input type="hidden" class="l" name="l" value="en" />
        <label for="q" class="control-label sr-only">{$searchformlabel}</label>
        <div class="input-group">
            <input type="text" class="form-control input-sm" name="q" value="{if $keywords}{$keywords}{elseif $searchformdefault}Search{/if}" />
            <span class="input-group-btn"><button type="submit" class="btn btn-primary btn-sm" name="submitbutton">{$searchformsubmit}</button></span>
        </div>
    </form>
