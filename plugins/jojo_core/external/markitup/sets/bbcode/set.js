// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
myBbSettings = {
	previewParserPath:	'', // path to your BBCode parser
	markupSet: [
		{name:'Heading 2', key:'2', openWith:'[h2(!( class="[![Class]!]")!)]', closeWith:'[/h2]', placeHolder:'Your title here...' },
		{name:'Heading 3', key:'3', openWith:'[h3(!( class="[![Class]!]")!)]', closeWith:'[/h3]', placeHolder:'Your title here...' },
		{name:'Heading 4', key:'4', openWith:'[h4(!( class="[![Class]!]")!)]', closeWith:'[/h4]', placeHolder:'Your title here...' },
		{name:'Heading 5', key:'5', openWith:'[h5(!( class="[![Class]!]")!)]', closeWith:'[/h5]', placeHolder:'Your title here...' },
		{name:'Heading 6', key:'6', openWith:'[h6(!( class="[![Class]!]")!)]', closeWith:'[/h6]', placeHolder:'Your title here...' },
		{separator:'---------------' },
		{name:'Bold', key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name:'Italic', key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name:'Underline', key:'U', openWith:'[u]', closeWith:'[/u]'},
		{separator:'---------------' },
		{name:'Picture', key:'P', replaceWith:'[img][![Url]!][/img]'},
		{name:'Link', key:'L', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...'},
		{name:'Link (nofollow)', key:'L', openWith:'[url=[![Url]!] nofollow]', closeWith:'[/url]', placeHolder:'Your text to link here...'},
		{name:'Email', key:'E', openWith:'[email=[![Email]!]]', closeWith:'[/email]', placeHolder:'Your text to link here...'},
		{separator:'---------------' },
		{name:'Size', key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]',
		dropMenu :[
			{name:'Big', openWith:'[size=200]', closeWith:'[/size]' },
			{name:'Normal', openWith:'[size=100]', closeWith:'[/size]' },
			{name:'Small', openWith:'[size=50]', closeWith:'[/size]' }
		]},
		{separator:'---------------' },
		{name:'Bulleted list', openWith:'[list]\n', closeWith:'\n[/list]'},
		{name:'Numeric list', openWith:'[list=[![Starting number]!]]\n', closeWith:'\n[/list]'}, 
		{name:'List item', openWith:'[*] '},
		{separator:'---------------' },
		{name:'Quotes', openWith:'[quote]', closeWith:'[/quote]'},
		{name:'Code', openWith:'[code]', closeWith:'[/code]'}, 
		{separator:'---------------' },
		{name:'Clean', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		{name:'Preview', className:"preview", call:'preview' }
	]
}