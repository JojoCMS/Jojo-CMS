<?php
/*

 magazinelayout.class.php

 Introduction
 ============
 A class for creating magazine-like layouts for images. A Magazine-like layout arranges the images at
 different sizes so that all images fit within a defined "square box". This can be an attractive way
 of arranging images when you are dealing with user-uploaded images, or don't have a graphic designer
 handy to arrange and resize them in photoshop.

 Purpose
 =======
 The obvious use for this script is anywhere where more than one user submitted image needs to be presented
 in a HTML page. I'm thinking product databases, forum image uploads, random image rotations, etc etc.
 Once you have 10 or so images, you are better off using an AJAX based image gallery, but this script will
 fill the gap nicely up till that point.

 Layouts
 =======
 The layouts that are used depend on the number of landscape and portrait images. For example, if we are
 given a portrait and 2 landscapes, the layout will appear as follows (different numbers represent different
 images)...

 11113333
 11113333
 22223333
 22223333

 With 3 landscapes, the layout may appear as such...

 11112222
 11112222
 33333333
 33333333

 With 2 portraits, 1 landscape we use

 11122222222333  or  111222
 11122222222333      111222
 11122222222333      333333
                     333333

 With 3 portraits, we could use either

 111222333  or   11333
 111222333       11333
 111222333       22333
                 22333

 If you have 4 images to display, this class will use any of the following...

 111222   112233   111444
 111222   112233   222444
 333444   444444   333444
 333444   444444

 Logic
 =====
 The logic behind these calculations are based on algebra - yes, x + y = z (but a little more complicated).
 I have attempted to clearly document all calculations, however you will find the tools at http://www.quickmath.com/
 very useful if you aren't a mathematics expert (I'm certainly not one).

 Requirements
 ============
 -A PHP 4.3.x server with GD2.x extension enabled - most PHP shared hosting is suitable
 -An image resizing script - I have included a very simple one with this bundle

 Usage
 =====

 //include the class file
 require_once('magazinelayout.class.php');

 //Define the width for the output area (pixels)
 $width = 600;

 //Define padding around each image - this *must* be included in your stylesheet (pixels)
 $padding = 3;

 //Define your template for outputting images
 $template = "<img src=\"image.php?size=[size]&amp;file=[image]\" alt=\"\" />"; //Don't forget to escape the &

 //create a new instance of the class
 $mag = new magazinelayout($width, $padding, $template);

 //Add the images in any order
 $mag->addImage('landscape1.jpg');
 $mag->addImage('portrait1.jpg');
 $mag->addImage('landscape2.jpg');

 //display the output
 echo $mag->getHtml();

 Template
 ========
 A different <img> tag will be required for different installations, depending mainly on the image script used.
 Variables:
 [size] = The size of the image, either 500, h500 or w500
 [file] = The filename of the image, eg images/image1.jpg

 The default is...
 <img src="image.php?size=[size]&file=[image]" alt="" />

 Using Apache's mod_rewrite, a better format might be...
 <image src="images/[size]/[file]" alt="" />
 Note your script and server must be configured for this, details of this are out of the scope of this script

 A static looking image URL is better because Google will usually ignore dynamic looking images (they look
 like PHP scripts, not images)


 CSS
 ===
 The following CSS is required for padding to work correctly...

 .magazine-image {
    background: #fff;
    border: 1px #eee solid;
 }
 .magazine-image img {
    padding: 0px;
    background: #fff;
    margin: 2px;
    border: 1px #eee solid;
 }

 Padding
 =======
 Including padding between images was the most complicated part of this script. On the more complex layouts,
 the equations double in complexity once padding is added. Padding is implemented as x pixels gap between the
 images - x is defined when the class is created.
 The padding you specify *must* be reflected in the stylesheet you use, or the layout will not look right.
 Because IE deals with padding incorrectly, padding has been implemented as "margin" instead. If a padding of
 3 is specified in the PHP class, then the CSS class for " . magazine-image img" should reflect a margin of 3px
 or a margin o 2px + border of 1px. Do not specify padding on the image unless you are prepared to hack the PHP
 code.

 Rounding
 ========
 Almost all of the calculations are done using floating point numbers. Because HTML required whole numbers, the
 numbers need to be rounded (down) before outputting. On some examples, this makes no difference. On others, the
 1 or 2 pixels worth of rounding is noticeable. Would welcome any suggestions on this issue for those who want
 pixel perfect layouts.

 Restrictions
 ============
 -There is a current limit of 8 images. This can easily be extended.
 -Images must be a reasonable quality. Images that are too small are stretched which doesn't look good.
 -The included image resizing script is very basic. I recommend using a script which caches output. The
  image resizing script that is right for you will depend on your server configuration and is out of
  the scope of this class.

 To Do
 =====
 There are several obvious improvements that can be made to this script. These include...
 -Adding code for more than 8 images
 -Configuring so that low-res images are always shown in the small spots
 -Allow positioning of images (though this does defeat the purpose)
 -Include an all-purpose image resizing script with static looking URLs and Image caching
 -Full testing for version 1 release
 -Rounding issue explained above

 Copyright
 =========
 This file may be used and distributed subject to the LGPL available from http://www.fsf.org/copyleft/lgpl.html
 If you find this script useful, I would appreciate a link back to http://www.ragepank.com or simply an email
 to let me know you like it :)
 You are free (and encouraged) to modify or enhance this class - if you do so, I would appreciate a copy
 of the changes if it's not too much trouble. Please keep this copyright statement intact, and give fair
 credit on any derivative work.

 About the Author
 ================
 Harvey Kane is a PHP Web developer living and working in Auckland New Zealand. He is interested in developing
 "best practice" websites, and especially interested in using PHP to automate this process as much as
 possible. Harvey works as a freelance developer doing CMS websites and SEO under the umbrella of www.harveykane.com,
 and publishes SEO Articles and tools at www.ragepank.com

 Ragepank.com
 ============
 www.ragepank.com is a source of original SEO Articles and tools - PHP based techniques for improving
 search engine positions, and good practice for web development.

 Support
 =======
 I am happy to help with support and installation, so long as all documentation and forum threads are read
 first. You can contact me at info@ragepank.com

 Thanks
 ======
 Thanks to Alexander Burkhardt (www.alex3d.de) for the use of the demo images. The images were taken on
 the lovely Hokianga Harbour in Northland New Zealand.

 @version 0.9
 @copyright 2006 Harvey Kane
 @author Harvey Kane info@ragepank.com

 */


