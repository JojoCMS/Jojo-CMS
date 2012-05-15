{if $htmldoctype}<!doctype html>
{else}{$xmldoctype}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{/if}{if $boilerplatehtmltag}<!--[if lt IE 7 ]> <html xmlns="http://www.w3.org/1999/xhtml" {if $ogxmlns && !$isadmin}{$ogxmlns} {/if}lang="{if $pg_htmllang}{$pg_htmllang}{elseif $pg_language}{$pg_language}{else}en{/if}" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html xmlns="http://www.w3.org/1999/xhtml" {if $ogxmlns && !$isadmin}{$ogxmlns} {/if}lang="{if $pg_htmllang}{$pg_htmllang}{elseif $pg_language}{$pg_language}{else}en{/if}" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html xmlns="http://www.w3.org/1999/xhtml" {if $ogxmlns && !$isadmin}{$ogxmlns} {/if}lang="{if $pg_htmllang}{$pg_htmllang}{elseif $pg_language}{$pg_language}{else}en{/if}" class="no-js ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html xmlns="http://www.w3.org/1999/xhtml" {if $ogxmlns && !$isadmin}{$ogxmlns} {/if}lang="{if $pg_htmllang}{$pg_htmllang}{elseif $pg_language}{$pg_language}{else}en{/if}" class="no-js"> <!--<![endif]-->
{else}<html xmlns="http://www.w3.org/1999/xhtml" {if $ogxmlns && !$isadmin}{$ogxmlns} {/if}xml:lang="{if $pg_htmllang}{$pg_htmllang}{elseif $pg_language}{$pg_language}{else}en{/if}" lang="{if $pg_htmllang}{$pg_htmllang}{elseif $pg_language}{$pg_language}{else}en{/if}">
{/if}
