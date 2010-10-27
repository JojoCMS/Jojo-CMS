User-agent: *

{foreach from=$disallow item=d}
Disallow: /{$d}{if strpos($d, '.')===false}/{/if}

{/foreach}

{$rules}
