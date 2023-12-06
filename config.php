<?php

$serverName="DESKTOP-E3NO62N\MSSQLSERVER01";
$connectionInfo=array("Database"=>"DBMSAssignmentOrderBook");
$conn=sqlsrv_connect($serverName,$connectionInfo);

if(!$conn) {
    echo "<script>alert('Connection failed');</script>";
    die(print_r(sqlsrv_errors(),true));
}

?>