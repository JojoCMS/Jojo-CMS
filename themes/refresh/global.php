<?php

/* Latest Article for left side */
if (class_exists('Jojo_Plugin_Jojo_article')) {
    $smarty->assign('articles', Jojo_Plugin_Jojo_article::getArticles(3));
}