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

$new_indexes = array(
                    'pg_url',
                    'pg_link',
                    'pg_parent',
                    'pg_mainnav',
                    'pg_mainnavalways',
                    'pg_secondarynav',
                    'pg_footernav',
                    'pg_order',
                    'pg_sitemapnav',
                    'pg_index',
                    'pg_status',
                    'pg_title',
                    array('pg_mainnav', 'pg_mainnavalways'),
                    array('pg_livedate', 'pg_expirydate')
                );
$indexes['page'] = array_merge($indexes['page'], $new_indexes);