class magazinelayout
{
    var $images = array();
    var $_numimages = 0;
    var $_fullwidth;
    var $_imagetemplate = "<img src=\"image.php?size=[size]&amp;file=[image]\" alt=\"\" />";
    var $_padding = 3;

    function magazinelayout($maxwidth=600,$padding=3,$imagetemplate = '')
    {
        $this->_fullwidth = $maxwidth;
        $this->_padding = $padding;
        if ($imagetemplate != '') $this->_imagetemplate = $imagetemplate;
    }



    /* Gets the file extension for a given filename */
    function _getFileExt($file)
    {
        $ext = explode(".", $file);
        if (count($ext) == 0) return '';
        return $ext[count($ext)-1];
    }



    /* Converts the format of a 2D array from $arr[a][b] to $arr[b][a] - used for sorting the array*/
    function _transpose($arr)
    {
        if (!is_array($arr)) return $arr;
        foreach($arr as $keyx => $valx) {
            foreach($valx as $keyy => $valy) {
                $newarr[$keyy][$keyx] = $valy;
            }
        }
        return $newarr;
    }



    function addImage($filename, $url = '')
    {
        if ($url == '') $url = $filename;
        /* Ensure the file is an image */
        if (
            file_exists($filename) &&
            (strtolower($this->_getFileExt($filename)) != "jpg") &&
            (strtolower($this->_getFileExt($filename)) != "jpeg") &&
            (strtolower($this->_getFileExt($filename)) != "gif") &&
            (strtolower($this->_getFileExt($filename)) != "png")
             ) {
            return false;
        }

        /* Read the dimensions of the image */
        if (!file_exists($filename)) return false;
        $imagesize = getimagesize($filename);
        $w = $imagesize[0];
        $h = $imagesize[1];

        /* don't include zero sized images */
        if (($h == 0) || ($w == 0)) return false;

        /* Find the ration of width:height */
        $ratio = $w / $h;

        /* Set format based on the dimensions */
        $format = ($w > $h) ? 'landscape' : 'portrait';

        /* Keep a count on the total number of images */
        $this->_numimages++;


        /* Save all image details to an array */
        $i = $this->_numimages - 1;
        $this->images[$i] = array();
        $this->images[$i]['filename'] = $filename;
        $this->images[$i]['url'] = $url;
        $this->images[$i]['format'] = $format;
        $this->images[$i]['ratio'] = $ratio;
        $this->images[$i]['w'] = $w; //Not currently used
        $this->images[$i]['h'] = $h; //Not currently used

        return true;
    }


