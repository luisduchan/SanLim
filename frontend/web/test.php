<?php
$myServer = "172.16.1.88";
$myUser = "hoaht";
$myPass = "hoaht123456";
$myDB = "SanlimDatabase"; 

try {
  # MS SQL Server and Sybase with PDO_DBLIB
  $conn = new PDO("sqlsrv:Server=$myServer;Database=$myDB", $myUser, $myPass);
  $query = 'SELECT top 10 * FROM [dbo].[SAN LIM FURNITURE VIETNAM LTD$Vendor]';
  $stmt = $conn->query($query);
  $a=$stmt->fetch(PDO::FETCH_ASSOC);var_dump($a);
}
catch(PDOException $e) {
    echo $e->getMessage();
}
?>