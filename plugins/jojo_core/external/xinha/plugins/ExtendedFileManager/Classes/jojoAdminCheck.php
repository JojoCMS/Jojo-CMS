<?php

if(!(isset($isadmin) && $isadmin )){
        echo "Must be logged in as admin";
        die();exit();
}

