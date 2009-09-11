// ----------------------------------------------------------------------------
// j(Universal?)TagEditor, JQuery plugin
// ----------------------------------------------------------------------------
// Copyright (C) 2007 Jay Salvat
// http://www.jaysalvat.com/jquery/jtageditor/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
[
	{label:"Bold", accessKey:"b", closeTag:"[/b]", openTag:"[b]"}, 
	{label:"Italic", accessKey:"i", closeTag:"[/i]", openTag:"[i]"}, 
	{label:"Underline", accessKey:"u", closeTag:"[/u]", openTag:"[u]"}, 
	
	{label:"H2", accessKey:"", closeTag:"[/h2]", openTag:"[h2]"}, 
	{label:"H3", accessKey:"", closeTag:"[/h3]", openTag:"[h3]"}, 
	{label:"H4", accessKey:"", closeTag:"[/h4]", openTag:"[h4]"}, 
	
	{label:"Align left", accessKey:"", closeTag:"[/align]", openTag:"[align=left]"}, 
	
	{label:"Link", accessKey:"l", closeTag:"[/url]", openTag:"[url=@Url (include http:// for external links)@]"}, 
	{label:"Link (nofollow)", accessKey:"l", closeTag:"[/url]", openTag:"[url=@Url (include http:// for external links)@ nofollow]"}, 
	{label:"Email", accessKey:"e", closeTag:"[/email]", openTag:"[email=@Email@]"}, 
	
	{label:"Bulleted list", accessKey:"", closeTag:"\n[/list]", openTag:"[list]\n[*]\n[*]\n[*]\n"}, 
	{label:"Numeric list", accessKey:"", closeTag:"\n[/list]", openTag:"[list=@Starting number@]\n[*]\n[*]\n[*]\n"}, 
	{label:"List item", accessKey:"", closeTag:"", openTag:"[*]"}, 
	
	{label:"Picture", accessKey:"p", closeTag:"", openTag:"[img]@Url@[/img]"}, 
	
	{label:"Citation / Quote", accessKey:"", closeTag:"[/quote]", openTag:"[quote=@Author@]"}, 
	{label:"Code", accessKey:"", closeTag:"[/code]", openTag:"[code]"}, 
	{label:"Codeblock", accessKey:"", closeTag:"[/codeblock]", openTag:"[codeblock]"}, 
	
	{label:"Close Tags", accessKey:"<", callBack:"closeAll"}, 
	{label:"Clean Tags", accessKey:"", callBack:"cleanAll"}
	//{label:"Preview", accessKey:"", callBack:"preview"}
]