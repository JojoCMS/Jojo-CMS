<?php
/**
 *
 * bbconverter.class.php
 *
 * A BBCode converter class designed for forums and content management systems. BBCode is not a formal standard
 but is a collection of html-like tags that contain basic functionality and are easy to use.
 BBCode is most commonly used where rich formatting is required, but you don't what random visitors posting
 HTML content, which might include javascript, improperly formed tags etc.

 There are a number of functions available for processing bbcode, and most of them have one major drawback -
 they can't handle nested tags. The nature of regular expression matching makes it hard to know which
 open-close tag combination the parser should be matching. This is a problem where users quote users who have
 quoted other users (and so on). This class was written to handle nested tags, particularly nested [quote] tags
 which will happen frequently on forums.

 This class also tries to keep code easy to read. It will correctly indent code where nested tags occur, and
 apply line breaks where it is logical (headings and list items should be on their own line, links don't need
 to be). To help prevent spam, external links are marked as nofollws by default. If the appropriate image
 handling scripts are in place, images can also be cached to speed things up.

 The class will convert BBCode to HTML, but also HTML to BBCode. The HTML to BBCode is very useful when converting
 a website. It doesn't do a perfect job, and most complicated tags are stripped. I have found it does an excellent
 job of removing "tag soup", created by WYSIWYG editors. Certainly a good place to start when giving those old
 sites a polish in the quest for validation. It can also convert HTML to clean HTML, maintaining a logical indent
 structure.

 As with most BBCode converters, this class relies heavily on regular expressions. Expect the class to be expensive
 on CPU resource when you run this on large files, or repeatedly on a page. It is therefore a good idea to cache
 the output of this script, rather than running the script on every request. I store 2 fields in the database - one
 for the BBCode which is what you will edit. Another field for the output, which is updated every time the record is
 saved. Your mileage may vary.

 Here is a basic rundown of bbcode this class uses

 [b]bold[/b]
 [i]italic[/i]
 [u]underline[/u]
 [align=center]center aligned text[/align]              - Options: left, right, center
 [center]center aligned text[/center]                   - "centre" will also work for non-americans
 [left]left aligned text[/left]
 [right]right aligned text[/right]
 [img]http://www.domain.com/image.jpg[/img]             - an external image
 [img]image.jpg[/img] - an internal image
 [img=This is an image]image.jpg[/img]                  - an image with alt tag
 [url=http://www.domain.com]Click here[/url]            - an external hyperlink
 [url=http://www.domain.com lightbox][img][/img][/url]   - go to a lightbox image
 [url=http://www.domain.com nofollow]Click here[/url]   - an external hyperlink - nofollowed
 [url=http://www.domain.com new]Click here[/url]        - an link to be opened in new window
 [url=mypage.htm]Click here[/url]                       - an internal hyperlink
 [email=harvey@harveykane.com]Email me[/email]
 [h1]Headings[/h1]                                      - h1 through h6
 [hr]                                                   - horizontal rule
 [br]                                                   - force a line break
 [list]
 [*]item 1
 [*]item 2
 [/list]

 CUSTOM TAGS
 [popup=Popup Title Here]Popup content here[/popup
 [db=tablename, 165]Link Title here[/db] - a link to tablename.php?id=165

 * @version 0.11
 * @copyright 2005 Harvey Kane <info@harveykane.com>
 * @author Harvey Kane <info@harveykane.com>
 *
 * Usage:
 * //to convert html to bbcode
 * $bb->setHtmlFromFile('data.htm');
 * echo $bb->convert('bbcode');
 * //to convert bbcode to html
 * $bb->setBbCodeFromFile('bbdata.php');
 * echo $bb->convert('bbcode2html');
 *
 * TODO:
 * Auto close on all open tags
 * Tables support via [table] [row] [cell]
 * EMAIL OBFUSCATION
 * If Email obfuscation is enabled (default), then all emails are obfuscated automatically to prevent spam.
 * The following Javascript function is required in the head or attached Javascript file...
 *
 function xyz(c,a,b)
 {
 var o = '';
 var m = '';
 var m2 = ':otliam';
 for (i = 0; i <= b.length; i++) {
 o = b.charAt (i) + o;
 }
 b = o;
 for (i = 0; i <= m2.length; i++) {
 m = m2.charAt (i) + m;
 }
 return m + a + unescape('%'+'4'+'0') + b + '.' + c;
 }
 * This method of obfuscation requires Javascript enabled in on the client. If javascript is not enabled, then
 * the link will take the user to the "contact us" page, where it would be good to have a form they can use
 * instead.
 *
 *
 * This file may be used and distributed subject to the LGPL available from http://www.fsf.org/copyleft/lgpl.html
 * If you find this script useful, I would appreciate a link back to http://www.harveykane.com
 * or simply an email to let me know you like it :)
 * This script is distributed as is, there are no implied guarantees that the code works, or won't
 * crash your server / burn your house down / cause you to put on 10kg.
 *
 **/

class bbConverter
{
    /* Config variables - set these via calling script */
    //source code that will be converted
    var $source;
    //true or false - determines whether a link is for the lightbox plugin
    var $lightbox = false;
    //true or false - determines whether external links should be rel="nofollow" which discourages blog/forum spam
    var $nofollow = false;
    //true or false - determines whether external links should be target="_BLANK"
    var $newwindow = true;
    //TODO - true or false - Obfuscate email addresses to prevent spam - Obfuscation requires some JS code detailed above
    var $obfuscate = true;
    //The address of the "contact us" page - only required if emails are obfuscated
    var $contactpage = 'contact/';
    //the default target of external links. default = "new" (a new window that gets re-used if another link is clicked). other options "_BLANK" (new window every time) or "" (open in current window)
    var $externaltarget = 'new';
    //As above, but for internal links. Internal links would normally open in the same window.
    var $internaltarget = '';
    //This will cache and resize images on this domain, can speed things up significantly. Requires Image manipulation files from Alexandria CMS
    var $imagecache = true;
    //Apply image dropshadows as described in http://www.alistapart.com/articles/cssdropshadows/ Requires CSS from the article for this markup to work
    var $imagedropshadow = true;
    //an array of classes to use for any images
    var $imageclass = array();
    //Location of smiley images
    var $smileyfolder = 'images/cms/smilies';
    //Causes long URLs to be truncated - useful to prevent pages from overflowing horizontally. 0 means don't truncate.
    var $truncateurl = 0;

    /* Magazine Layout Variables - see magazinelayout.class.php */
    var $magwidth = 600;
    var $magpadding = 3;
    var $magtemplate = "<a href=\"images/650/[image]\" rel=\"lightbox\" onclick=\"return false;\"><img src=\"images/[size]/[image]\" alt=\"\" /></a>";

    /* Internal use only */
    var $_html;
    var $_bbcode;
    //an array of all tags, content and meta information
    var $_dom = array();
    var $_bbdom = array();
    //used for matching </a> tags to either [/email] or [/url] depending on the start tag
    var $_lastlink = '';
    //as above, but for lists <ul> and <ol>
    var $_lastlist = '';

