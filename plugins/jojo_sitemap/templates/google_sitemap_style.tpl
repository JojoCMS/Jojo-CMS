{literal}<?xml version="1.0" encoding="UTF-8"?>

<!-- Google Sitmaps Stylesheets (GSStylesheets)
     Project Home: http://sourceforge.net/projects/gstoolbox
     Copyright (c) 2005 Baccou Bonneville SARL (http://www.baccoubonneville.com)
     License http://www.gnu.org/copyleft/lesser.html GNU/LGPL

     Created by Serge Baccou
     1.0 / 20 Aug 2005

     Changes by Johannes Müller (http://GSiteCrawler.com)
     1.1 / 20 Aug 2005 - sorting by clicking on column headers
                       - open urls in new window/tab
                       - some stylesheet/CSS cleanup
     1.5a/ 31 Aug 2005 - added version number in footer
                       - removed images (don't allow tracking on other servers)

     Changes by Tobias Kluge (http://enarion.net)
     1.2 / 22 Aug 2005 - moved sitemap file and sitemap index file into one file gss.xsl
	 1.5 / 27 Aug 2005 - added js and css into xslt stylesheet; only gss.xsl is needed now

     Changes by Serge Baccou
     1.3 / 23 Aug 2005 - some XSLT cleanup
     1.4 / 24 Aug 2005 - sourceForge and LGPL links and logos
                       - sorting is working for siteindex (see gss.js)

     Changes by Andrew Berezin
     1.5 / 02 May 2007 - Support new sitemaps.org namespace 0.9.

     Changes by Michael Brandon (http://www.SearchMasters.co.nz)
     1.6 / 25 Apr 2008 - updating links, localising images and css for jojo instalation
-->

<xsl:stylesheet version="2.0"
                xmlns:html="http://www.w3.org/TR/REC-html40"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="html" version="1.0" encoding="iso-8859-1" indent="yes"/>

  <!-- Root template -->
  <xsl:template match="/">
    <html>

      <head>
        <title>Google Sitemap File</title>
        <link href="css/gss.css" type="text/css" rel="stylesheet"/>
        <script type="text/javascript" src="js/gss.js"></script>
      </head>

      <!-- Store in $fileType if we are in a sitemap or in a siteindex -->
      <xsl:variable name="fileType">
        <xsl:choose>
		  <xsl:when test="//sitemap:url">sitemap</xsl:when>

		  <xsl:otherwise>siteindex</xsl:otherwise>
        </xsl:choose>
      </xsl:variable>

      <!-- Body -->
      <body onLoad="initXsl('table0','{$fileType}');">

        <!-- Text and table -->
        <h1 id="head1">Google Sitemap</h1>

        <xsl:choose>
	      <xsl:when test="$fileType='sitemap'"><xsl:call-template name="sitemapTable"/></xsl:when>
	      <xsl:otherwise><xsl:call-template name="siteindexTable"/></xsl:otherwise>
  		</xsl:choose>

        <!-- Copyright notice
             &#x0020; means significant space character -->
        <br/>
        <table class="copyright" id="table_copyright">
          <tr>

            <td>
              <p>Google Sitemaps: copyright 2005 <a target="new" href="http://www.google.com">Google</a> - <a target="new" href="http://www.google.com/webmasters/tools/">My Sitemaps</a> - <a target="new" href="https://www.google.com/webmasters/tools/docs/en/about.html">About</a> - <a target="new" href="http://www.google.com/support/webmasters/bin/topic.py?topic=13450">FAQ</a> - <a target="new" href="http://groups.google.com/group/Google_Webmaster_Help">Discussion</a> - <a target="new" href="http://googlewebmastercentral.blogspot.com/">Blog</a></p>

              Google Sitemaps Stylesheets:  copyright 2005 <a target="new" href="http://www.baccoubonneville.com">Baccou Bonneville</a> - <a target="new" href="http://sourceforge.net/projects/gstoolbox">Project</a> - <a target="new" href="http://www.baccoubonneville.com/blogs/index.php/webdesign/2005/08/20/google-sitemaps-stylesheets">Blog</a><br/>
              Contributions: Johannes Muller, SOFTPlus <a target="new" href="http://gsitecrawler.com">GSiteCrawler</a> - Tobias Kluge, enarion.net <a target="new" href="http://enarion.net/google/phpsitemapng">phpSitemapNG</a>
              <p><a target="new" href="http://www.gnu.org/copyleft/lesser.html"><img src="images/lgpl.jpg" alt="LGPL" border="0"/></a>&#x0020;<a target="new" href="http://sourceforge.net/projects/gstoolbox"><img src="images/sourceforge.jpg" alt="SourceForge.net" border="0"/></a></p>

            </td>
          </tr>
        </table>
      </body>
    </html>
  </xsl:template>

  <!-- siteindexTable template -->
  <xsl:template name="siteindexTable">

    <h2>Number of sitemaps in this Google sitemap index: <xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"></xsl:value-of></h2>
    <p class="sml">Click on the table headers to change sorting.</p>
    <table border="1" width="100%" class="data" id="table0">
      <tr class="header">
        <td>Sitemap URL</td>
        <td>Last modification date</td>
      </tr>

      <xsl:apply-templates select="sitemap:sitemapindex/sitemap:sitemap">
        <xsl:sort select="sitemap:lastmod" order="descending"/>
      </xsl:apply-templates>
    </table>
  </xsl:template>

  <!-- sitemapTable template -->
  <xsl:template name="sitemapTable">
    <h2>Number of URLs in this Google Sitemap: <xsl:value-of select="count(sitemap:urlset/sitemap:url)"></xsl:value-of></h2>

    <p class="sml">Click on the table headers to change sorting.</p>
    <table border="1" width="100%" class="data" id="table0">
	  <tr class="header">
	    <td>Sitemap URL</td>
		<td>Last modification date</td>
		<td>Change freq.</td>
		<td>Priority</td>

	  </tr>
	  <xsl:apply-templates select="sitemap:urlset/sitemap:url">
	    <xsl:sort select="sitemap:priority" order="descending"/>
	  </xsl:apply-templates>
	</table>
  </xsl:template>

  <!-- sitemap:url template -->
  <xsl:template match="sitemap:url">

    <tr>
      <td>
        <xsl:variable name="sitemapURL"><xsl:value-of select="sitemap:loc"/></xsl:variable>
        <a target="new" href="{$sitemapURL}" ref="nofollow"><xsl:value-of select="$sitemapURL"></xsl:value-of></a>
      </td>
      <td><xsl:value-of select="sitemap:lastmod"/></td>
      <td><xsl:value-of select="sitemap:changefreq"/></td>
      <td><xsl:value-of select="sitemap:priority"/></td>
    </tr>

  </xsl:template>

  <!-- sitemap:sitemap template -->
  <xsl:template match="sitemap:sitemap">
    <tr>
      <td>
        <xsl:variable name="sitemapURL"><xsl:value-of select="sitemap:loc"/></xsl:variable>
        <a target="new" href="{$sitemapURL}"><xsl:value-of select="$sitemapURL"></xsl:value-of></a>
      </td>

      <td><xsl:value-of select="sitemap:lastmod"/></td>
    </tr>
  </xsl:template>

</xsl:stylesheet>{/literal}