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
 * @package jojo_core
 */

/* Ensure users of this function have access to the admin page */
$tablename = Jojo::getPost('table');
$page = Jojo_Plugin::getPage(Jojo::parsepage('admin/edit/' . $tablename));
if (!$page->perms->hasPerm($_USERGROUPS, 'view')) {
    echo "Permission Denied";
    exit;
}

/* Get Table */
$table = Jojo_Table::singleton($tablename);


$id        = Jojo::getPost('id');
$newParent = Jojo::getPost('newParent');
$newPos    = Jojo::getPost('order');


$groupbyfield = $table->getOption('group1');
$orderfield = $table->getFieldByType('Jojo_Field_Order');

/**
 * All under a parent id
 */
if ($table->getOption('parentfield')) {
    $idfield = $table->getOption('primarykey');
    $parentfield = $table->getOption('parentfield');

    if ($orderfield) {
        /* Re-parent and re-order */
        $orderfield = $orderfield->getOption('field');
        $res = Jojo::selectQuery("SELECT `$idfield` FROM {{$tablename}} WHERE $parentfield = ? ORDER BY $orderfield", array($newParent));
        $siblings = array();
        foreach ($res as $s) {
            $siblings[] = $s[$idfield];
        }

        /* Ensure this page isn't in there already */
        $currentPos = array_search($id, $siblings);
        if ($currentPos !== false) {
            unset($siblings[$currentPos]);
            $siblings = array_values($siblings);
        }

        /* Insert the new page */
        array_splice($siblings, $newPos, 0, $id);

        /* Re-parent page */
        $query = "UPDATE {{$tablename}} SET $parentfield = ?, $orderfield = ? WHERE $idfield = ?";
        $values = array($newParent, $newPos, $id);
        Jojo::updateQuery($query, $values);

        /* Re-order pages */
        $query = "UPDATE {{$tablename}} SET $orderfield = ? WHERE $idfield = ?";
        foreach($siblings as $o => $sid) {
            $values = array($o, $sid);
            Jojo::updateQuery($query, $values);
        }

        echo "Moved by parent id and re-ordered";
        exit;
    } else {
        /* Re-parent page */
        $query = "UPDATE {{$tablename}} SET $parentfield = ? WHERE $idfield = ?";
        $values = array($newParent, $id);
        Jojo::updateQuery($query, $values);

        echo "Moved by parent id";
        exit;
    }
}

/**
 * All under a category
 */
 elseif ($categoryfield = $table->getOption('categoryfield')) {

    $idfield = $table->getOption('primarykey');
 
    $newParent = ltrim($newParent, 'c');

    if ($orderfield) {
        /* Re-categorize and re-order */
        $orderfield = $orderfield->getOption('field');
        $res = Jojo::selectQuery("SELECT `$idfield` FROM {{$tablename}} WHERE $categoryfield = ? ORDER BY $orderfield", array($newParent));
        $siblings = array();
        foreach ($res as $s) {
            $siblings[] = $s[$idfield];
        }

        /* Ensure this item isn't in there already */
        $currentPos = array_search($id, $siblings);
        if ($currentPos !== false) {
            unset($siblings[$currentPos]);
            $siblings = array_values($siblings);
        }

        /* Insert the new item */
        array_splice($siblings, $newPos, 0, $id);

        /* Re-categorize item */
        $query = "UPDATE {{$tablename}} SET $categoryfield = ?, $orderfield = ? WHERE $idfield = ?";
        $values = array($newParent, $newPos, $id);
        Jojo::updateQuery($query, $values);

        /* Re-order item */
        $query = "UPDATE {{$tablename}} SET $orderfield = ? WHERE $idfield = ?";
        foreach($siblings as $o => $sid) {
            $values = array($o, $sid);
            Jojo::updateQuery($query, $values);
        }

        echo "Moved by category id and re-ordered";
        exit;
    } else {
        /* Re-categorize item */
        $query = "UPDATE {{$tablename}} SET $categoryfield = ? WHERE $idfield = ?";
        $values = array($newParent, $id);
        Jojo::updateQuery($query, $values);

        echo "Moved by category id";
        exit;
    }
}

/**
 * All under a second level group
 */