    function bbConverter()
    {
    }

    /* ShortenURL */
    /* This function truncates a URL so it does not stretch a page. It is included outside the class so it will work within the context of create_function() */
    function _shortenURL($url, $limit = 30, $add = '...')
    {
        //return preg_replace("!(http:/{2}[\w\.]{2,}[/\w\-\.\?\&\=\#]*)!e", "'<a href=\"\\1\" title=\"\\1\" target=\"_blank\">'.(strlen('\\1')>=$chr_limit ? substr('\\1', 0,$chr_limit) . '$add':'\\1') . '</a>'", $url);
        return preg_replace("!(http:/{2}[\w\.]{2,}[/\w\-\.\?\&\=\#]*)!e", "''.(strlen('\\1')>=$limit ? substr('\\1', 0,$limit) . '$add':'\\1') . ''", $url);
    }

    function setHtml($html)
    {
        $this->_html = $html;
        $this->_parseHtml();
    }

    function setHtmlFromFile($filename)
    {
        $data = file($filename);
        $this->_html = '';
        for ($i = 0; $i < count($data); $i++) {
            $this->_html .= $data[$i];
            $data[$i] = htmlentities($data[$i], ENT_COMPAT, 'UTF-8') . "<br />\n";
        }
        $this->_parseHtml();
    }

    function setBbCode($bbcode)
    {
        $this->_bbcode = htmlentities($bbcode, ENT_COMPAT, 'UTF-8');
        $this->_parseBbCode();
    }

    function setBbCodeFromFile($filename)
    {
        $data = file($filename);
        $this->_bbcode = '';
        for ($i = 0; $i < count($data); $i++) {
            $this->_bbcode .= $data[$i];
            $data[$i] = htmlentities($data[$i], ENT_COMPAT, 'UTF-8') . "<br />\n";
        }
        $this->_parseBbCode();
    }

    /* ************* getElementType ************
     Uses Regex to find the type of element in $source. eg. <div class="foo"> will return "open-div"
     Differentiates between opening and closing tags (and tags that don't close such as <br>), and
     returns an arbitary code that is simpler than the tag itself. The output of this function is
     used in getElementBehaviour().
     */
    function _getElementType($source)
    {
        //doctype
        if (preg_match("/<\!?doctype(.*?)>/i", $source)) {
            return 'doctype';
            //br
        } elseif (preg_match("/<br ?\/{0, 1}>/i", $source)) {
            return 'break';
            //comment
        } elseif (preg_match("/<!--(.*?)-->/i", $source)) {
            return 'comment';
            //meta,img,link,input,base
        } elseif (preg_match("/<(meta|img|link|input|base)(.*?)>/i", $source, $matches)) {
            return strtolower($matches[1]);
            //hr
        } elseif (preg_match("/<hr ?\/{0, 1}>/i", $source)) {
            return 'horizontal-rule';
            //general tags
        } elseif (preg_match("/<([a-z0-9]*?)>/i", $source, $matches)) {
            return 'open-' . strtolower($matches[1]);
        } elseif (preg_match("/<\/([a-z0-9]*?)>/i", $source, $matches)) {
            return 'close-' . strtolower($matches[1]);
            //general with attributes
        } elseif (preg_match("/<([a-z0-9]*?) (.*?)>/i", $source, $matches)) {
            return 'open-' . strtolower($matches[1]);
        } else {
            //content is literal text, if nothing else matches
            return 'content';
        }
    }

    /* ************* getElementBehaviour ************
     based on the element type calculated in getElementType() this function
     will determine whether newlines should be added before or after the tag.
     For example, newlines are generally favourable before and after <br /> tags,
     but <b>bold</b> tags are better staying inline.
     */
    function _getElementBehaviour($type)
    {
        $behaviour = array();
        switch ($type) {
            case "open-h1":
            case "open-h2":
            case "open-h3":
            case "open-h4":
            case "open-h5":
            case "open-h6":
            case "open-p":
            case "open-div":
            case "open-li":
                $behaviour['startbreak'] = true;
                $behaviour['endbreak'] = false;
                break;
            case "close-h1":
            case "close-h2":
            case "close-h3":
            case "close-h4":
            case "close-h5":
            case "close-h6":
            case "close-p":
            case "close-div":
            case "close-li":
                $behaviour['startbreak'] = false;
                $behaviour['endbreak'] = true;
                break;
            case "content":
            case "open-a":
            case "close-a":
            case "open-span":
            case "close-span":
            case "open-u":
            case "close-u":
            case "open-b":
            case "close-b":
            case "open-strong":
            case "close-strong":
            case "open-i":
            case "close-i":
            case "open-em":
            case "close-em":
                $behaviour['startbreak'] = false;
                $behaviour['endbreak'] = false;
                break;
            case "break":
            case "doctype":
                $behaviour['startbreak'] = true;
                $behaviour['endbreak'] = true;
                break;
            default:
                $behaviour['startbreak'] = true;
                $behaviour['endbreak'] = true;
        }
        return $behaviour;
    }

    /* ************* _parseHtml ************
     Reads $html and populates the DOM array.
     */
    function _parseHtml()
    {
        $matches = array();
        //clear this variable
        $this->_dom = array();
        //clear this variable
        $this->_bbdom = array();

        /* Trim whitespace from start of lines in HTML */
        $lines = explode("\n", $this->_html);
        $n = count($lines);
        for ($i = 0; $i < $n; $i++) {
            $lines[$i] = ltrim($lines[$i]);
            if ($lines[$i])
                $lines[$i] = ltrim($lines[$i]);
        }
        $this->_html = implode("\n", $lines);

        /* Patterns for matching tags and plain content */
        //match <tag>
        $pattern1 = "/<(.+?)>/is";
        //match content up to the next tag
        $pattern2 = "/[^<]*/is";

        /* used for managing the loop */
        $startstr = '1';
        $endstr = '2';

        $j = 0;
        $k = 0;

        /* repeat until no more matches are found within the document */
        while ($startstr != $endstr) {
            //used for managing the loop
            $startstr = $this->_html;
            $matches = array();
            /* look for HTML tags */
            preg_match($pattern1, $this->_html, $matches);
            $this->_html = preg_replace($pattern1, "", $this->_html, 1);
            $this->_dom[$j] = array();
            //source is the full tag
            $this->_dom[$j]['source'] = $matches[0];
            //lookup element type
            $this->_dom[$j]['element'] = $this->_getElementType($this->_dom[$j]['source']);
            //lookup bbcode equivalent
            $this->_dom[$j]['bbcode'] = $this->_htmlTag2BbTag($this->_dom[$j]['source']);
            if (is_null($this->_dom[$j]['bbcode']) || $this->_dom[$j]['bbcode'] != '') {
                $this->_bbdom[$k] = array();
                $this->_bbdom[$k]['source'] = $matches[0];
                $this->_bbdom[$k]['element'] = $this->_dom[$j]['element'];
                $this->_bbdom[$k]['bbcode'] = $this->_dom[$j]['bbcode'];
                $k++;
            }
            $j++;

            /* look for text content between tags */
            $matches = array();
            preg_match($pattern2, $this->_html, $matches);
            $this->_html = preg_replace($pattern2, "", $this->_html, 1);
            if (trim($matches[0]) != '') {
                //ignore any whitespace only entries
                //TEMP!!
                $matches[0] = trim($matches[0], "\r\n");
                $this->_dom[$j] = array();
                //source is the content //Trim added 24/1/06 to tidy up <li> tags
                $this->_dom[$j]['source'] = $matches[0];
                //These are always of type "content"
                $this->_dom[$j]['element'] = 'content';
                //The bbcode is simply the content //Trim added 24/1/06 to tidy up <li> tags
                $this->_dom[$j]['bbcode'] = $matches[0];

                $this->_bbdom[$k] = array();
                $this->_bbdom[$k]['source'] = $this->_dom[$j]['source'];
                $this->_bbdom[$k]['element'] = $this->_dom[$j]['element'];
                $this->_bbdom[$k]['bbcode'] = $this->_dom[$j]['bbcode'];

                $j++;
                $k++;
            }

            //used for managing the loop
            $endstr = $this->_html;
        }
    }

