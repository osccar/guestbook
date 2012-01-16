<?php
    $DSN_MYSQL = "mysql:dbname=test;host=localhost";
    $dbuser = 'root';
    $dbpass = 'AdminSQL';

    try {
        $dbh = new PDO($DSN_MYSQL, $dbuser, $dbpass);
        //$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch ( PDOException $e ){
        $dbh = NULL;
        exit("<strong>Connection failed:</strong> ". $e->getMessage() ."<br />");
    }