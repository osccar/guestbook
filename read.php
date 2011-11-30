<?php
    mysql_connect("localhost", "admin", "AdminSQL");
    mysql_select_db("guestbook");

    $result = mysql_query("SELECT guest_name, guest_email, guest_message, date_submitted FROM guestbook ORDER BY date_submitted DESC;");
    if (mysql_num_rows($result))
        {
        while ($row = mysql_fetch_assoc($result))
            {
            extract($row, EXTR_PREFIX_ALL, 'gb');
            $gb_date_submitted = date("jS of F Y", $gb_date_submitted);
            echo "<strong>Posted by <a href='mailto:$gb_guest_email'>$gb_guest_name</a> on $gb_date_submitted</strong><br />";
            echo "$gb_guest_message<br /><br />";
            }
        }
    else
        {
        echo "<em>This guestbook has no messages!</em><br /><br />";
        }
?>

<a href="post.php">Add a message to this guestbook</a>