    /* Replaces variables into the supplied image template */
    function insertImage($size, $name)
    {
        return str_replace('[image]',$name,str_replace('[size]',$size, $this->_imagetemplate));
    }


    /*
    IMAGE LAYOUTS
    =============
    These layouts are coded based on the number of images.
    Some fairly heavy mathematics is used to calculate the image sizes and the excellent calculators at
    http://www.quickmath.com/ were very useful.
    Each of these layouts outputs a small piece of HTML code with the images and a containing div
    around each.
    */


    function get1a($i1)
    {
        /*
        111 or 1
               1
        */

        $s = floor($this->_fullwidth - ($this->_padding * 2));
        $html = '';
        $html .= "<div style=\"float: left; clear: both;\">" . $this->insertImage('' . $s, $this->images[$i1]['url']) . "</div>\n";
        return $html;
    }

    function get2a($i1,$i2)
    {
        /*
        1122

        Equation: t = 4p + ha + hb Variable: h

        */

        $a = $this->images[$i1]['ratio'];
        $b = $this->images[$i2]['ratio'];
        $t = $this->_fullwidth;
        $p = $this->_padding;

        $h1 = floor( (4*$p - $t) / (-$a - $b) );

        $html = '';
        $html .= "<div style=\"float: left; clear: both;\">" . $this->insertImage('h' . $h1,$this->images[$i1]['url']) . "</div>\n";
        $html .= "<div style=\"float: left;\">" . $this->insertImage('h' . $h1,$this->images[$i2]['url']) . "</div>\n";
        return $html;
    }




    function get3a($i1,$i2,$i3)
    {
        /*
        1223
        */

        /* To save space in the equation */
        $a = $this->images[$i3]['ratio'];
        $b = $this->images[$i1]['ratio'];
        $c = $this->images[$i2]['ratio'];
        $t = $this->_fullwidth;
        $p = $this->_padding;

        /*
        Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
        EQUATIONS
        t = 6p + ah + bh + ch
        VARIABLES
        h
        */

        $h1 = floor(
        (6 * $p - $t)
        /
        (-$a -$b -$c)
        );

        $html = '';
        $html .= "<div style=\"float: left; clear: both;\">" . $this->insertImage('h' . $h1,$this->images[$i1]['url']) . "</div>\n";
        $html .= "<div style=\"float: left;\">" . $this->insertImage('h' . $h1,$this->images[$i3]['url']) . "</div>\n";
        $html .= "<div style=\"float: left;\">" . $this->insertImage('h' . $h1,$this->images[$i2]['url']) . "</div>\n";
        return $html;
    }


    function get3b($i1,$i2,$i3)
    {
        /*
        1133
        2233
        */

        /* To save space in the equation */
        $a = $this->images[$i3]['ratio'];
        $b = $this->images[$i1]['ratio'];
        $c = $this->images[$i2]['ratio'];
        $t = $this->_fullwidth;
        $p = $this->_padding;

        /*
        Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
        EQUATIONS
        x/a = w/b + w/c + 2p
        w+x+4p = t
        VARIABLES
        w
        x
        */

        /* width of left column with 2 small images */
        $w1 = floor(
        -(
        (2 * $a * $b * $c * $p + 4 * $b * $c * $p - $b * $c * $t)
        /
        ($a * $b + $c * $b + $a * $c)
        )
        );

        /* width of right column with 1 large image */
        $w2 = floor(
        ($a * (-4 * $b * $p + 2 * $b * $c * $p - 4 * $c * $p + $b * $t + $c * $t))
        /
        ($a * $b + $c * $b + $a * $c)
        );

        $html = '';
        $html .= "<div style=\"float: right; clear: both;\">" . $this->insertImage('w' . $w2,$this->images[$i3]['url']) . "</div>\n";
        $html .= "<div style=\"float: left;\">" . $this->insertImage('w' . $w1,$this->images[$i1]['url']) . "</div>\n";
        $html .= "<div style=\"float: left;\">" . $this->insertImage('w' . $w1,$this->images[$i2]['url']) . "</div>\n";

        return $html;
    }