    /* TODO: to be documented properly... */
    function _recursiveScandir($dir = './', $sort = 0)
    {
        $dir_open = @ opendir($dir);
        if (!$dir_open)
            return false;
        while (($dir_content = readdir($dir_open)) !== false)
            $files[] = $dir_content;
        if ($sort == 1)
            rsort($files, SORT_STRING);
        else
            sort($files, SORT_STRING);
        return $files;
    }

    //function _callback_url($matches)
    //{
    //    return "[url=" . $matches[1]."]foo[/url]";
    //}

    /* ************* _parseBbCode ************
     Reads $html and populates the DOM array.
     */
    function _parseBbCode()
    {
        //Remove any Javascript BBTags - these can only be added by this script
        $this->_bbcode = preg_replace("/\[javascript\]/i", '', $this->_bbcode);
        $this->_bbcode = preg_replace("/\[\/javascript\]/i", '', $this->_bbcode);

        /* PHPBB has many tags with ids eg [quote:424c92552d]Quote here[/quote:424c92552d] - these need to be removed */
        $this->_bbcode = preg_replace("/\:[a-z0-9]{10}\]/i", ']', $this->_bbcode);
        $this->_bbcode = preg_replace("/\:[a-z0-9]{10}\=/i", '=', $this->_bbcode);

        /* Split out "quoted by" text from quote tags for easier processing */
        $this->_bbcode = preg_replace("/\[quote=\"?(.*?)\"?\]/i", '[quote][b]\\1 said...[/b][br]', $this->_bbcode);

        /* Custom POPUP tags [popup=popup title here]content here[/popup] */
        $this->_bbcode = preg_replace("/\[popup=\"?(.*?)\"?\]/i", "[jsurl=showhide('\\1');]\\1[/jsurl][popup=\\1]", $this->_bbcode);

        /* Preprocess difficult BBCode tags */
        //remove return characters
        $this->_bbcode = str_replace("\r", "", $this->_bbcode);
        //$this->_bbcode = str_replace("\n\n", "\n",$this->_bbcode); //Remove double newlines - this allows source code to be spaced so that it is easy to read, without causing the output to have too many fixed line breaks
        //replace newlines with <br />
        $this->_bbcode = str_replace("\n", "[br]\n", $this->_bbcode);

        /* Add a bbcode </li> equivalent to lists */
        //$this->_bbcode = preg_replace("/\[\*\](.*?)\[br\]/i", '[*]\\1[/*]', $this->_bbcode); //some list items take more than one line
        $this->_bbcode = preg_replace("/\[\*\]\[br\]/i", '[*]', $this->_bbcode);
        //these 2 lines work through issues relating to [br] at the end of list items
        $this->_bbcode = preg_replace("/\[\*\](.*?)\n(\[\*\]|\[\/list])/i", "[*]\\1[/*]\n\\2", $this->_bbcode);
        $this->_bbcode = preg_replace("/\[\*\](.*?)\[br\]\n(\[\*\]|\[\/list])/i", "[*]\\1[/*]\n\\2", $this->_bbcode);
        $this->_bbcode = preg_replace("/\[\*\](.*?)\[\/list\]/i", '[*]\\1[/*][/list]', $this->_bbcode);
        //$this->_bbcode = preg_replace("/\[\*\](.*?)(\[\*\]|\[\/list])/i", '[*]\\1[/*]\\2', $this->_bbcode);
        //Remove any occurances of [br][/*] as the [br] is not required
        $this->_bbcode = preg_replace("/\[br\]\[\/\*\]/i", "[/*]", $this->_bbcode);
        //Remove any occurances of [list][br] as the [br] is not required
        $this->_bbcode = preg_replace("/\[list\]\[br\]/i", "[list]", $this->_bbcode);

        //Remove any occurances of [tr/td/h1/h2][br] as the [br] is not required
        $this->_bbcode = preg_replace("/\[(\/)?(tr|td|table|cell|row|h[1-6])\]\s*\[br\]/i", "[\\1\\2]", $this->_bbcode);

        //Remove any occurances of [br][h3] as the [br] is not required
        $this->_bbcode = preg_replace("/\[br\]\s*\[(\/)?(tr|td|cell|row|h[1-6])\]/i", "[\\1\\2]", $this->_bbcode);

        //Remove any occurances of [br][class=foo] as the [br] is not required
        $this->_bbcode = preg_replace("/\[br\]\s*\[(\/)?(class=[a-z0-9-_]+)\]/i", "[\\1\\2]", $this->_bbcode);

        //Remove any occurances of [/class][br] as the [br] is not required
        $this->_bbcode = preg_replace("/\[(\/)?(class)\]\s*\[br\]/i", "[\\1\\2]", $this->_bbcode);

        //Remove any occurances of [br][/class] as the [br] is not required
        $this->_bbcode = preg_replace("/\[br\]\s*\[(\/)?(class)\]/i", "[\\1\\2]", $this->_bbcode);

        //Remove any occurances of [class=foo][br] as the [br] is not required
        $this->_bbcode = preg_replace("/\[(\/)?(class=[a-z0-9-_]+)\]\s*\[br\]/i", "[\\1\\2]", $this->_bbcode);

        /* Convert BBCode [img]url[/img] to [img src=url] format for easier processing later */
        $this->_bbcode = preg_replace("/\[img\](.*?)\[\/img\]/i", '[img src=\\1]', $this->_bbcode);
        $this->_bbcode = preg_replace("/\[img=(.*?)\](.*?)\[\/img\]/i", '[img src=\\2 alt=\\1]', $this->_bbcode);
        /* Convert BBCode [mag]url,url,url[/mag] to [mag src=url] format for easier processing later */
        $this->_bbcode = preg_replace("/\[mag\](.*?)\[\/mag\]/i", '[mag width = ' . $this->magwidth . ' src=\\1]', $this->_bbcode);

        /* Convert BBCode [mag]url,url,url[/mag] to [mag src=url] format for easier processing later */
        $this->_bbcode = preg_replace("/\[mag=([0-9]*?)\](.*?)\[\/mag\]/i", '[mag width=\\1 src=\\2]', $this->_bbcode);

        /* Convert BBCode [gallery]url,url,url[/gallery] to [gallery src=url] format for easier processing later */
        $this->_bbcode = preg_replace("/\[gallery(.*?)\](.*?)\[\/gallery\]/i", '[gallery src=\\2]', $this->_bbcode);

        /* Convert BBCode [thumb=100]url[/thumb] to [thumb src=url size=100] format for easier processing later */
        $this->_bbcode = preg_replace("/\[thumb\](.*?)\[\/thumb\]/i", '[thumb size=100 src=\\1]', $this->_bbcode);
        $this->_bbcode = preg_replace("/\[thumb=(.*?)\](.*?)\[\/thumb\]/i", '[thumb size=\\1 src=\\2]', $this->_bbcode);

        /* Convert BBCode [email]address[/email] to [email=address]address[/email] format for easier processing later */
        //$this->_bbcode = preg_replace("/\[email(.*?)\](.*?)\[\/email\]/i", '[email=\\2]\\2[/email]', $this->_bbcode);
        //$this->_bbcode = preg_replace_callback("/\[email\](.*?)\[\/email\]/i", create_function ('$matches', 'return "[email=" . $matches[1]."][javascript]document.write(" . bbConverter::obfuscateEmail($matches[1], false) . ");[/javascript][/email][noscript][url=contact/]Email[/url][/noscript]";'), $this->_bbcode);
        $this->_bbcode = preg_replace_callback("/\[email\](.*?)\[\/email\]/i", create_function('$matches', '$e = mt_rand(1000, 9999); return "[email=" . $matches[1]."][span id=e-" . $e . "][/span][javascript]document.getElementById(\'e-" . $e . "\').innerHTML = " . bbConverter::obfuscateEmail($matches[1], false) . ";[/javascript][/email][noscript][url=contact/]Email[/url][/noscript]";'), $this->_bbcode);

        /* Convert BBCode [url]address[/url] to [url=address]address[/url] format for easier processing later */
        //if ($this->truncateurl == 0) {
        $this->_bbcode = preg_replace("/\[url( lightbox)?\](.*?)\[\/url\]/i", '[url=\\2\\1]\\2[/url]', $this->_bbcode);
        $this->_bbcode = preg_replace("/\[url( nofollow)?\](.*?)\[\/url\]/i", '[url=\\2\\1]\\2[/url]', $this->_bbcode);
        $this->_bbcode = preg_replace("/\[url( new)?\](.*?)\[\/url\]/i", '[url=\\2\\1]\\2[/url]', $this->_bbcode);
        $this->_bbcode = preg_replace("/\[url( nofollow new)?\](.*?)\[\/url\]/i", '[url=\\2\\1]\\2[/url]', $this->_bbcode);
        $this->_bbcode = preg_replace("/\[url( new nofollow)?\](.*?)\[\/url\]/i", '[url=\\2\\1]\\2[/url]', $this->_bbcode);
        //} else {
        //    $this->_bbcode = preg_replace_callback("/\[url\](.*?)\[\/url\]/i", '_callback_url', $this->_bbcode);
        //}
        /*
        $smilieimages = $this->_recursiveScandir($this->smileyfolder . '/');

        $smiliefind = array();
        $smiliefind[] = 'smile';
        $smiliefind[] = 'sad';
        $smiliefind[] = 'biggrin';
        $smiliefind[] = 'angry';
        $smiliefind[] = 'evil';
        //$smiliefind[] = 'cool';
        $smiliefind[] = 'cry';
        $smiliefind[] = 'thinking';
        $smiliefind[] = 'wink';

        $smiliereplace = array();
        $smiliereplace[] = ':)';
        $smiliereplace[] = ':(';
        $smiliereplace[] = ':D';
        $smiliereplace[] = ':(';
        $smiliereplace[] = '>:(';
        //$smiliereplace[] = '8)'; //This was causing grief with real data
        $smiliereplace[] = ';(';
        $smiliereplace[] = ':S';
        $smiliereplace[] = ';)';

        $smilies = array();
        */
        /* Build 2D array of smilies - name, file, code */
        /*
        //code commented out because it's broken and generates an error
        if (is_array($smilieimages)) {
            foreach ($smilieimages as $k => $v) {
                if (getFileExtension($v) == 'gif') {
                    $smiliefile = 'images/cms/smilies/' . $v;
                    $smiliename = str_replace('.gif', '', $v);

                    $smiliecode = str_replace($smiliefind, $smiliereplace, $smiliename);
                    if ($smiliename == $smiliecode) {
                        $smiliecode = ':' . $smiliecode . ':';
                    }

                    $smilies[] = array('name' => $smiliename, 'file' => $smiliefile, 'code' => $smiliecode);

                    $this->_bbcode = str_replace($smiliecode, "[smilie src=$smiliefile]", $this->_bbcode);
                }
            }
        }
        */
        /* Debug code to test BBCode intermediate output */
        //echo $this->_bbcode;
        //exit();

        $matches = array();
        //clear this variable
        $this->_dom = array();
        //clear this variable
        $this->_bbdom = array();

        /* Patterns for matching tags and plain content */
        //match [tag]
        $pattern1 = "/\[(.*?)\]/is";
        //match content up to the next tag
        $pattern2 = "/[^\[]*/is";

        /* used for managing the loop */
        $startstr = '1';
        $endstr = '2';

        $j = 0;
        $k = 0;

        /* repeat until no more matches are found within the document */
        while ($startstr != $endstr) {
            //used for managing the loop
            $startstr = $this->_bbcode;

            /* look for text content between tags */
            $matches = array();
            preg_match($pattern2, $this->_bbcode, $matches);
            $this->_bbcode = preg_replace($pattern2, "", $this->_bbcode, 1);
            if (trim($matches[0]) != '') {
                //ignore any whitespace only entries
                //TEMP!!
                $matches[0] = trim($matches[0], "\r\n");
                $this->_bbdom[$j] = array();
                //source is the content
                $this->_bbdom[$j]['source'] = $matches[0];
                //These are always of type "content"
                $this->_bbdom[$j]['element'] = 'content';
                //The bbcode is simply the content
                $this->_bbdom[$j]['bbcode'] = $matches[0];

                $this->_dom[$k] = array();
                $this->_dom[$k]['source'] = $this->_bbdom[$j]['source'];
                $this->_dom[$k]['element'] = $this->_bbdom[$j]['element'];
                $this->_dom[$k]['bbcode'] = $this->_bbdom[$j]['bbcode'];

                $j++;
                $k++;
            }

            /* look for HTML tags */
            $matches = array();
            preg_match($pattern1, $this->_bbcode, $matches);
            $this->_bbcode = preg_replace($pattern1, "", $this->_bbcode, 1);
            $this->_bbdom[$j] = array();
            $this->_bbdom[$j]['bbcode'] = isset($matches[0]) ? $matches[0] : '';
            $this->_bbdom[$j]['source'] = $this->_bbTag2HtmlTag($this->_bbdom[$j]['bbcode']);
            $this->_bbdom[$j]['element'] = $this->_getElementType($this->_bbdom[$j]['source']);

            if (isset($this->_dom[$j]) && is_null($this->_dom[$j]['bbcode']) || (isset($this->_bbdom[$j]) && $this->_bbdom[$j]['bbcode'] != '')) {
                $this->_dom[$k] = array();
                $this->_dom[$k]['bbcode'] = isset($matches[0]) ? $matches[0] : '';
                $this->_dom[$k]['source'] = $this->_bbdom[$j]['bbcode'];
                $this->_dom[$k]['element'] = $this->_bbdom[$j]['element'];

                $k++;
            }

            $j++;

            //used for managing the loop
            $endstr = $this->_bbcode;
        }
    }

