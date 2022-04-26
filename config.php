<?php

include_once 'FileMaker.php';

$fm = new FileMaker();
$fm->setProperty('database','Tigeen_TimeSheet_web');
$fm->setProperty('hostspec','fm.tigeensolutions.com');
$fm->setProperty('username','tigeen_web');
$fm->setProperty('password','webtigeen_@123');


?>