elseif ($table->getOption('group1') && $table->getOption('group2') && $newParent[0] == '~') {
    $g1 = substr($newParent, strpos($newParent, '|') + 1);
    $g1 = ($g1 == 'noname') ? '' : $g1;
    $g2 = substr($newParent, 1, strpos($newParent, '|') - 1);
    $g2 = ($g2 == 'noname') ? '' : $g2;

    /* Check group column values already exist */
    $query = sprintf("SELECT `%s` as id FROM {%s} WHERE `%s` = ? AND `%s` = ?",
                    $table->getOption('primarykey'),
                    $table->getTableName(),
                    $table->getOption('group1'),
                    $table->getOption('group2')
                    );
    $values = array($g1, $g2);

    $newSiblings = Jojo::selectQuery($query, $values);
    if (!$newSiblings) {
        echo "Not Moved - groups not found";
        exit;
    }

    if ($orderfield) {
        /* Re parent and re order rows */
        $siblings = array();
        foreach ($newSiblings as $s) {
            $siblings[] = $s['id'];
        }

        /* Ensure this row isn't in there in a different position */
        $currentPos = array_search($id, $siblings);
        if ($currentPos !== false) {
            unset($siblings[$currentPos]);
            $siblings = array_values($siblings);
        }

        /* Insert the new row */
        array_splice($siblings, $newPos, 0, $id);

        /* Re-parent row */
        $query = sprintf("UPDATE {%s} SET `%s` = ?, `%s` = ?, `%s` = ? WHERE `%s` = ?",
                        $table->getTableName(),
                        $table->getOption('group1'),
                        $table->getOption('group2'),
                        $orderfield->getOption('field'),
                        $table->getOption('primarykey')
                        );
        $values = array($g1, $g2, $newPos, $id);
        Jojo::updateQuery($query, $values);

        /* Save new order of sibling rows */
        $query = sprintf("UPDATE {%s} SET `%s` = ? WHERE `%s` = ?",
                        $table->getTableName(),
                        $orderfield->getOption('field'),
                        $table->getOption('primarykey')
                        );
        foreach($siblings as $o => $sid) {
            Jojo::updateQuery($query, array($o, $sid));
        }

        echo "Moved - group1 and group2 changed updated and re-ordered";
    } else {
        /* Re-parent page */
        $query = sprintf("UPDATE {%s} SET `%s` = ?, `%s` = ? WHERE `%s` = ?",
                        $table->getTableName(),
                        $table->getOption('group1'),
                        $table->getOption('group2'),
                        $table->getOption('primarykey')
                        );
        $values = array($g1, $g2, $id);

        Jojo::updateQuery($query, $values);

        echo "Moved - group1 and group2 changed updated";
    }

}

if ($table->getOption('group1') && !$table->getOption('group2') && $newParent[0] == '|') {
    $newParent = substr($newParent, 1);
    $newParent = ($newParent == 'noname') ? '' : $newParent;

    /* Check group column values already exist */
    $query = sprintf("SELECT `%s` as id FROM {%s} WHERE `%s` = ?",
                    $table->getOption('primarykey'),
                    $table->getTableName(),
                    $table->getOption('group1')
                    );
    $newSiblings = Jojo::selectQuery($query, $newParent);
    if (!$newSiblings) {
        echo "Not Moved - group not found";
        exit;
    }

    $orderfield = $table->getFieldByType('Jojo_Field_Order');
    if ($orderfield) {
        /* Re parent and re order rows */
        $siblings = array();
        foreach ($newSiblings as $s) {
            $siblings[] = $s['id'];
        }

        /* Ensure this row isn't in there in a different position */
        $currentPos = array_search($id, $siblings);
        if ($currentPos !== false) {
            unset($siblings[$currentPos]);
            $siblings = array_values($siblings);
        }

        /* Insert the new row */
        array_splice($siblings, $newPos, 0, $id);

        /* Re-parent row */
        $query = sprintf("UPDATE {%s} SET `%s` = ?, `%s` = ? WHERE `%s` = ?",
                        $table->getTableName(),
                        $table->getOption('group1'),
                        $orderfield->getOption('field'),
                        $table->getOption('primarykey')
                        );
        $values = array($newParent, $newPos, $id);
        Jojo::updateQuery($query, $values);

        /* Save new order of sibling rows */
        $query = sprintf("UPDATE {%s} SET `%s` = ? WHERE `%s` = ?",
                        $table->getTableName(),
                        $orderfield->getOption('field'),
                        $table->getOption('primarykey')
                        );
        foreach($siblings as $o => $sid) {
            Jojo::updateQuery($query, array($o, $sid));
        }

        echo "Moved - group1 changed updated and re-ordered";
    } else {
        /* Re-parent page */
        $query = sprintf("UPDATE {%s} SET `%s` = ?WHERE `%s` = ?",
                        $table->getTableName(),
                        $table->getOption('group1'),
                        $table->getOption('primarykey')
                        );
        $values = array($newParent, $id);

        Jojo::updateQuery($query, $values);
        echo "Moved - group1 changed updated";
    }
}

/**
 * Just a straight list
 */
elseif ($orderfield) {
    $idfield = $table->getOption('primarykey');

    /* Re-order */
    $orderfield = $orderfield->getOption('field');
    $res = Jojo::selectQuery("SELECT `$idfield` FROM {{$tablename}} ORDER BY $orderfield");
    $siblings = array();
    foreach ($res as $s) {
        $siblings[] = $s[$idfield];
    }

    /* Ensure this item isn't in there already */
    $currentPos = array_search($id, $siblings);
    if ($currentPos !== false) {
        unset($siblings[$currentPos]);
        $siblings = array_values($siblings);
    }

    /* Insert the new item */
    array_splice($siblings, $newPos, 0, $id);

    /* Re-order items */
    $query = "UPDATE {{$tablename}} SET $orderfield = ? WHERE $idfield = ?";
    foreach($siblings as $o => $sid) {
        $values = array($o, $sid);
        Jojo::updateQuery($query, $values);
    }

    echo "Re-ordered";
    exit;
}
