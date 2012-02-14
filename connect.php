<?php
    $DSN_MYSQL = "mysql:dbname=test;host=localhost";
    $dbuser = 'osccar';
    $dbpass = 'AdminSQL';

    try {
        $dbh = new PDO($DSN_MYSQL, $dbuser, $dbpass);
        # Uncomment the next line if your prefer accessing data in an object style instead of array style
        //$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch ( PDOException $e ){
        $dbh = NULL;
        exit("<strong>Connection failed:</strong> ". $e->getMessage() ."<br />");
    }