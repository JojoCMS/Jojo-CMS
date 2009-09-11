// -------------------------------------------------------------------
// J(Universal?)TagEditor, JQuery plugin
// Copyright (C) 2007 Jay Salvat - http://www.jaysalvat.com/
// -------------------------------------------------------------------
// Wiki tags example
// http://meta.wikimedia.org/wiki/Help:Editing
// -------------------------------------------------------------------
// Feel free to add more tags
// -------------------------------------------------------------------
[ 
	{label:"Section", accessKey:"1", closeTag:"=", openTag:"="}, 
	{label:"Picture", accessKey:"", closeTag:"", openTag:"[[Image:@Url@|@Title@]]"}, 
	{label:"Media", accessKey:"", closeTag:"", openTag:"[[Media:@Url@|@Title@]]"},  
	{label:"Internal Link", accessKey:"", closeTag:"]]", openTag:"[["}, 
	{label:"External Link", accessKey:"", closeTag:"]", openTag:"["}, 
	{label:"Bold", accessKey:"b", closeTag:"'''", openTag:"'''"}, 
	{label:"Italic", accessKey:"i", closeTag:"''", openTag:"''"}, 
	{label:"Formula", accessKey:"", closeTag:"</math>", openTag:"<math>"}, 
	{label:"Table", accessKey:"", closeTag:"\n|}", openTag:"{|\n|+ @Title@\n"}, 
	{label:"Table row", accessKey:"", closeTag:"", openTag:"|- \n"}, 
	{label:"Table col", accessKey:"", closeTag:"", openTag:"| "}, 
	{label:"Bulleted list", accessKey:"", closeTag:"", openTag:"*"}, 
	{label:"Numeric list", accessKey:"", closeTag:"", openTag:"#"}, 
	{label:"Ident", accessKey:"", closeTag:"", openTag:":"}, 
	{label:"No Wiki", accessKey:"", closeTag:"</nowiki>", openTag:"<nowiki>"}, 
	{label:"Category", accessKey:"", closeTag:"", openTag:"[[Category:@Category name@]]"}, 
	{label:"Signature", accessKey:"", closeTag:"", openTag:"--~~~~"}, 
	{label:"Close Tags", accessKey:"<", callBack:"closeAll"}, 
	{label:"Preview", accessKey:"", callBack:"preview"}
]