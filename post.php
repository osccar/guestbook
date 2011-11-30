<?php
    if (isset($_POST['guest_name']))
        {
        mysql_connect("localhost", "admin", "adminSQL");
        mysql_select_db("guestbook");
        // Remove unacceptable words before submitting data to database
        $GuestName = str_ireplace("dog", "***", $_POST['guest_name']);
        $GuestEmail = str_ireplace("dog", "***", $_POST['guest_email']);
        $GuestMessage = str_ireplace("dog", "***", $_POST['guest_message']);
        // Remove HTML tags
        $GuestName = strip_tags($GuestName);
        $GuestEmail = strip_tags($GuestEmail);
        $GuestMessage = strip_tags($GuestMessage);
        // Sanitize text
        $GuestName = addslashes($GuestName);
        $GuestName = addslashes($_POST['guest_name']);
        $GuestEmail = addslashes($_POST['guest_email']);
        $GuestMessage = addslashes($_POST['guest_message']);
        $CurrentTime = time();

        $result = mysql_query("
                INSERT INTO guestbook (guest_name, guest_email, guest_message, date_submitted)
                VALUES ('$GuestName', '$GuestEmail', '$GuestMessage', $CurrentTime)
        ");
        if ($result)
            {
            exit("Thanks for posting - click <a href='read.php'>here</a> to view the guestbook with your message added!");
            }
        else
            {
            echo "There was an error adding your guestbook entry - please try again, filling in all fields.";
            }
        }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Post comments in Guestbook</title>
</head>
<body>
    <form method="post" action="post.php">
      Name: <input type="text" name="GuestName"><br>
      Email: <input type="text" name="GuestEmail"><br><br>
      Message:<br><textarea rows="10" cols="40" name="GuestMessage"></textarea>
      <br>
        <br>
      <input type="submit" value="Post" />
    </form>
</body>
</html>
