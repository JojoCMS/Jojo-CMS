<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2010 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

/*
  $indexes[TABLENAME] has already been defined. Add string (for single-column indexes) or array (for multi-column indexes) elements to this
  eg $indexes['my_table'][] = 'my_field';
     $indexes['my_table'][] = array('my_field', 'my_other_field');
  
  DO NOT overwrite the base array - this will prevent other plugins from defining indexes on this table
  eg $indexes['my_table'] = array('my_field'); //don't do this
 */

$indexes['option'][] = 'op_category';
