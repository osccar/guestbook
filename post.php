<?php

    $option = filter_input(INPUT_GET, 'op', FILTER_SANITIZE_STRING);
    $entry_id = filter_input(INPUT_GET, 'entry', FILTER_SANITIZE_NUMBER_INT);

    // Delete a comment
    if ( isset($option) && $option=='del' && isset($entry_id) )
        {
        require "connect.php";
        $sql = "DELETE FROM guestbook WHERE id=$entry_id";
        if ( $dbh->exec($sql) )
            {
            print "Your comment has been deleted.<br>Click <a href='read.php'>here</a> return to the guestbook.";
            exit();
            }
        }

    // Insert new comment to guestbook
    if ( !isset($option) && isset($_POST['GuestName']) && isset($_POST['GuestPass']) )
        {
        require "connect.php";

        $unacceptable = array('fuu', 'ass', 'shii');
        $salt = 'guestbk';

        // Remove unacceptable words before submitting data to database
        $GuestMessage = str_ireplace($unacceptable, "***", $_POST['GuestMessage']);

        // Remove HTML tags and sanitize
        $GuestName = addslashes(strip_tags(rtrim( $_POST['GuestName'] )));
        $GuestPass = addslashes(strip_tags(rtrim( $_POST['GuestPass'] . '%' . $salt )));
        $GuestEmail = addslashes(strip_tags(rtrim( $_POST['GuestEmail'] )));
        $GuestMessage = addslashes(strip_tags(rtrim( $GuestMessage )));

        $sql = "INSERT INTO guestbook (guest_name, guest_pass, guest_email, guest_message)
                VALUES ('$GuestName', PASSWORD('$GuestPass') ,'$GuestEmail', '$GuestMessage')";

        if ( $stmt = $dbh->query($sql) )
            {
            exit("Thanks for posting - click <a href='read.php'>here</a> to view the guestbook with your message added!");
            }
        else
            {
            echo "There was an error adding your entry - please try again, filling in all fields.";
            }

        unset($_POST);
        unset($dbh);
        unset($query);
        }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post comments in Guestbook</title>
</head>
<body>
    <h2>Please leave a comment (be gentle!)</h2>

    <?php
    if ( isset($option) && $option=='edit' && isset($entry_id) )
        {
        require "connect.php";

        if ( $stmt = $dbh->query("SELECT COUNT(*) FROM guestbook") )
            {
            // TODO: revise and adapt SQL query for edited comments
            $sql = "SELECT guest_name, guest_email, guest_message, date_submitted
                    FROM guestbook
                    WHERE id=$entry_id";

            // Show comment in form
            $stmt = $dbh->query($sql);
            $entry = $stmt->fetchAll();
            //echo '<pre>';
            //var_dump($entry);

            $date_submitted = date("F j, Y g:i a", strtotime($entry[0]["date_submitted"]));
            print "
                <p><strong>Previously submitted on $date_submitted</strong></p>
                <form method='post' action='post.php'>
                    Name: <input type='text' name='GuestName' disabled value='{$entry[0]['guest_name']}'><br>
                    Email: <input type='text' name='GuestEmail' disabled value='{$entry[0]['guest_email']}'><br><br>
                    Message:<br><textarea rows='10' cols='40' name='GuestMessage'>{$entry[0]['guest_message']}</textarea>
                    <br><br>
                    <input type='submit' value='post'>
                </form>";
            }
        }
    else
        {
        print "
            <form method='post' action='post.php'>
            Name: <input type='text' name='GuestName'><br>
            Pass: <input type='password' name='GuestPass'><br>
            Email: <input type='text' name='GuestEmail'><br><br>
            Message:<br><textarea rows='10' cols='40' name='GuestMessage'></textarea>
            <br><br>
            <input type='submit' value='post'>
            </form>";
        }
    ?>

</body>
</html>
