<?php
    $serverName="DESKTOP-E3NO62N\MSSQLSERVER01";
    $connectionInfo = array("Database"=>"btl");
    $conn = sqlsrv_connect($serverName,$connectionInfo);

    if($conn) {
        echo "Connection established.<br/>";
    }
    else {
        echo "Connection could not be established.<br/>";
        die(print_r(sqlsrv_errors(),true));
    }

    $tsql ="SELECT Genre_name FROM GENRE";

    $stmt = sqlsrv_query($conn,$tsql);
    if($stmt==false) {
        echo "Lỗi truy vấn.<br/>";
        die(print_r(sqlsrv_errors(),true));
    }

    $row =[];
    while($row=sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC)) {
        echo $row['Genre_name']."<br>";
    }

    $tsql2="INSERT INTO GENRE (Genre_name) VALUES ('WIBU')";

    $stmt2 = sqlsrv_query($conn,$tsql2);
    if($stmt2==false) {
        echo "Loi Truy van.<br>";
        die(print_r(sqlsrv_errors(),true));
    }

    echo "here";
?>