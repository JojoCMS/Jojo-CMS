Adds a site-search facility to the Jojo site. This will search all regular pages, and return a result set ordered by relevance.

Pages created by plugins can also be included in search results, using the Plugin API. See the Gallery3 plugin for an example of this in use.

You may want to include a site search box somewhere on your site - include or modify the code below in your theme's template.

<form class="search" method="post" action="search/">
  <p>
    <input class="textbox" type="text" name="q" value="{$keywords}" />
    <input class="button" type="submit" name="Submit" value="Search" />
  </p>
</form>