<?php
    $DSN_SQLITE = "sqlite:/home/osccar/www/guestbook/guestbook.sqlite";
    $DSN_MYSQL = "mysql:dbname=test;host=localhost";
    $dbuser = 'root';
    $dbpass = 'AdminSQL';

    // Open DB connection (sqlite3)
    try {
        //$dbh = new PDO($DSN_SQLITE);
        $dbh = new PDO($DSN_MYSQL, $dbuser, $dbpass);
        //$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        $dbh = null;
        print "Connection failed! ". $e->getMessage() ."<br />";
        exit();
    }