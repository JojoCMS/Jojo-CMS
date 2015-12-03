{if $ogdata}{if $ogdata.fb_admins}  <meta property="fb:admins" content="{$ogdata.fb_admins}"/>{/if}{if $ogdata.fb_app_id} 
    <meta property="fb:app_id" content="{$ogdata.fb_app_id}"/> {/if} 
    <meta property="og:site_name" content="{$ogdata.site_name}"/>
    <meta property="og:type" content="{$ogdata.type}"/>
    <meta property="og:url" content="{$ogdata.url}"/>
    <meta property="og:title" content="{$ogdata.title}"/>
    <meta property="og:image" content="{if $ogdata.image}{$ogdata.image}{/if}"/>
    <meta property="og:description" content="{if $ogdata.description}{$ogdata.description}{/if}"/>{if $ogdata.audio} 
    <meta property="og:audio" content="{$ogdata.audio}" />
    <meta property="og:audio:title" content="{if $ogdata.audio_title}{$ogdata.audio_title}{/if}" /> 
    <meta property="og:audio:artist" content="{if $ogdata.audio_artist}{$ogdata.audio_artist}{/if}" />
    <meta property="og:audio:album" content="{if $ogdata.audio_album}{$ogdata.audio_album}{/if}" />
    <meta property="og:audio:type" content="application/mp3" />{/if}{if $ogdata.latitude} 
    <meta property="og:latitude" content="{$ogdata.latitude}"/>{/if}{if $ogdata.longitude} 
    <meta property="og:longitude" content="{$ogdata.longitude}"/>{/if}{if $ogdata.street_address} 
    <meta property="og:street-address" content="{$ogdata.street_address}"/>{/if}{if $ogdata.locality} 
    <meta property="og:locality" content="{$ogdata.locality}"/>{/if}{if $ogdata.region} 
    <meta property="og:region" content="{$ogdata.region}"/>{/if}{if $ogdata.postal_code} 
    <meta property="og:postal-code" content="{$ogdata.postal_code}"/>{/if}{if $ogdata.country_name} 
    <meta property="og:country-name" content="{$ogdata.country_name}"/>{/if}{if $ogdata.email} 
    <meta property="og:email" content="{$ogdata.email}"/>{/if}{if $ogdata.phone_number} 
    <meta property="og:phone_number" content="{$ogdata.phone_number}"/>{/if}{if $ogdata.fax_number} 
    <meta property="og:fax_number" content="{$ogdata.fax_number}"/>{/if}{if $ogdata.video} 
    <meta property="og:video" content="{$ogdata.video}" />{if $ogdata.video_height} 
    <meta property="og:video:height" content="{$ogdata.video_height}" />{/if}{if $ogdata.video_width} 
    <meta property="og:video:width" content="{$ogdata.video_width}" />{/if}
    <meta property="og:video:type" content="application/x-shockwave-flash" />{/if}
    <meta name="twitter:card" value="summary" />
    <meta name="twitter:url" value="{$ogdata.url}" />
    <meta name="twitter:title" value="{$ogdata.title}" />
    <meta name="twitter:description" value="{if $ogdata.description}{$ogdata.description}{/if}" />
    <meta name="twitter:image" value="{if $ogdata.image}{$ogdata.image}{elseif $pageimage}{$pageimage}{/if}" />{if Jojo::getOption('twitter_id','')} 
    <meta name="twitter:site" value="@{Jojo::getOption('twitter_id','')}" />{/if}{/if}
