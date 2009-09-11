The jojo_credits plugin is a system for including a smart credit link to the person / company who made the website.

This can be done manually, however this plugin helps you get the maximum benefit from these links.

Installation
============
1. Install the plugin.
2. Add between 1 and 16 variations of text to the "Webmaster credits" option on the "SEO" tab (see below).
3. Insert the Smarty variable {$credits} anywhere on your theme's template.tpl file, where you want the credits to appear.

Adding text variations
======================
The value for the "Webmaster credits" option is a newline separated list of links. You need to include between 1 and 16 links, in HTML format.

Example...
--------------------------
<a href="http://www.domain.com">Web design</a> by YOURCOMPANY
<a href="http://www.domain.com/services/">Web development</a> by YOURCOMPANY
<a href="http://www.domain.com/seo/">SEO</a> by YOURCOMPANY
<a href="http://www.domain.com/hosting/">Web hosting</a> by YOURCOMPANY
Powered by YOURCOMPANY <a href="http://www.domain.com/hosting/">Web hosting</a>
etc...
--------------------------

The intent is to use a variety of credit variations, with a variety of link text, pointing to your homepage, and also sub-pages.

Logic
=====
- Having a link with rich anchor text  (eg "Web design") is much more valuable to you than a link withyour company name as the anchor text. Using keyword-rich anchor text in your incoming links helps you rank better for these phrases.
- Having a mix of anchor texts is desireable. Having thousands of pages all with the same link, same anchor text does not look as natural as a random variation of anchor text. And you want to rank for a variety of phrases too.
- Your homepage probably has the lion's share of links at the moment. Help your important sub-pages rank better by sharing the links around.

Nofollow option
===============
The authors of this plugin are of 2 minds about how to use footer credit links. The default method above has worked very well in the past, but is a reasonably aggressive technique. We believe that too many sitewide links may look a little cheesy to the search engines, so we are now providing the option to nofollow all links except the one on the homepage. Enabling this option negates the benefit of having the variety of link text, but is safer, and you still get a great homepage link out of it, without bleeding the site of link juice.