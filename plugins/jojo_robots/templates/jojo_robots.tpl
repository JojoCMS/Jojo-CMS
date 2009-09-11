User-agent: *

{section name=d loop=$disallow}
Disallow: /{$disallow[d]}/
{/section}

{$rules}