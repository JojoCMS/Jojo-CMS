<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_tags
 */

$related = Jojo::getFormData('related', '');

function relatedtagsort($a, $b)
{
    if ($a['frequency'] != $b['frequency']) {
        return ($a['frequency'] < $b['frequency']) ? 1 : -1;
    } elseif ($a['tg_tag'] != $b['tg_tag']) {
        return strcmp($a['tg_tag'], $b['tg_tag']);
    }
    return 0;
}

if (!empty($related)) {
    $tags = Jojo_Plugin_Jojo_Tags::getTagArray(urldecode($related));
    if ($tags && is_array($tags)) {
        usort($tags, "relatedtagsort");
        echo json_encode($tags);
    }
}
exit;