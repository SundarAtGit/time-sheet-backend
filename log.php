<?php

include "db_con.php" ;

$filename = "log.json";
$data = file_get_contents($filename);
$array = json_decode($data,true);
foreach($array as $row)
{
    $sql = "INSERT INTO user (username,password) VALUES ('".$row["username"]."','".$row["password"]."')";
    mysqli_query($con,$sql);
}
 echo "Data Inserted";