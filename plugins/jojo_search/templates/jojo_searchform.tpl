    <form class="search form-inline" method="post" action="{$SITEURL}/search/">
        <input type="hidden" class="l" name="l" value="en" />
        <div class="form-group">
            <label for="q" class="control-label{if !$searchformlabel} sr-only{/if}">{$searchformlabel}</label>
            <input type="text" class="form-control input-sm" name="q" value="{if $keywords}{$keywords}{elseif $searchformdefault}Search{/if}" />
        </div>
        <button type="submit" class="btn btn-primary btn-sm" name="submitbutton">{$searchformsubmit}</button>
    </form>
