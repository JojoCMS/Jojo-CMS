User-agent: *

{section name=d loop=$disallow}
Disallow: /{$disallow[d]}{if strpos($disallow[d], '.')===false}/{/if}
{/section}

{$rules}