    function convert($type = 'bbcode')
    {
        if ($type == 'html') {
            $newline = "\n";
            //$padding = '    '; //temporarily removed to resolve issue with too much padding on lists.
            $padding = '';
            $dom = $this->_dom;
        } elseif ($type == 'htmlentities') {
            $newline = "<br />\n";
            $dom = $this->_dom;
            $padding = '&nbsp;&nbsp;&nbsp;&nbsp;';
        } elseif ($type == 'bbcode') {
            $newline = "\n";
            $padding = '';
            $dom = $this->_bbdom;
        } elseif ($type == 'bbcodeentities') {
            $newline = "<br />\n";
            $padding = '&nbsp;&nbsp;&nbsp;&nbsp;';
            $dom = $this->_bbdom;
        } elseif ($type == 'bbcode2html') {
            $newline = "\n";
            //$padding = '    '; //temporarily removed to resolve issue with too much padding on lists.
            $padding = '';
            $dom = $this->_bbdom;
        }

        $indentlevel = 0;
        $output = '';

        $n = count($dom);
        for ($i = 0; $i < $n; $i++) {
            $behaviour = array();
            $element = $dom[$i]['element'];

            if ($type == 'html') {
                $display = $dom[$i]['source'];
            } elseif ($type == 'htmlentities') {
                $display = htmlentities($dom[$i]['source'], ENT_COMPAT, 'UTF-8');
            } elseif ($type == 'bbcode') {
                $display = $dom[$i]['bbcode'];
            } elseif ($type == 'bbcodeentities') {
                $display = $dom[$i]['bbcode'];
            } elseif ($type == 'bbcode2html') {
                $display = $dom[$i]['source'];
            }
            //echo $dom[$i]['element']."<br />";
            $thisbehaviour = $this->_getElementBehaviour($dom[$i]['element']);
            $lastbehaviour = isset($dom[$i-1]) ? $this->_getElementBehaviour($dom[$i-1]['element']) : array('startbreak'=>'','endbreak'=>'');
            $behaviour['startbreak'] = ($thisbehaviour['startbreak'] || $lastbehaviour['endbreak']) ? true : false;
            //$behaviour['endbreak'] = ($thisbehaviour['endbreak'] || $lastbehaviour['startbreak']) ? true : false;
            $behaviour['endbreak'] = ($thisbehaviour['endbreak']) ? true : false;

            //echo $display = $dom[$i]['element'].' - '. $behaviour['startbreak'].'.' . $behaviour['endbreak'].'<br>';

            if ($dom[$i]['element'] == 'break') {
                //Breaks are a special case - in bbcode, they are represented by a simple newline
                //for html2bb
                //$output .= '';
                //for bb2html
                //$output .= $lastbehaviour['endbreak'] ? '' : $display;
                if ($dom[$i - 1]['element'] == 'break') {
                    //when there are 2 breaks in a row
                    $output .= $display;
                } else {
                    $output .= $lastbehaviour['endbreak'] ? '' : $display;
                }
            } elseif ($display == "\n") {
                //don't bother with padding for newlines
                $output .= $display;
            } elseif (strpos($element, 'open') !== false) {
                //opening tag
                if ($behaviour['startbreak']) {
                    $output .= $newline;
                    for ($j = 0; $j < $indentlevel; $j++) {
                        $output .= $padding;
                    }
                }
                $output .= $display;

                $indentlevel = $indentlevel + 1;
            } elseif (strpos($element, 'close') !== false) {
                //closing tag
                $indentlevel = $indentlevel - 1;
                if ($behaviour['startbreak']) {
                    $output .= $newline;
                    for ($j = 0; $j < $indentlevel; $j++) {
                        $output .= $padding;
                    }
                }
                $output .= $display;
            } else {
                if ($behaviour['startbreak']) {
                    $output .= $newline;
                    for ($j = 0; $j < $indentlevel; $j++) {
                        $output .= $padding;
                    }
                }
                $output .= $display;
            }
        }

        return $output;
    }

