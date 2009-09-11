// ----------------------------------------------------------------------------
// j(Universal?)TagEditor, JQuery plugin
// ----------------------------------------------------------------------------
// Copyright (C) 2007 Jay Salvat
// http://www.jaysalvat.com/jquery/jtageditor/
// ----------------------------------------------------------------------------
// Html tags
// http://en.wikipedia.org/wiki/html
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
[
	{label:"H1", accessKey:"1", closeTag:"</h1>", openTag:"<h1>" },
	{label:"H2", accessKey:"2", closeTag:"</h2>", openTag:"<h2>" },
	{label:"H3", accessKey:"3", closeTag:"</h3>", openTag:"<h3>" },
	{label:"H4", accessKey:"4", closeTag:"</h4>", openTag:"<h4>" },
	{label:"H5", accessKey:"5", closeTag:"</h5>", openTag:"<h5>" },
	{label:"H6", accessKey:"6", closeTag:"</h6>", openTag:"<h6>" },
	{label:"Paragraph", accessKey:"p", closeTag:"</p>", openTag:"<p>" }, 
	{label:"Picture", accessKey:"", closeTag:"", openTag:"<img src=\"@Source@\" alt=\"@Alt@\" />" },
	{label:"Link", accessKey:"", closeTag:"</a>", openTag:"<a href=\"@Link@\" title=\"@Title@\">" },
	{label:"Bold", accessKey:"b", closeTag:"</strong>", openTag:"<strong>" },
	{label:"Italic", accessKey:"i", closeTag:"</em>", openTag:"<em>" },
	{label:"Stroke", accessKey:"s", closeTag:"</del>", openTag:"<del>" },
	{label:"Superscript", accessKey:"", closeTag:"</sup>", openTag:"<sup>" },
	{label:"Subscript", accessKey:"", closeTag:"</sub>", openTag:"<sub>" },
	{label:"Table", accessKey:"", closeTag:"\n</table>", openTag:"<table>\n" },
	{label:"Tr", accessKey:"", closeTag:"</tr>", openTag:"<tr>" },
	{label:"Td", accessKey:"", closeTag:"</td>", openTag:"<td>" },
	{label:"Ul", accessKey:"", closeTag:"\n</ul>\n", openTag:"<ul>\n" },
	{label:"Ol", accessKey:"", closeTag:"\n</ol>\n", openTag:"<ol>\n" },
	{label:"Li", accessKey:"", closeTag:"</li>", openTag:"<li>" },
	{label:"Blockquote", accessKey:"", closeTag:"</blockquote>\n",openTag:"\n<blockquote>" },
	{label:"Code", accessKey:"", closeTag:"</code>", openTag:"<code>" },
	{label:"Comment", accessKey:"", closeTag:"-->", openTag:"<!--" },
	{label:"Close Tags", accessKey:"<", callBack:"closeAll" },
	{label:"Clean Tags", accessKey:"", callBack:"cleanAll" },
	{label:"Preview", accessKey:"", callBack:"preview" },
	<?php
        $contentvars = Jojo::getContentVars();
        foreach ($contentvars as $v) {
            echo '{label:"'.$v['name'].'", accessKey:"", closeTag:"", openTag:"'.$v['jtagformat'].'"}, ';
        }
        ?>
]