    function get4a($i1,$i2,$i3,$i4)
    {
        /*
        1234
        */

        /* To save space in the equation */
        $a = $this->images[$i1]['ratio'];
        $b = $this->images[$i2]['ratio'];
        $c = $this->images[$i3]['ratio'];
        $d = $this->images[$i4]['ratio'];
        $t = $this->_fullwidth;
        $p = $this->_padding;

        /*
        Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
        EQUATIONS
        t = 6p + ah + bh + ch + dh
        VARIABLES
        h
        */

        $h1 = floor(
        (8 * $p - $t)
        /
        (-$a -$b -$c -$d)
        );

        //$h1 = floor($this->_fullwidth / ($this->images[$p1]['ratio'] + $this->images[$p2]['ratio'] + $this->images[$p3]['ratio'] + $this->images[$p4]['ratio']));
        $html = '';
        $html .= "<div style=\"float: left; clear: both;\">" . $this->insertImage('h' . $h1,$this->images[$i1]['url']) . "</div>\n";
        $html .= "<div style=\"float: left;\">" . $this->insertImage('h' . $h1,$this->images[$i2]['url']) . "</div>\n";
        $html .= "<div style=\"float: left;\">" . $this->insertImage('h' . $h1,$this->images[$i3]['url']) . "</div>\n";
        $html .= "<div style=\"float: left;\">" . $this->insertImage('h' . $h1,$this->images[$i4]['url']) . "</div>\n";

        return $html;
    }



    function get4b($i1,$i2,$i3,$i4)
    {
        /*
        11444
        22444
        33444
        */

        /* To save space in the equation */
        $a = $this->images[$i4]['ratio'];
        $b = $this->images[$i1]['ratio'];
        $c = $this->images[$i2]['ratio'];
        $d = $this->images[$i3]['ratio'];
        $t = $this->_fullwidth;
        $p = $this->_padding;

        /*
        Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
        EQUATIONS
        x/a = w/b + w/c + 2p
        w+x+4p = t
        VARIABLES
        w
        x
        */

        /* width of left column with 2 small images */
        $w1 = floor(
        -(
        (4 * $a * $b * $c * $d * $p + 4 * $b * $c * $d * $p - $b * $c * $d * $t)
        /
        ($a * $b * $c + $a * $d * $c + $b * $d * $c + $a * $b * $d)
        )
        );

        /* width of right column with 1 large image */
        $w2 = floor(
        -(
        (-4 * $p - (-(1/$c) -(1/$d) -(1/$b)) * (4 * $p - $t) )
        /
        ( (1/$b) + (1/$c) + (1/$d) + (1/$a) )
        )
        );

        $html = '';
        $html .= "<div style=\"float: right; clear: both;\">" . $this->insertImage('w' . $w2,$this->images[$i4]['url']) . "</div>\n";
        $html .= "<div style=\"float: left;\">" . $this->insertImage('w' . $w1,$this->images[$i1]['url']) . "</div>\n";
        $html .= "<div style=\"float: left;\">" . $this->insertImage('w' . $w1,$this->images[$i2]['url']) . "</div>\n";
        $html .= "<div style=\"float: left;\">" . $this->insertImage('w' . $w1,$this->images[$i3]['url']) . "</div>\n";

        return $html;
    }


