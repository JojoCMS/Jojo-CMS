<?php
header('Content-type: text/css');
header('Cache-Control: private, max-age=28800');
header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
header('Pragma: ');
?>/* -------------------------------------------------------------------
// BBCode Editor Skin
// j(Universal?)TagEditor, JQuery plugin
// By Jay Salvat - http://www.jaysalvat.com/jquery/jtageditor/
// -------------------------------------------------------------------
// Icons based on http://www.famfamfam.com/
// ------------------------------------------------------------------*/
.jTagBB {

}
.jTagBB .jTagEditor-toolBar {
	list-style:none;
}
.jTagBB .jTagEditor-toolBar ul	{
	margin:0px; padding:0px;
}
.jTagBB .jTagEditor-toolBar li	{
	float:left;
	margin: 0;
	margin-bottom:5px;
}
.jTagBB .jTagEditor-toolBar a	{
	display:block;
	width:16px; height:16px;
	margin:1px 3px;
	text-indent:-1000px;
	overflow:hidden;
}
.jTagBB .jTagEditor-editor {
	font:12px "Courier New", Courier, monospace;
	padding:5px 5px 5px 35px; margin-top:10px;
	border:3px solid #666666;
	width:100%;
	height:320px;
	background-image:url(../_images/bg-bbcode.png);
	background-repeat:no-repeat;
	clear:both; display:block;
	line-height:18px;
}
.jTagBB .jTagEditor-button1 a	{
	background-image:url(../_icons/bold.png);
}
.jTagBB .jTagEditor-button2 a	{
	background-image:url(../_icons/italic.png);
}
.jTagBB .jTagEditor-button3 a	{
	background-image:url(../_icons/underline.png);
	margin-right:20px;
}
.jTagBB .jTagEditor-button4 a	{
	background-image:url(../_icons/h2.png);
}
.jTagBB .jTagEditor-button5 a	{
	background-image:url(../_icons/h3.png);
}
.jTagBB .jTagEditor-button6 a	{
	background-image:url(../_icons/h4.png);
	margin-right:20px;
}
.jTagBB .jTagEditor-button7 a	{
	background-image:url(../../../images/cms/icons/text_align_left.png);
	margin-right:20px;
}
.jTagBB .jTagEditor-button8 a	{
	background-image:url(../_icons/link.png);
}
.jTagBB .jTagEditor-button9 a	{
	background-image:url(../../../images/cms/icons/link_error.png);
}
.jTagBB .jTagEditor-button10 a	{
	background-image:url(../../../images/cms/icons/email.png);
	margin-right:20px;
}
.jTagBB .jTagEditor-button11 a	{
	background-image:url(../_icons/list-bullets.png);
}
.jTagBB .jTagEditor-button12 a	{
	background-image:url(../_icons/list-numbers.png);
}
.jTagBB .jTagEditor-button13 a	{
	background-image:url(../_icons/list-item.png);
	margin-right:20px;
}
.jTagBB .jTagEditor-button14 a	{
	background-image:url(../_icons/picture.png);
	margin-right:20px;
}
.jTagBB .jTagEditor-button15 a	{
	background-image:url(../_icons/comments.png);
}
.jTagBB .jTagEditor-button16 a	{
	background-image:url(../_icons/tags.png);
}
.jTagBB .jTagEditor-button17 a	{
	background-image:url(../_icons/tags.png);
	margin-right:20px;
}
.jTagBB .jTagEditor-button18 a	{
	background-image:url(../_icons/tags-close.png);
}
.jTagBB .jTagEditor-button19 a	{
	background-image:url(../_icons/tags-delete.png);
}

<?php
/*
.jTagBB .jTagEditor-button20 a	{
	background-image:url(../_icons/preview.png);
}
*/

$contentvars = Jojo::getContentVars();
$i = 20;
foreach ($contentvars as $v) {
echo ".jTagBB .jTagEditor-button".$i++." a {
	background-image:url(../../../".$v['icon'].");
}
";
}
?>
.jTagBB .jTagEditor-resizeHandle {
	width:16px; height:5px;
	margin:0px 0 0 394px;
	background-image:url(../_icons/handle.png);
	cursor:n-resize;
}