    /* ************* _bbTag2HtmlTag ************
     Converts BBCode code to HTML. The output of this function will depend on config vars above.
     This function works on a tag by tag basis, it will not convert a whole document
     */
    function _bbTag2HtmlTag($source)
    {
        $imgprefix = $this->imagedropshadow ? '<div class="img-shadow">' : '';
        $imgsuffix = $this->imagedropshadow ? '</div><br style="clear:both;" />' : '';
        $imageclasses = count($this->imageclass) > 0 ? ' class="' . implode($this->imageclass, ' ') . '"' : '';

        if (false) {
            //br
        } elseif (preg_match("/\[br\]/i", $source)) {
            return "<br />";

            //copyright
        } elseif (preg_match("/\[copyright\]/i", $source)) {
            return "&copy;";

            //bold
        } elseif (preg_match("/\[b\]/i", $source, $matches)) {
            return '<strong>';
        } elseif (preg_match("/\[\/b\]/i", $source, $matches)) {
            return '</strong>';

            //italic
        } elseif (preg_match("/\[i\]/i", $source, $matches)) {
            return '<em>';
        } elseif (preg_match("/\[\/i\]/i", $source, $matches)) {
            return '</em>';

            //underline
        } elseif (preg_match("/\[u\]/i", $source, $matches)) {
            return '<u>';
        } elseif (preg_match("/\[\/u\]/i", $source, $matches)) {
            return '</u>';

            //hr
        } elseif (preg_match("/\[hr\]/i", $source)) {
            return '<hr />';

            //clear
        } elseif (preg_match("/\[clear\]/i", $source)) {
            return '<div style="clear: both;"></div>';

            //degrees
        } elseif (preg_match("/\[degrees\]/i", $source)) {
            return '&deg;';

            //info
        } elseif (preg_match("/\[info\]/i", $source, $matches)) {
            return '<div class="info">';
        } elseif (preg_match("/\[\/info\]/i", $source, $matches)) {
            return '</div>';

            //author
        } elseif (preg_match("/\[author\]/i", $source, $matches)) {
            return '<div class="author">';
        } elseif (preg_match("/\[\/author\]/i", $source, $matches)) {
            return '</div>';

            //class
        } elseif (preg_match("/\[class=?([^ ]*?)(.*?)\]/i", $source, $matches)) {
            return '<div class="' . strtolower($matches[2]) . '">';
        } elseif (preg_match("/\[\/class\]/i", $source, $matches)) {
            return '</div>';

            //span id=foo
        } elseif (preg_match("/\[span id=?([^ ]*?)(.*?)\]/i", $source, $matches)) {
            return '<span id="' . strtolower($matches[2]) . '">';
        } elseif (preg_match("/\[\/span\]/i", $source, $matches)) {
            return '</span>';

            //basic tables
        } elseif (preg_match("/\[table\]/i", $source, $matches)) {
            return '<table>';
        } elseif (preg_match("/\[\/table\]/i", $source, $matches)) {
            return '</table>';

            //basic table rows
        } elseif (preg_match("/\[row\]/i", $source, $matches)) {
            return '<tr>';
        } elseif (preg_match("/\[\/row\]/i", $source, $matches)) {
            return '</tr>';
        } elseif (preg_match("/\[tr\]/i", $source, $matches)) {
            return '<tr>';
        } elseif (preg_match("/\[\/tr\]/i", $source, $matches)) {
            return '</tr>';

            //basic table cells
        } elseif (preg_match("/\[cell\]/i", $source, $matches)) {
            //return '<td valign="top">';
            return '<td>';
        } elseif (preg_match("/\[\/cell\]/i", $source, $matches)) {
            return '</td>';
        } elseif (preg_match("/\[td\]/i", $source, $matches)) {
            //return '<td valign="top">';
            return '<td>';
        } elseif (preg_match("/\[\/td\]/i", $source, $matches)) {
            return '</td>';

            //quote
        } elseif (preg_match("/\[quote\]/i", $source, $matches)) {
            return '<div class="quote">';
        } elseif (preg_match("/\[\/quote\]/i", $source, $matches)) {
            return '</div>';
            //} else if (preg_match("/\[quote=\"?(.*?)\"?\]/i", $source, $matches)) {
            //    return '<div class="quote">' . $matches[1].' said...<br />';//<strong>' . $matches[1].' said...</strong><br />'; //<b>' . $matches[2].' said:</b><br />

            //popup
        } elseif (preg_match("/\[popup=?([^ ]*?)(.*?)\]/i", $source, $matches)) {
            return '<div class="popup" id="' . strtolower($matches[2]) . '">';
        } elseif (preg_match("/\[\/popup\]/i", $source, $matches)) {
            return '</div>';

            //align
        } elseif (preg_match("/\[align=?([^ ]*?)(.*?)\]/i", $source, $matches)) {
            return '<div align="' . strtolower($matches[2]) . '">';
        } elseif (preg_match("/\[\/align\]/i", $source, $matches)) {
            return '</div>';

            //center / centre
        } elseif (preg_match("/\[(center|centre)\]/i", $source, $matches)) {
            return '<div align="center">';
        } elseif (preg_match("/\[\/(center|centre)\]/i", $source, $matches)) {
            return '</div>';

            //code
        } elseif (preg_match("/\[code\]/i", $source, $matches)) {
            return '<span class="code">';
        } elseif (preg_match("/\[\/code\]/i", $source, $matches)) {
            return '</span>';

            //codeblock
        } elseif (preg_match("/\[codeblock\]/i", $source, $matches)) {
            return '<div class="codeblock">';
        } elseif (preg_match("/\[\/codeblock\]/i", $source, $matches)) {
            return '</div>';

            //left
        } elseif (preg_match("/\[left\]/i", $source, $matches)) {
            return '<div align="left">';
        } elseif (preg_match("/\[\/left\]/i", $source, $matches)) {
            return '</div>';

            //right
        } elseif (preg_match("/\[right\]/i", $source, $matches)) {
            return '<div align="right">';
        } elseif (preg_match("/\[\/right\]/i", $source, $matches)) {
            return '</div>';

            //headings - h1 h2 h3 etc
        } elseif (preg_match("/\[(h[0-9]+?)\]/i", $source, $matches)) {
            return '<' . strtolower($matches[1]) . '>';
        } elseif (preg_match("/\[\/(h[0-9]+?)\]/i", $source, $matches)) {
            return '</' . strtolower($matches[1]) . '>';

            //javascript
        } elseif (preg_match("/\[javascript\]/i", $source, $matches)) {
            return '<script type="text/javascript" language="javascript">';
        } elseif (preg_match("/\[\/javascript\]/i", $source, $matches)) {
            return '</script>';

            //noscript
        } elseif (preg_match("/\[noscript\]/i", $source, $matches)) {
            return '<noscript>';
        } elseif (preg_match("/\[\/noscript\]/i", $source, $matches)) {
            return '</noscript>';

            //links and email links, and anchor name links [url] [email]
        } elseif (preg_match("/\[email=?([^ ]*?)(.*?)\]/i", $source, $matches)) {
            if ($this->obfuscate) {
                return "<a href=\"" . $this->contactpage . "\" onmouseover=\"this.href=" . bbConverter::obfuscateEmail(strtolower($matches[2])) . ";\">";
            } else {
                //do not obfuscate the email
                return '<a href="mailto:' . strtolower($matches[2]) . '" title="' . strtolower($matches[2]) . '">';
            }
        } elseif (preg_match("/\[url=?(http\:\/\/[^ ]*?)(.*?)( nofollow)?( new)?( nofollow)?\]/i", $source, $matches)) {
            //external link
            $nofollowcode = ($this->nofollow || !empty($matches[3]) || !empty($matches[5])) ? ' rel="nofollow"' : '';
            $newwindowcode = ($this->newwindow || !empty($matches[4])) ? ' target="_BLANK"' : '';
            //return '<a href="http://' . $matches[2] . '" title="'.strtolower($matches[2]) . '"' . $nofollowcode . '>';
            return '<a href="http://' . $matches[2] . '"'.$nofollowcode.$newwindowcode.'>';
        } elseif (preg_match("/\[url=?([^ ]*?)(.*?) nofollow\]/i", $source, $matches)) {
            //internal link
            return '<a href="' . $matches[2] . '" rel="nofollow">';
        } elseif (preg_match("/\[url=?([^ ]*?)(.*?) lightbox\]/i", $source, $matches)) {
            //internal link
            return '<a href="' . $matches[2] . '" rel="lightbox">';
        } elseif (preg_match("/\[url=?([^ ]*?)(.*?) new\]/i", $source, $matches)) {
            //internal link
            return '<a href="' . $matches[2] . '" target="_BLANK">';
        } elseif (preg_match("/\[url=?([^ ]*?)(.*?) nofollow new\]/i", $source, $matches)) {
            //internal link
            return '<a href="' . $matches[2] . '" rel="nofollow" target="_BLANK">';
        } elseif (preg_match("/\[url=?([^ ]*?)(.*?) new nofollow\]/i", $source, $matches)) {
            //internal link
            return '<a href="' . $matches[2] . '" rel="nofollow" target="_BLANK">';
        } elseif (preg_match("/\[url=?([^ ]*?)(.*?)\]/i", $source, $matches)) {
            //internal link
            return '<a href="' . $matches[2] . '">';
        } elseif (preg_match("/\[jsurl=?([^ ]*?)(.*?)\]/i", $source, $matches)) {
            //internal link
            return '<a href="" onclick="javascript:' . $matches[2] . ' return false;" title="More Information...">';
        } elseif (preg_match("/\[\/(url|email|jsurl)\]/i", $source, $matches)) {
            return '</a>';

            //Database links [db=tablename,id]link title[/db]
            /*
             } else if (preg_match("/\[db=?([^ ]*?)(.*?),(.*?)\]/i", $source, $matches)) {
             return '<a href="'.strtolower($matches[2]) . '.php?id = '.strtolower($matches[3]) . '>';
             } else if (preg_match("/\[\/db\]/i", $source, $matches)) {
             return '</a>';
             */

            //Lists <ul> <ol>
        } elseif (preg_match("/\[list=1\]/i", $source, $matches)) {
            $this->_lastlist = 'ol';
            return '<ol>';
        } elseif (preg_match("/\[list\]/i", $source, $matches)) {
            $this->_lastlist = 'ul';
            return '<ul>';
        } elseif (preg_match("/\[\/list\]/i", $source, $matches)) {
            return $this->_lastlist != '' ? '</' . $this->_lastlist . '>' : '</ul>';

            //Lists <li> - no end tag in bbml
        } elseif (preg_match("/\[\*\]/i", $source, $matches)) {
            return '<li>';
        } elseif (preg_match("/\[\/\*\]/i", $source, $matches)) {
            return '</li>';

            //Images <li> - src contained between tags in bbml, not within the initial tag

            //External images with alt tag
        } elseif (preg_match("/\[img(.*?)src=http\:\/\/(.*?) alt=(.*?)\]/i", $source, $matches)) {
            if ($this->imagecache) {
                //return $imgprefix . '<img' . $imageclasses . ' src="images/default/external/' . $matches[2] . '" alt="' . $matches[3] . '" />' . $imgsuffix;
                return $imgprefix . '<img' . $imageclasses . ' src="http://' . $matches[2] . '" alt="' . $matches[3] . '" />' . $imgsuffix;
            } else {
                return $imgprefix . '<img' . $imageclasses . ' src="http://' . $matches[2] . '" alt="' . $matches[3] . '" />' . $imgsuffix;
            }

            //External images
        } elseif (preg_match("/\[img(.*?)src=http\:\/\/(.*?)\]/i", $source, $matches)) {
            if ($this->imagecache) {
                //return $imgprefix . '<img' . $imageclasses . ' src="images/300/external/' . $matches[2] . '" alt="" />' . $imgsuffix;
                return $imgprefix . '<img' . $imageclasses . ' src="http://' . $matches[2] . '" alt="" />' . $imgsuffix;
            } else {
                return $imgprefix . '<img' . $imageclasses . ' src="http://' . $matches[2] . '" alt="" />' . $imgsuffix;
            }

            //Internal images with alt tag
        } elseif (preg_match("/\[img(.*?)src=(.*?) alt=(.*?)\]/i", $source, $matches)) {
            if ($this->imagecache) {
                return $imgprefix . '<img' . $imageclasses . ' src="' . $matches[2] . '" alt="' . $matches[3] . '" />' . $imgsuffix;
            } else {
                return $imgprefix . '<img' . $imageclasses . ' src="' . $matches[2] . '" alt="' . $matches[3] . '" />' . $imgsuffix;
            }

            //Internal images
        } elseif (preg_match("/\[img(.*?)src=(.*?)\]/i", $source, $matches)) {
            if ($this->imagecache) {
                return $imgprefix . '<img' . $imageclasses . ' src="' . $matches[2] . '" alt="" />' . $imgsuffix;
            } else {
                return $imgprefix . '<img' . $imageclasses . ' src="' . $matches[2] . '" alt="" />' . $imgsuffix;
            }

            //Smiley images
        } elseif (preg_match("/\[smilie(.*?)src=(.*?)\]/i", $source, $matches)) {
            return '<img src="' . $matches[2] . '" alt="" />';

            //Smiley images with alt tag
        } elseif (preg_match("/\[smilie(.*?)src=(.*?) alt=(.*?)\]/i", $source, $matches)) {
            return '<img src="' . $matches[2] . '" alt="' . $matches[3] . '" />';

            //Thumbnail images - these are clickable to open the fullsize image in a new window
        } elseif (preg_match("/\[thumb size=(.*?) src=(.*?)\]/i", $source, $matches)) {
            if ($this->imagecache) {
                return $imgprefix . "<a href=\"images/default/" . $matches[2] . "\">" . '<img' . $imageclasses . ' src="images/' . $matches[1] . '/' . $matches[2] . '" alt="" />' . "</a>" . $imgsuffix;
            } else {
                return $imgprefix . "<a href=\"\">" . '<img' . $imageclasses . ' src="' . $matches[3] . '" alt="" />' . "</a>" . $imgsuffix;
            }

            //Magazine Layout images
        } elseif (preg_match("/\[mag width=(.*?) src=(.*?)\]/i", $source, $matches)) {
            require_once(dirname(__FILE__) . '/magazinelayout.class.php');
            $mag = new magazinelayout($matches[1], $this->magpadding, $this->magtemplate);
            $imgarr = explode(', ', $matches[2]);
            foreach ($imgarr as $i) {
                $i = trim($i);
                $mag->addImage(_DOWNLOADDIR . '/' . $i, $i);
            }
            return $mag->getHtml();

            //Gallery Layout images
        } elseif (preg_match("/\[gallery(.*?)src=(.*?)\]/i", $source, $matches)) {
            require_once(dirname(__FILE__) . '/gallerylayout.class.php');
            $gallery = new gallerylayout($this->magwidth, $this->magpadding, $this->magtemplate);
            $imgarr = explode(', ', $matches[2]);
            foreach ($imgarr as $i) {
                $i = trim($i);
                $gallery->addImage(_DOWNLOADDIR . '/' . $i, $i);
            }
            return $gallery->getHtml();
        } else {
            return $source;
        }
    }