    function getHtml()
    {

        /* Sort the images array landscape first, then portrait */
        $this->images = $this->_transpose($this->images);
        if (is_array($this->images['format'])) array_multisort($this->images['format'], SORT_STRING, SORT_ASC, $this->images['url'], $this->images['ratio']);
        $this->images = $this->_transpose($this->images);

        /* Profile explains the makeup of the images (landscape vs portrait) so we can use the best layout eg. LPPP or LLLP */
        $profile = '';
        if (is_array($this->images)) {
          foreach ($this->images as $i) {
            $profile .= $i['format'] == 'landscape' ? 'L' : 'P';
          }
        }

        /* Open the containing DIV */
        $html = '';
        $html .= "<div class=\"magazine-image\" style=\"width: " . $this->_fullwidth . "px;\">\n";

        /* 1 Images */
        if ($this->_numimages == 1) {
            $html .= $this->get1a(0);
        }

        /* 2 Images */
        if ($this->_numimages == 2) {
            $html .= $this->get2a(0, 1);
        }

        /* 3 Images */
        if ($this->_numimages == 3) {
            if ($profile == 'LLL') {
              $html .= $this->get3b(0, 1, 2);
                //$html .= $this->get2a(1, 2);
                //$html .= $this->get1a(0);
            } else {
                $html .= $this->get3b(0, 1, 2);
            }
        }

        /* 4 Images */
        if ($this->_numimages == 4) {

            if ($profile == 'LLLP') {
                $html .= $this->get4b(0, 1, 2, 3);
            } elseif ($profile == 'LPPP') {
                $html .= $this->get3a(1, 2, 3);
                $html .= $this->get1a(0);
            } else { // LLLL LLPP PPPP
                $html .= $this->get2a(2, 0);
                $html .= $this->get2a(1, 3);
            }
        }

        /* 5 Images */
        if ($this->_numimages == 5) {
            if ($profile == 'LLLLL') {
                $html .= $this->get3a(0, 1, 2);
                $html .= $this->get2a(3, 4);
            } elseif ($profile == 'LLLLP') {
                $html .= $this->get3b(0, 1, 4);
                $html .= $this->get2a(2, 3);
            } elseif ($profile == 'LLLPP') {
                $html .= $this->get3b(0, 1, 4);
                $html .= $this->get2a(2, 3);
            } elseif ($profile == 'LLPPP') {
                $html .= $this->get3b(2, 3, 4);
                $html .= $this->get2a(0, 1);
            } elseif ($profile == 'LPPPP') {
                $html .= $this->get3b(2, 3, 4);
                $html .= $this->get2a(0, 1);
            } elseif ($profile == 'PPPPP') {
                $html .= $this->get2a(4, 0);
                $html .= $this->get3a(1, 2, 3);
            }
        }

        /* 6 Images */
        if ($this->_numimages == 6) {
            if ($profile == 'LLLLLL') {
                $html .= $this->get2a(0, 1);
                $html .= $this->get2a(2, 3);
                $html .= $this->get2a(4, 5);
            } elseif ($profile == 'LLLLLP') {
                $html .= $this->get4b(0, 1, 2, 5);
                $html .= $this->get2a(3, 4);
            } elseif ($profile == 'LLLLPP') {
                $html .= $this->get3b(0, 1, 4);
                $html .= $this->get3b(2, 3, 5);
            } elseif ($profile == 'LLLPPP') {
                $html .= $this->get3b(0, 1, 5);
                $html .= $this->get3b(2, 3, 4);
            } elseif ($profile == 'LLPPPP') {
                $html .= $this->get3b(0, 2, 4);
                $html .= $this->get3b(1, 3, 5);
            } elseif ($profile == 'LPPPPP') {
                $html .= $this->get3b(0, 1, 5);
                $html .= $this->get3a(2, 3, 4);
            } elseif ($profile == 'PPPPPP') {
                $html .= $this->get3a(3, 4, 5);
                $html .= $this->get3a(0, 1, 2);
            }
        }

        /* 7 Images */
        if ($this->_numimages == 7) {
            if ($profile == 'LLLLLLL') {
                $html .= $this->get3a(0, 1, 2);
                $html .= $this->get2a(3, 4);
                $html .= $this->get2a(5, 6);
            } elseif ($profile == 'LLLLLLP') {
                $html .= $this->get4b(0, 1, 2, 6);
                $html .= $this->get3a(3, 4, 5);
            } elseif ($profile == 'LLLLLPP') {
                $html .= $this->get4b(0, 1, 2, 5);
                $html .= $this->get3b(3, 4, 6);
            } elseif ($profile == 'LLLLPPP') {
                $html .= $this->get3b(0, 1, 5);
                $html .= $this->get4b(2, 3, 4, 6);
            } elseif ($profile == 'LLLPPPP') {
                $html .= $this->get3b(0, 1, 5);
                $html .= $this->get4b(2, 3, 4, 6);
            } elseif ($profile == 'LLPPPPP') {
                $html .= $this->get3a(4, 5, 6);
                $html .= $this->get2a(0, 1);
                $html .= $this->get2a(2, 3);
            } elseif ($profile == 'LPPPPPP') {
                $html .= $this->get3a(0, 1, 2);
                $html .= $this->get4b(3, 4, 5, 6);
            } elseif ($profile == 'PPPPPPP') {
                $html .= $this->get4a(0, 1, 2, 3);
                $html .= $this->get3b(4, 5, 6);
            }
        }

        /* 8 Images */
        if ($this->_numimages == 8) {
            if ($profile == 'LLLLLLLL') {
                $html .= $this->get3a(0, 1, 2);
                $html .= $this->get2a(3, 4);
                $html .= $this->get3a(5, 6, 7);
            } elseif ($profile == 'LLLLLLLP') {
                $html .= $this->get4b(0, 1, 2, 7);
                $html .= $this->get2a(3, 4);
                $html .= $this->get2a(5, 6);
            } elseif ($profile == 'LLLLLLPP') {
                $html .= $this->get4b(0, 1, 2, 6);
                $html .= $this->get4b(3, 4, 5, 7);
            } elseif ($profile == 'LLLLLPPP') {
                $html .= $this->get4b(0, 1, 2, 6);
                $html .= $this->get4b(3, 4, 5, 7);
            } elseif ($profile == 'LLLLPPPP') {
                $html .= $this->get4b(0, 1, 2, 6);
                $html .= $this->get4b(3, 4, 5, 7);
            } elseif ($profile == 'LLLPPPPP') {
                $html .= $this->get3a(4, 5, 6);
                $html .= $this->get2a(0, 1);
                $html .= $this->get3a(2, 3, 7);
            } elseif ($profile == 'LLPPPPPP') {
                $html .= $this->get3b(5, 6, 7);
                $html .= $this->get2a(0, 1);
                $html .= $this->get3b(2, 3, 4);
            } elseif ($profile == 'LPPPPPPP') {
                $html .= $this->get3b(5, 6, 7);
                $html .= $this->get2a(0, 1);
                $html .= $this->get3b(2, 3, 4);
            } elseif ($profile == 'PPPPPPP') {
                $html .= $this->get4a(0, 1, 2, 3);
                $html .= $this->get4a(4, 5, 6, 7);
            } else {
                $html .= $this->get3b(5, 4, 7);
                $html .= $this->get2a(1, 0);
                $html .= $this->get3b(2, 3, 6);
            }
        }

        /* 9 Images */
        if ($this->_numimages == 9) {
            if ($profile == 'LLLLLLLLL') {
                $html .= $this->get3a(0, 1, 2);
                $html .= $this->get3a(3, 4, 8);
                $html .= $this->get3a(5, 6, 7);
            } elseif ($profile == 'LLLLLLLLP') {
                $html .= $this->get4b(0, 1, 2, 7);
                $html .= $this->get2a(3, 4);
                $html .= $this->get3a(5, 6, 8);
            } elseif ($profile == 'LLLLLLLPP') {
                $html .= $this->get4b(0, 1, 2, 6);
                $html .= $this->get2a(3, 4);
                $html .= $this->get3a(5, 7, 8);
            } elseif ($profile == 'LLLLLLPPP') {
                $html .= $this->get4b(0, 1, 2, 6);
                $html .= $this->get2a(3, 4);
                $html .= $this->get3a(5, 7, 8);
            } elseif ($profile == 'LLLLLPPPP') {
                $html .= $this->get4b(0, 1, 2, 6);
                $html .= $this->get2a(3, 4);
                $html .= $this->get3a(5, 7, 8);
            } elseif ($profile == 'LLLLPPPPP') {
                $html .= $this->get3a(4, 5, 6);
                $html .= $this->get3b(0, 1, 8);
                $html .= $this->get3a(2, 3, 7);
            } elseif ($profile == 'LLLPPPPPP') {
                $html .= $this->get3b(5, 6, 7);
                $html .= $this->get3a(0, 1, 8);
                $html .= $this->get3b(2, 3, 4);
            } elseif ($profile == 'LLPPPPPPP') {
                $html .= $this->get3b(5, 6, 7);
                $html .= $this->get3a(0, 1, 8);
                $html .= $this->get3b(2, 3, 4);
            } elseif ($profile == 'LPPPPPPPP') {
                $html .= $this->get4a(0, 1, 2, 3);
                $html .= $this->get2a(4, 5);
                $html .= $this->get3a(6, 7, 8);
            } elseif ($profile == 'PPPPPPPPP') {
                $html .= $this->get4a(0, 1, 2, 3);
                $html .= $this->get2a(4, 5);
                $html .= $this->get3a(6, 7, 8);
            } else {
                $html .= $this->get3b(5, 4, 7);
                $html .= $this->get2a(1, 0);
                $html .= $this->get4b(2, 3, 6, 8);
            }
        }

        /* 10 Images */
        if ($this->_numimages >= 10) {
            /*
            Note this code is applied for 10 or more images - any images over 10 are ignored. Adding support
            for more than 10 images would be easy, but the layouts do start losing their effect as more images
            are added.
            */
            if ($profile == 'LLLLLLLLLL') {
                $html .= $this->get3a(0, 1, 2);
                $html .= $this->get4a(3, 4, 8, 9);
                $html .= $this->get3a(5, 6, 7);
            } elseif ($profile == 'LLLLLLLLLP') {
                $html .= $this->get4b(0, 1, 2, 7);
                $html .= $this->get3a(3, 4, 9);
                $html .= $this->get3a(5, 6, 8);
            } elseif ($profile == 'LLLLLLLLPP') {
                $html .= $this->get4b(0, 1, 2, 6);
                $html .= $this->get3a(3, 4, 9);
                $html .= $this->get3a(5, 7, 8);
            } elseif ($profile == 'LLLLLLLPPP') {
                $html .= $this->get4b(0, 1, 2, 6);
                $html .= $this->get3b(3, 4, 9);
                $html .= $this->get3a(5, 7, 8);
            } elseif ($profile == 'LLLLLLPPPP') {
                $html .= $this->get4b(0, 1, 2, 6);
                $html .= $this->get2a(3, 4);
                $html .= $this->get3a(5, 7, 8);
            } elseif ($profile == 'LLLLLPPPPP') {
                $html .= $this->get3a(4, 5, 6);
                $html .= $this->get4b(0, 1, 8, 9);
                $html .= $this->get3a(2, 3, 7);
            } elseif ($profile == 'LLLLPPPPPP') {
                $html .= $this->get3b(5, 6, 7);
                $html .= $this->get4a(0, 1, 8, 9);
                $html .= $this->get3b(2, 3, 4);
            } elseif ($profile == 'LLLPPPPPPP') {
                $html .= $this->get3b(5, 6, 7);
                $html .= $this->get4a(0, 1, 8, 9);
                $html .= $this->get3b(2, 3, 4);
            } elseif ($profile == 'LLPPPPPPPP') {
                $html .= $this->get4a(0, 1, 2, 3);
                $html .= $this->get3a(4, 5, 9);
                $html .= $this->get3a(6, 7, 8);
            } elseif ($profile == 'LPPPPPPPPP') {
                $html .= $this->get4a(0, 1, 2, 3);
                $html .= $this->get3a(4, 5, 9);
                $html .= $this->get3a(6, 7, 8);
            } elseif ($profile == 'PPPPPPPPPP') {
                $html .= $this->get4a(0, 1, 2, 3);
                $html .= $this->get3a(4, 5, 9);
                $html .= $this->get3a(6, 7, 8);
            } else {
                $html .= $this->get3b(5, 4, 7);
                $html .= $this->get2a(1, 0);
                $html .= $this->get4b(2, 3, 6, 8);
            }
        }

        /* Close the containing DIV */
        $html .= "<div style=\"clear: both;\"></div>\n</div>\n";

        return $html;
    }
}
/* End of Class */
