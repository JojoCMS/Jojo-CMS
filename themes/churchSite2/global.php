<?php

if ($page->getValue('pg_parent') > 0) {
    /* Get sister pages to this page */
        $smarty->assign('subnav', _getNav($page->getValue('pg_parent'), 0));
} else {
    /* Get children pages of this page */
    $smarty->assign('subnav', _getNav($page->id, 0));
}
$smarty->assign('footernav', _getNav(0, 0, 'footernav'));

/* Latest Article for left side */
if (class_exists('Jojo_Plugin_Jojo_article')) {
    $smarty->assign('articles', Jojo_Plugin_Jojo_article::getArticles(3));
}