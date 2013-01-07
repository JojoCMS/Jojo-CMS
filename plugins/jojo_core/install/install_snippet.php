<?php

$table = 'snippet';
$query = "
CREATE TABLE {snippet} (
  `snippetid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `snippet` text NOT NULL,
  `snippet_code` text NOT NULL,
  `section` varchar(100) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`snippetid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("snippets: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("snippets: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);
