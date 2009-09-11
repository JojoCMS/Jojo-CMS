If you intend to use BBCode on your site, you will need to install this plugin, or include a few extra styles in your stylesheet.

There are certain BBCode tags that rely on CSS classes being defined, either here or in your theme's CSS file.

If you don't install this plugin, please ensure you cater for the following styles that you need in your theme's CSS file.
--------------------------
/* BBCode [align=right][/align] */
div.right {
  float: right;
}

/* BBCode [align=left][/align] */
div.left {
  float: left;
}

/* BBCode [clear] */
div.clear {
  clear: both;
}

/* BBCode [quote][/quote] */
div.quote {
  border: 1px solid #777;
  padding: 0 10px;
}

/* BBCode [code][/code] or [codeblock][/codeblock] */
.code, .codeblock {
  font-family: Consolas, "Bitstream Vera Sans Mono", "Courier New", Courier, monospace, serif;
}

/* BBCode [align=center][/align] or [center][/center] */
.center {
  text-align: center;
}

/* BBCode [info][/info] */
.info {

}
--------------------------