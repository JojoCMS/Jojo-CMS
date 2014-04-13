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
 * @author  Tom Dale <tom@zero.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

header('Content-Type: application/json');
header("Cache-Control: must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")-2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

/* Ensure users of this function have access to the admin page */
$t = Jojo::getFormData('table', false);
$page = Jojo_Plugin::getPage(Jojo::parsepage('admin/edit/' . $t));
if (!$page->perms->hasPerm($_USERGROUPS, 'view')) {
    $nodes[] = array('data' => "Access Denied. Trying reloading the page", 'state' => array( 'opened' => false ));
    echo json_encode(array_values($nodes));
    exit;
}

$node = Jojo::getFormData('id', 0);
$nodes = array();
$nodes = getNodes($t, $node);

echo json_encode(array_values($nodes));
exit;

function getNodes($t, $node)
{
    /* Get the table */
    $table = new Jojo_Table($t);
    $pos = 0;

    /**
     * Real content under a parent
     */
    if ($table->getOption('parentfield')) {
        /* Get all children of the current node */
        $query = sprintf("SELECT %s as id, %s as title, %s as parent FROM {%s}",
                        $table->getOption('primarykey'),
                        $table->getOption('displayfield'),
                        $table->getOption('parentfield'),
                        $t
                        );
        $query .= $table->getOption('orderbyfields') ? ' ORDER BY ' . $table->getOption('orderbyfields') : '';
        $res = Jojo::selectQuery($query);

        /* Add the nodes to the array for output - jstree will sort out the structure */
        foreach ($res as $r) {
            $nodes[$r['id']] = array(
                                'id'        => $r['id'],
                                'text'     => $r['title'],
                                'parent'     => ( $r['parent'] ? $r['parent'] : '#' ),
                                'state'    => array( 'opened' => false, 'selected' => false ),
                                'type' => 'file',
                                'li_attr'     => array ('pos' => $pos++)
                               );
        }


    } elseif ($table->getOption('categorytable') || $table->getOption('m2mcategoryfield')) {
        $m2mfield = false;
        if ($table->getOption('m2mcategoryfield')) {
            $m2mfield = new Jojo_Field(
                Jojo::selectRow(
                    "SELECT * FROM {fielddata} WHERE fd_table = ? AND fd_field = ?",
                    array(
                        $t,
                        $table->getOption('m2mcategoryfield')
                    )
                )
            );
            $categoryTable = new Jojo_Table($m2mfield->cattable);
        } else {
            $categoryTable = new Jojo_Table($table->getOption('categorytable'));
        }
        $isItemNode = true;
        if ($node[0] == 'c') {
            $node = substr($node, 1);
            $isItemNode = false;
        }
        if ($node == 0) {
            $isItemNode = false;
        }
        $pos = 0;
        $nodes = array();
        if (!$isItemNode && $categoryTable->getOption('parentfield')) {
            /* Get categories */
            $query = sprintf("SELECT %s as id, %s as title, %s as parent FROM {%s}",
                            $categoryTable->getOption('primarykey'),
                            $categoryTable->getOption('displayfield'),
                            $categoryTable->getOption('parentfield'),
                            $categoryTable->getTableName()
                            );
            $query .= $categoryTable->getOption('orderbyfields') ? ' ORDER BY ' . $categoryTable->getOption('orderbyfields') : '';
            $res = Jojo::selectQuery($query);

            if ($categoryTable->getOption('displayfield')) {
                $displayfielddata = Jojo::selectRow("SELECT fd_type, fd_options FROM {fielddata} WHERE fd_table = ? AND fd_field = ?", array($categoryTable->getTableName(), $categoryTable->getOption('displayfield')));
                $displayfieldtype = $displayfielddata['fd_type'];
                $displayfieldoptions = $displayfielddata['fd_options'];
                if ($displayfieldtype == 'dbpluginpagelist') {
                    $displaytitles = Jojo::selectAssoc("SELECT pageid AS id, pageid, pg_title, pg_language FROM {page} WHERE pg_link = ? ", array($displayfielddata['fd_options']));
                    foreach ($res as &$r) {
                        $r['title'] = isset($displaytitles[$r['title']]['pg_title']) ? $displaytitles[$r['title']]['pg_title'] . (_MULTILANGUAGE ? ' (' . $displaytitles[$r['title']]['pg_language'] . ')' : '') : 'page missing';
                    }
                }
            }
            /* Add the nodes to the array for output */
            foreach ($res as $r) {
                $nodes['c' . $r['id']] = array(
                        'id'        => 'c' . $r['id'],
                        'text'     => $r['title'],
                        'parent'     => ($r['parent'] ? 'c' . $r['parent'] : '#'),
                        'state'    => array( 'opened' => false, 'selected' => false ),
                        'type' => 'folder',
                        'li_attr'     => array ('pos' => $pos++)
                    );
            }

            if ($m2mfield) {
                /* Add nodes from m2m categories */
                $values = array();
                if ($node == 0) {
                    // Root display, show products that aren't in any categories
                    $where = "IS NULL";
                } else {
                    $where = "= ?";
                    $values = $node;
                }
                $query = sprintf("SELECT t.%s as id, %s as title FROM {%s} t LEFT JOIN {%s} l ON t.%s = l.%s WHERE l.%s ".$where,
                                 $table->getOption('primarykey'),
                                 $table->getOption('displayfield'),
                                 $t,
                                 $m2mfield->linktable,
                                 $table->getOption('primarykey'),
                                 $m2mfield->linkitemid,
                                 $m2mfield->linkcatid
                                );
                $query .= $table->getOption('orderbyfields') ? ' ORDER BY ' . $table->getOption('orderbyfields') : '';
                $res = Jojo::selectQuery($query);

                /* Add the nodes to the array for output */
                foreach ($res as $r) {
                    $nodes[$r['id']] = array(
                        'id'        => $r['id'],
                        'text'     => $r['title'],
                        'parent'     => 'c' . $node,
                        'type' => 'file',
                        'state'    => array( 'opened' => false, 'selected' => false ),
                        'li_attr'     => array ('pos' => $pos++)
                    );
                }
            }
            if ($table->getOption('categoryfield')) {
                /* Add nodes from traditional categories */
                $query = sprintf("SELECT %s as id, %s as title, %s as parentcategory FROM {%s}",
                                 $table->getOption('primarykey'),
                                 $table->getOption('displayfield'),
                                 $table->getOption('categoryfield'),
                                 $t
                                );
                $query .= $table->getOption('orderbyfields') ? ' ORDER BY ' . $table->getOption('orderbyfields') : '';
                $res = Jojo::selectQuery($query);

                /* Add the nodes to the array for output */
                foreach ($res as $r) {
                    $nodes['c' . $r['id']] = array(
                        'id'        => $r['id'],
                        'text'     => $r['title'],
                        'parent'     => 'c' . $r['parentcategory'],
                        'state'    => array( 'opened' => false, 'selected' => false ),
                        'li_attr'     => array ('pos' => $pos++),
                        'type' => 'file'
                    );
                }
            }

        } else {
            /* Get categories if at root level */
            if ($node == 0) {
                $query = sprintf("SELECT %s as id, %s as title FROM {%s}",
                                $categoryTable->getOption('primarykey'),
                                $categoryTable->getOption('displayfield'),
                                $categoryTable->getTableName()
                                );
                $query .= $categoryTable->getOption('orderbyfields') ? ' ORDER BY ' . $categoryTable->getOption('orderbyfields') : '';
                $res = Jojo::selectQuery($query);

                if ($categoryTable->getOption('displayfield')) {
                    $displayfielddata = Jojo::selectRow("SELECT fd_type, fd_options FROM {fielddata} WHERE fd_table = ? AND fd_field = ?", array($categoryTable->getTableName(), $categoryTable->getOption('displayfield')));
                    $displayfieldtype = isset($displayfielddata['fd_type']) ? $displayfielddata['fd_type'] : '';
                    $displayfieldoptions = isset($displayfielddata['fd_options']) ? $displayfielddata['fd_options'] : '';
                    if ($displayfieldtype == 'dbpluginpagelist') {
                        $displaytitles = Jojo::selectAssoc("SELECT pageid AS id, pageid, pg_title, pg_language FROM {page} WHERE pg_link = ? ", array($displayfielddata['fd_options']));
                        foreach ($res as $k=>$r) {
                            $res[$k]['title'] = isset($displaytitles[$r['title']]['pg_title']) ? $displaytitles[$r['title']]['pg_title'] . (_MULTILANGUAGE ? ' (' . $displaytitles[$r['title']]['pg_language'] . ')' : '') : 'page missing';
                        }
                    }
               }
                /* Add the nodes to the array for output */
                foreach ($res as $k=>$r) {
                    $nodes['c' . $r['id']] = array(
                        'id'        => 'c' . $r['id'],
                        'text'     => $r['title'],
                        'parent'     => $node,
                        'state'    => array( 'opened' => false, 'selected' => false ),
                        'type'   => 'folder',
                        'li_attr'     => array ('pos' => $pos++),
                      );
                }
            }

            /* Add nodes */
            $res = array();
            if ($table->getOption('categorytable')) {
                $query = sprintf("SELECT %s as id, %s as title, %s as parentcategory FROM {%s}",
                                 $table->getOption('primarykey'),
                                 $table->getOption('displayfield'),
                                 $table->getOption('categoryfield'),
                                 $t
                                );
                $query .= $table->getOption('orderbyfields') ? ' ORDER BY ' . $table->getOption('orderbyfields') : '';
                $res = Jojo::selectQuery($query);
            }
            if (isset($m2mfield) && $m2mfield) {
                $query = sprintf("SELECT n.%s as id, %s as title FROM {%s} n LEFT JOIN {%s} l ON n.%s = l.%s WHERE l.%s IS NULL",
                                 $table->getOption('primarykey'),
                                 $table->getOption('displayfield'),
                                 $t,
                                 $m2mfield->linktable,
                                 $table->getOption('primarykey'),
                                 $m2mfield->linkitemid,
                                 $m2mfield->linkcatid
                                );
                $query .= $table->getOption('orderbyfields') ? ' ORDER BY ' . $table->getOption('orderbyfields') : '';
                $res = Jojo::selectQuery($query);
            }

            /* Add the nodes to the array for output */
            foreach ($res as $r) {
                $nodes[$r['id']] = array(
                        'id'        => $r['id'],
                        'text'     => $r['title'],
                        'parent'     => 'c' . $r['parentcategory'],
                        'state'    => array( 'opened' => false, 'selected' => false ),
                        'type'   => 'file',
                        'li_attr' => array ('pos' => $pos++)
                    );
            
            }
        }

    /**
     * Top level grouping
     */
    } elseif ($table->getOption('group1') && !$node) {
        /* Top Level groups */
        $query = sprintf("SELECT DISTINCT %s as id FROM {%s} ORDER BY %s",
                        $table->getOption('group1'),
                        $t,
                        $table->getOption('group1')
                        );
        $values = array();
        $res = Jojo::selectQuery($query, $values);

        /* Add the nodes to the array for output */
        $pos = 0;
        $nodes = array();
        foreach ($res as $r) {
            $r['id'] = ($r['id'] != '') ? $r['id'] : 'noname';
            $nodes[$r['id']] = array(
                                'id' => '|' . $r['id'],
                                'text'     => $r['id'],
                                'parent' => '',
                                'type' => 'file',
                                'state' => array( 'opened' => false, 'selected' => false),
                               );
        }
 
    /**
     * Second level groups under a first level group
     * Node id = |group1id
     */
    } elseif ($table->getOption('group1') && $table->getOption('group2') && $node[0] == '|') {
        $node = substr($node, 1);
        $node = ($node == 'noname') ? '' : $node;
        $query = sprintf("SELECT DISTINCT %s as id FROM {%s} WHERE %s = ? ORDER BY %s",
                        $table->getOption('group2'),
                        $t,
                        $table->getOption('group1'),
                        $table->getOption('group2')
                        );
        $values = array($node);
        $res = Jojo::selectQuery($query, $values);

        /* Add the nodes to the array for output */
        $pos = 0;
        $nodes = array();
        foreach ($res as $r) {
            $r['id'] = ($r['id'] != '') ? $r['id'] : 'noname';
            $nodes[$r['id']] = array(
                                'id' => '~' . $r['id'] . '|' . $node,
                                'text'     => $r['id'],
                                'parent' => 'g' . $node,
                                'type' => 'file',
                                'state' => array( 'opened' => false, 'selected' => false),
                                'li_attr' => array ('pos' => $pos++)
                               );
        }

    /**
     * Real contents under a first level group
     * Node id = |group1id
     */
    } elseif ($table->getOption('group1') && $node[0] == '|') {
        $node = substr($node, 1);
        $node = ($node == 'noname') ? '' : $node;
        $query = sprintf("SELECT %s as id, %s as title FROM {%s} WHERE %s = ?",
                        $table->getOption('primarykey'),
                        $table->getOption('displayfield'),
                        $table->getTableName(),
                        $table->getOption('group1')
                        );
        $query .= $table->getOption('orderbyfields') ? ' ORDER BY ' . $table->getOption('orderbyfields') : '';
        $values = array($node);
        $res = Jojo::selectQuery($query, $values);

        /* Add the nodes to the array for output */
        $pos = 0;
        $nodes = array();
        foreach ($res as $r) {
            $nodes[$r['id']] = array(
                                'id' => $r['id'],
                                'text'     => $r['title'],
                                'parent' => '|' . $node,
                                'type' => 'file',
                                'state' => array( 'opened' => false, 'selected' => false),
                                'li_attr' => array ('pos' => $pos++)
                                );
        }

    /**
     * Real content under a 2nd level group
     * Node id = ~group2id|group1id
     */
    } elseif ($table->getOption('group1') && $table->getOption('group2') && $node[0] == '~') {
        $g1 = substr($node, strpos($node, '|') + 1);
        $g1 = ($g1 == 'noname') ? '' : $g1;
        $g2 = substr($node, 1, strpos($node, '|') - 1);
        $g2 = ($g2 == 'noname') ? '' : $g2;
        $query = sprintf("SELECT %s as id, %s as title FROM {%s} WHERE %s = ? AND %s = ?",
                        $table->getOption('primarykey'),
                        $table->getOption('displayfield'),
                        $table->getTableName(),
                        $table->getOption('group1'),
                        $table->getOption('group2')
                        );
        $query .= $table->getOption('orderbyfields') ? ' ORDER BY ' . $table->getOption('orderbyfields') : '';
        $values = array($g1, $g2);
        $res = Jojo::selectQuery($query, $values);

        /* Add the nodes to the array for output */
        $pos = 0;
        $nodes = array();
        foreach ($res as $r) {
            $nodes[$r['id']] = array(
                                'li_attr'     => array ('pos' => $pos++),
                                'id'     => $r['id'],
                                'text'     => $r['title'],
                                'parent' => $node,
                                'type' => 'file',
                                'state' => array( 'opened' => false, 'selected' => false)
                               );
        }

    }
    return $nodes;
}

