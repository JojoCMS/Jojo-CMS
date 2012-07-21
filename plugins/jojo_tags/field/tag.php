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

//////////////////////TAGFIELD//////////////////////
class Jojo_Field_tag extends Jojo_Field
{
    var $interfacecutoff = 0; //Change from the rich interface to the fast interface after this many tags

    function displayedit()
    {
        global $smarty;

        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('readonly', $this->fd_readonly);
        $tags = Jojo_Plugin_Jojo_Tags::getTags(trim($this->getOption('options')), $this->table->getRecordID());
        $taglist = array();
        if (!empty($tags)) {
            foreach ($tags as $tagitem) {
                $taglist[] = $tagitem['tg_tag'];
            }
            $smarty->assign('taglist', Jojo_Plugin_Jojo_Tags::tagArrayToStr($taglist));
        } else {
            $smarty->assign('taglist', '');
        }
        return  $smarty->fetch('admin/tag.tpl');
    }

    function setvalue($newvalue)
    {
        $tagarray = Jojo_Plugin_Jojo_Tags::tagStrToArray($_POST['fm_' . $this->fd_field]);
        $this->value = Jojo_Plugin_Jojo_Tags::tagArrayToStr($tagarray);
        return true;
    }

    /* After save event */
    function afterSave()
    {
        /* Delete existing tags for this item */
        Jojo_Plugin_Jojo_Tags::deleteTags(trim($this->getOption('options')), $this->table->getRecordID());

        /* Save all the new tags */
        foreach(Jojo_Plugin_Jojo_Tags::tagStrToArray($this->value) as $tag) {
            Jojo_Plugin_Jojo_Tags::saveTag($tag, trim($this->getOption('options')), $this->table->getRecordID());
        }
    }
}