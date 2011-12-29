<?php
    // VARS:
    $unacceptable = array('fuck', 'ass', 'shit', 'asshole', 'jerk');
    $pepper = 'guestbk';
    $salt = '%';

    // Sanitize input
    @$option = filter_input(INPUT_GET, 'op', FILTER_SANITIZE_STRING);

    // PROCESS COMMENTS:
    // Delete
    if ( isset($option) && $option=='del' && isset($entry_id) )
        {
        require "connect.php";
        $sql = "DELETE FROM guestbook WHERE id=$entry_id";
        if ( $dbh->exec($sql) )
            {
            unset($dbh);
            exit("Your comment has been deleted.<br>Click <a href='read.php'>here</a> return to the guestbook.");
            }
        }

    // Edited message but no password provided
    if ( isset($_POST['GuestID']) && $_POST['GuestPass'] == '' )
        {
        exit("Please enter you password in order to edit the message. Try <a href='read.php'>again</a>.");
        }

    // Edit
    if ( isset($_POST['GuestID']) && isset($_POST['GuestPass']) )
        {
        require "connect.php";

        $GuestPass = addslashes(strip_tags(rtrim( $_POST['GuestPass'] . $salt . $pepper )));
        $GuestID = intval($_POST['GuestID']);
        $GuestMessage = addslashes(strip_tags(rtrim( $_POST['GuestMessage'] )));
        $GuestMessage = str_ireplace($unacceptable, "***", $GuestMessage);  // Remove unacceptable words

        // Verify password
        if ( $stmt = $dbh->query("SELECT COUNT(id) FROM guestbook WHERE guest_pass = PASSWORD('$GuestPass')") )
            {
            if ( $stmt->fetchColumn() )
                {   // update DB
                $sql = "UPDATE guestbook
                        SET guest_message = '$GuestMessage', date_submitted = CURRENT_TIMESTAMP
                        WHERE id = $GuestID";

                if ( $dbh->exec($sql) )
                    {
                    exit("Your comment has been successfully updated. <a href='read.php'>View</a> all guestbook messages.");
                    }
                else
                    {
                    exit("Something went wrong. Please try again.");
                    }
                }
            else
                {
                exit("Invalid password. Try <a href='read.php'>again</a>.");
                }
            }
         unset($_POST);
         unset($dbh);
        }

    // New comment or Edit comment
    if ( !isset($option) && isset($_POST['GuestName']) && isset($_POST['GuestPass']) )
        {
        require "connect.php";

        $GuestName = addslashes(strip_tags(rtrim( $_POST['GuestName'] )));
        $GuestPass = addslashes(strip_tags(rtrim( $_POST['GuestPass'] . $salt . $pepper )));
        $GuestEmail = addslashes(strip_tags(rtrim( $_POST['GuestEmail'] )));
        $GuestMessage = addslashes(strip_tags(rtrim( $_POST['GuestMessage'] )));
        $GuestMessage = str_ireplace($unacceptable, "***", $GuestMessage);  // Remove unacceptable words

        $sql = "INSERT INTO guestbook (guest_name, guest_pass, guest_email, guest_message)
                VALUES ('$GuestName', PASSWORD('$GuestPass') ,'$GuestEmail', '$GuestMessage')";

        // Insert comment or bust
        if ( $stmt = $dbh->query($sql) )
            {
            exit("Thanks for leaving a comment. View the guestbook <a href='read.php'>here</a>.");
            }
        else
            {
            echo "There was an error adding your entry. Please try again.";
            }
        unset($_POST);
        unset($dbh);
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
    @$entry_id = intval($_GET['entry']);

    // Edit comment in guestbook
    if ( isset($option) && $option=='edit' && isset($entry_id) )
        {
        require "connect.php";

        if ( $stmt = $dbh->query("SELECT COUNT(*) FROM guestbook") )
            {
            $sql = "SELECT guest_name, guest_email, date_submitted
                    FROM guestbook
                    WHERE id=$entry_id";

            $stmt = $dbh->query($sql);
            $entry = $stmt->fetchAll();
            //echo '<pre>';
            //var_dump($entry);

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
    else
        // Post a new comment
        {
    ?>
        <form method='post' action='post.php'>
            Name: <input type='text' name='GuestName'><br>
            Pass: <input type='password' name='GuestPass'><br>
            Email: <input type='text' name='GuestEmail'><br><br>
            Message:<br><textarea rows='10' cols='40' name='GuestMessage'></textarea>
            <br><br>
            <input type='submit' value='Post comment'>
        </form>
    <?php } ?>
</body>
</html>