    /* ************* _htmlTag2BbTag ************
     Converts HTML code to BBCode. This is not an exact science, and many tags are lost in the process.
     It is best used for converting documents without tables.
     This function works on a tag by tag basis, it will not convert a whole document
     */
    function _htmlTag2BbTag($source)
    {
        //doctype
        if (preg_match("/<\!?doctype(.*?)>/i", $source)) {
            return '';
        }

        //<table> <tr> <td> <thead> <tbody>- these get stripped out at present
        /*
         if (preg_match("/<(table|tr|td|thead|tbody)(.*?)>/i", $source)) {
         return "";
         }
         if (preg_match("/<\/(table|tr|td|thead|tbody)>/i", $source)) {
         return "";
         }
         */

        if (preg_match("/<(thead|tbody)(.*?)>/i", $source)) {
            return '';
        }
        if (preg_match("/<\/(thead|tbody)>/i", $source)) {
            return '';
        }

        if (preg_match("/<(table|tr|td)(.*?)>/i", $source, $matches)) {
            return '[' . strtolower($matches[1]) . ']';
        }
        if (preg_match("/<\/(table|tr|td)>/i", $source, $matches)) {
            return '[/' . strtolower($matches[1]) . ']';
        }

        //br
        if (preg_match("/<br ?\/{0, 1}>/i", $source)) {
            //return "\n";
            //return "[BR]";
            //The newline will be inserted preceding this tag anyway
            return null;
        }

        //comment
        if (preg_match("/<\!--(.*?)\-->/is", $source)) {
            return '';
        }

        //meta
        if (preg_match("/<(meta)(.*?)>/i", $source, $matches)) {
            return '';
        }

        //hr
        if (preg_match("/<hr ?\/{0, 1}>/i", $source)) {
            return '[hr]';
        }

        //div - nothing at the start, newline at the end
        if (preg_match("/<div(.*?)>/i", $source, $matches)) {
            return '';
        }
        if (preg_match("/<\/div>/i", $source, $matches)) {
            return "\n";
        }

        //span / pre - nothing to see here unless span is styled (todo)
        if (preg_match("/<span(.*?)>/i", $source, $matches)) {
            return '';
        }
        if (preg_match("/<\/span>/i", $source, $matches)) {
            return "";
        }

        //Lists <ul> <ol>
        if (preg_match("/<ul(.*?)>/i", $source, $matches)) {
            return '[list]';
        }
        if (preg_match("/<ol(.*?)>/i", $source, $matches)) {
            return '[list=1]';
        }
        if (preg_match("/<\/(ul|ol)>/i", $source, $matches)) {
            return '[/list]';
        }

        //Lists <li> - no end tag in bbml
        if (preg_match("/<li(.*?)>/i", $source, $matches)) {
            return '[*]';
        }
        if (preg_match("/<\/li>/i", $source, $matches)) {
            //a NULL is returned instead of empty so that the line breaks are still inserted.
            return null;
        }

        //headings - h1 h2 h3 etc
        if (preg_match("/<(h[0-9]+?)(.*?)>/i", $source, $matches)) {
            return '[' . strtolower($matches[1]) . ']';
        }
        if (preg_match("/<\/(h[0-9]*?)>/i", $source, $matches)) {
            return '[/' . strtolower($matches[1]) . ']';
        }

        //bold
        if (preg_match("/<(b|strong)(.*?)>/i", $source, $matches)) {
            return '[b]';
        }
        if (preg_match("/<\/(b|strong)>/i", $source, $matches)) {
            return '[/b]';
        }

        //italic
        if (preg_match("/<(i|em)(.*?)>/i", $source, $matches)) {
            return '[i]';
        }
        if (preg_match("/<\/(i|em)>/i", $source, $matches)) {
            return '[/i]';
        }

        //underline
        if (preg_match("/<(u)(.*?)>/i", $source, $matches)) {
            return '[u]';
        }
        if (preg_match("/<\/(u)>/i", $source, $matches)) {
            return '[/u]';
        }

        //paragraph <p> - nothing at the start, newline at the end
        if (preg_match("/<(p)(.*?)>/i", $source, $matches)) {
            return '';
        }
        if (preg_match("/<\/(p)>/i", $source, $matches)) {
            return "\n";
        }

        //links and email links <a>
        //$_lastlink is used to remember the type of the last </a> - it could be email or url or anchor (internal page link)
        if (preg_match("/<a(.*?)href=\"mailto\:(.*?)\"(.*?)>/i", $source, $matches)) {
            $this->_lastlink = 'email';
            return '[email = ' . strtolower($matches[2]) . ']';
        }

        /* Anchors are currently deleted */
        if (preg_match("/<a(.*?)name=\"(.*?)\"(.*?)>/i", $source, $matches)) {
            $this->_lastlink = 'anchor';
            //return '[anchor = '.strtolower($matches[2]) . ']';
            //return '[anchor]'.strtolower($matches[2]) . '';
            return '';
        }

        if (preg_match("/<a(.*?)href=\"(.*?)\"(.*?)>/i", $source, $matches)) {
            $this->_lastlink = 'url';
            return '[url = ' . strtolower($matches[2]) . ']';
        }

        if (preg_match("/<\/a>/i", $source, $matches)) {
            if ($this->_lastlink == 'anchor')
                return '';
            return $this->_lastlink != '' ? '[/' . $this->_lastlink . ']' : '[/url]';
        }

        //Images
        if (preg_match("/<img(.*?)src=\"(.*?)\"(.*?)>/i", $source, $matches)) {
            return '[img]' . strtolower($matches[2]) . '[/img]';
        }

        return htmlentities($source, ENT_COMPAT, 'UTF-8');
    }

    /**
    Takes an email address user@domain.co.nz
    Splits into 3 parts
    $addressname = user (just the first part of the address)
    $addressdomain1 = niamod (the first part of the domain name reversed)
    $addressdomain2 = co.nz (the remaining part of the domain name, without the first dot)
    Final output looks like this...
    <a href="contact.php" onmouseover="this.href=xyz('co.nz','user','niamod');">
    */
    public static function obfuscateEmail($address,$includemailto=true) {
        $addressparts = explode('@',$address);
        $addressname = $addressparts[0];
        if (!isset($addressparts[1])) {
            /* Not a correctly formatted email address */
            return $address;
        }
        $addressdomainparts = explode('.',$addressparts[1]);
        $addressdomain1 = $addressdomainparts[0];
        $addressdomain1 = strrev($addressdomain1);
        unset($addressdomainparts[0]);
        $addressdomain2 = implode('.',$addressdomainparts);
        if ($includemailto) {
            return "xyz('$addressdomain2','$addressname','$addressdomain1')";
        } else {
            return "xyz('$addressdomain2','$addressname','$addressdomain1',false)";
        }
    }
}

