// -------------------------------------------------------------------
// J(Universal?)TagEditor, JQuery plugin
// Copyright (C) 2007 Jay Salvat - http://www.jaysalvat.com/
// -------------------------------------------------------------------
// Textile tags example
// http://en.wikipedia.org/wiki/Textile_(markup_language)
// http://www.textism.com/
// -------------------------------------------------------------------
// Feel free to add more tags
// -------------------------------------------------------------------
[
	{label:"H1", accessKey:"1", closeTag:"", openTag:"\nh1. "}, 
	{label:"H2", accessKey:"2", closeTag:"", openTag:"\nh2. "}, 
	{label:"H3", accessKey:"3", closeTag:"", openTag:"\nh3. "}, 
	{label:"H4", accessKey:"4", closeTag:"", openTag:"\nh4. "}, 
	{label:"H5", accessKey:"5", closeTag:"", openTag:"\nh5. "}, 
	{label:"H6", accessKey:"6", closeTag:"", openTag:"\nh6. "}, 
	{label:"Picture", accessKey:"", closeTag:"", openTag:"!@Source@!"}, 
	{label:"Link", accessKey:"", closeTag:"\":@Link@", openTag:"\""}, 
	{label:"Bold", accessKey:"b", closeTag:"*", openTag:"*"}, 
	{label:"Italic", accessKey:"i", closeTag:"_", openTag:"_"}, 
	{label:"Stroke", accessKey:"s", closeTag:"-", openTag:"-"}, 
	{label:"Superscript", accessKey:"", closeTag:"^", openTag:"^"}, 
	{label:"subscript", accessKey:"", closeTag:"~", openTag:"~"}, 
	{label:"Table row", accessKey:"", closeTag:"|", openTag:"\n|"}, 
	{label:"Table col", accessKey:"", closeTag:"", openTag:"|"}, 
	{label:"Bulleted list", accessKey:"", closeTag:"", openTag:"* "}, 
	{label:"Numeric list", accessKey:"", closeTag:"", openTag:"# "}, 
	{label:"Blockquote", accessKey:"", closeTag:"", openTag:"\nbq. "}, 
	{label:"Code", accessKey:"", closeTag:"@", openTag:"@"}, 
	{label:"Close Tags", accessKey:"<", callBack:"closeAll"}, 
	{label:"Preview", accessKey:"", callBack:"preview"}
]