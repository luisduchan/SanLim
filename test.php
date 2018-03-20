<?php
$myServer = "172.16.1.88";
$myUser = "hoaht";
$myPass = "hoaht123456";
$myDB = "SanlimDatabase"; 
die('aa');
try {
  # MS SQL Server and Sybase with PDO_DBLIB
  $DBH = new PDO("dblib:host=$myServer;dbname=$myDB", $myUser, $myPass);
}
catch(PDOException $e) {
    echo $e->getMessage();
}
?>