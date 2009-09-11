<?php

/* Set template engine to 'smarty' for existing installs */
$allOptions = Jojo::getOptions();
if ($allOptions && !isset($allOptions['templateengine'])) {
    Jojo::insertQuery("INSERT INTO {option} SET op_name=?, op_value=?", array('templateengine', 'smarty'));
}