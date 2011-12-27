<?php
    if ( isset($_POST['GuestName']) )
        {
        require "connect.php";

        // VARS
        $unacceptable = array('fuu', 'ass', 'shii');

        // Remove unacceptable words before submitting data to database
        $GuestMessage = str_ireplace($unacceptable, "***", $_POST['GuestMessage']);

        // Remove HTML tags and sanitize
        $GuestName = addslashes(strip_tags(rtrim($_POST['GuestName'])));
        $GuestEmail = addslashes(strip_tags(rtrim($_POST['GuestEmail'])));
        $GuestMessage = addslashes(strip_tags(rtrim($GuestMessage)));

        $sql = "INSERT INTO guestbook (guest_name, guest_email, guest_message)
                VALUES ('$GuestName', '$GuestEmail', '$GuestMessage')";

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

    <form method="post" action="post.php">
        Name: <input type="text" name="GuestName"><br>
        Email: <input type="text" name="GuestEmail"><br><br>
        Message:<br><textarea rows="10" cols="40" name="GuestMessage"></textarea>
        <br><br>
        <input type="submit" value="post" />
    </form>
</body>
</html>
