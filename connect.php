<?php
    $DSN_MYSQL = "mysql:dbname=[DATABASE NAME];host=localhost";
    $dbuser = '[DATABASE USER]';
    $dbpass = '[DATABASE PASSWORD]';

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