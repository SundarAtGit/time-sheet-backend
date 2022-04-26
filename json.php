<?php

include "config.php";

// $username="tigeen";
// $password="1234";
$username = $_GET["username"];
$password = $_GET["password"];

$findCommand = $fm->newFindCommand('PHP__CON');
$findCommand->addFindCriterion('Username', $username);
$findCommand->addFindCriterion('Password', $password);
// $findCommand->addFindCriterion('__kp__ConId__lsan', $conid);
$result = $findCommand->execute();
// $count = $result->getFoundSetCount();
if (FileMaker::isError($result)) {
    echo($result->getMessage());
    return;
}
$record = $result -> getFirstRecord();

$conid = $record->getField("__kp__ConId__lsan");
// $pass = $record->getField("Password");

$temp = [[
    'Username' => "$username",
    'Password' => "$password",
    '__kp__ConId__lsan' => "$conid"
]];


echo json_encode($temp);

?>

