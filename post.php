<?php
    /**
     * POST
     *
     * This file has two purposes:
     *      - post a new comment
     *      - edit a selected comment
     *
     *  TODO: it has basic input filtering and sanitization but a more curated filtering should be done
     *      in order to make it more robust.
     *
     */
    
    $unacceptable = array('fuck', 'ass', 'shit', 'asshole', 'jerk');
    $pepper = 'guestbk';
    $salt = '%';
    $insert_info = '';
    $error_msg = '';
    $error_found = 0;   // 0 = No, 1 = Yes
    
    
    // Sanitize and filter input vars (GET & POST)
    
    if ( filter_has_var(INPUT_GET, 'op') )
        $option = @filter_input( INPUT_GET, 'op', FILTER_SANITIZE_STRING );
        
    if ( filter_has_var(INPUT_GET, 'entry') )
        $entry_id = (int) @filter_input( INPUT_GET, 'entry', FILTER_SANITIZE_STRING );
        
    if ( @filter_has_var(INPUT_POST, 'GuestID') )
        $GuestID = (int) @filter_input( INPUT_POST, 'GuestID', FILTER_SANITIZE_STRING );
    
    /**
     * Basic requirements for all new comments:
     *  - all fields must be filled
     *  - all fields must be sanitized before inserting to DB
     *
     */
    // Check all POST fields
    if ( isset($_POST) )
        {
        foreach ( $_POST as $field => $value )
            {
            // Min. is 1 character
            if ( isset($_POST[$field]) && strlen(trim($value)) < 1 )
                {
                $error_msg = 'Please fill in all fields.';
                $error_found = 1;
                }
            // Minimal email validation. Can be improved!
            if ( $field === 'GuestEmail' )
                if ( filter_var($value, FILTER_VALIDATE_EMAIL) === FALSE ) $error_found = 1;
            }
        }
    
    // Clean input data
    $GuestName = addslashes(strip_tags(trim( $_POST['GuestName'] )));
    $GuestPass = addslashes(strip_tags(trim( $_POST['GuestPass'] . $salt . $pepper )));
    $GuestEmail = addslashes(strip_tags(trim( $_POST['GuestEmail'] )));
    $GuestMessage = addslashes(strip_tags(trim( $_POST['GuestMessage'] )));
    
    
    /**
     * TODO: in-depth filtering should be added for robustness
     *
     * Add below...
     *  
     */



    /** PROCESS COMMENTS ***********************************/

    // Edited message but no password provided
    if ( !isset($GuestID) && !isset($GuestPass) )
        exit("Please enter you password in order to edit the message. Try <a href='read.php'>again</a>.");

    // Edit
    if ( isset($GuestID) && isset($GuestPass) )
        {
        require "connect.php";

        $GuestPass = addslashes(strip_tags(rtrim( $_POST['GuestPass'] . $salt . $pepper )));
        $GuestMessage = addslashes(strip_tags(rtrim( $_POST['GuestMessage'] )));

        // Verify password
        if ( $stmt = $dbh->query("SELECT COUNT(id) FROM guestbook WHERE guest_pass = PASSWORD('$GuestPass')") )
            {
            if ( $stmt->fetchColumn() )
                {   // update DB
                $sql = "UPDATE guestbook
                        SET guest_message = '$GuestMessage', date_submitted = CURRENT_TIMESTAMP
                        WHERE id = $GuestID";

                if ( $dbh->exec($sql) )
                    exit("Your comment has been successfully updated. <a href='read.php'>View</a> all guestbook messages.");
                else
                    exit("Something went wrong. Please try again.");
                }
            else
                exit("Invalid password. Try <a href='read.php'>again</a>.");
            }
         unset($_POST);
         unset($dbh);
        }

    // New comment or Edit comment
    if ( !isset($option) && isset($GuestName) && isset($GuestPass) )
        {
        require "connect.php";

        $sql = "INSERT INTO guestbook (guest_name, guest_pass, guest_email, guest_message)
                VALUES ('$GuestName', PASSWORD('$GuestPass') ,'$GuestEmail', '$GuestMessage')";

        // Insert comment or bust!
        if ( $stmt = $dbh->query($sql) )
            $insert_info = "Thanks for leaving a comment. View the guestbook <a href='read.php'>here</a>.";
        else
            $insert_info = "There was an error adding your entry. Please try again.";

        unset($_POST);
        unset($dbh);
        }
?>

<!DOCTYPE html>
<html lang=es>
<head>
    <meta charset=utf-8>
    <title>Post comments in Guestbook</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Leave a comment (please be gentle!)</h2>

    <?php
    // Edit comment in guestbook
    if ( isset($option) && $option==='edit' && isset($entry_id) )
        {
        require "connect.php";
        if ( $stmt = $dbh->query("SELECT COUNT(*) FROM guestbook") )
            {
            $sql = "SELECT guest_name, guest_email, date_submitted
                    FROM guestbook
                    WHERE id = $entry_id";

            $stmt = $dbh->query($sql);
            $entry = $stmt->fetchAll();
            $date_submitted = date("F j, Y g:i a", strtotime($entry[0]["date_submitted"]));
            print "
                <p><strong>Previously submitted on $date_submitted</strong></p>
                <form method='post' action='post.php'>
                    <p>Name: {$entry[0]['guest_name']}</p>
                    Password: <input type='password' name='GuestPass'><br>
                    <p>Email: {$entry[0]['guest_email']}</p>
                    Message:<br><textarea rows='10' cols='40' name='GuestMessage'></textarea>
                    <br>
                    <input type='hidden' name='GuestName' value='{$entry[0]['guest_name']}'>
                    <input type='hidden' name='GuestEmail' value='{$entry[0]['guest_email']}'>
                    <input type='hidden' name='GuestID' value='$entry_id'>
                    <input type='submit' value='Post comment'>
                </form><br>";
            print "<br><a href='read.php'>Read comments</a>";
            }
        }
    // Alert errors
    elseif ( $error_found )
        {
        print '<div class=insert-info>'. $
        
        }
    // Post a new comment
    else
        {
        if ( !empty($insert_info) )
            print "<div class=insert-info>$insert_info</div>";
    ?>
        <div id=comments-form>
            <form method=post action=post.php>
                <p>Name</p>
                <p><input type=text name=GuestName placeholder='John Doe'></p>
                <p>Password</p>
                <p><input type=password name=GuestPass placeholder='Make it good!'></p>
                <p>Email</p>
                <p><input type=email name=GuestEmail placeholder=name@domain.com></p>
                <p>Comment</p>
                <p><textarea rows=10 cols=60 name=GuestMessage></textarea></p>
                <br>
                <input type=submit class='button cyan' value='Leave a comment'>
            </form>
        </div>
    <?php } ?>
</body>
</html>
