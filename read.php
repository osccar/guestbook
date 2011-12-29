
<!DOCTYPE html>
<html>
<head>
    <title>Guestbook entries</title>
</head>
<body>
    <h2>Guestbook entries</h2>
    <hr>

    <?php
    require "connect.php";

    if ( $stmt = $dbh->query("SELECT COUNT(*) FROM guestbook") )
        {
        print "<h5>Total entries in guestbook: ". $stmt->fetchColumn() ."</h5>";

        $sql = "SELECT id, guest_name, guest_email, guest_message, date_submitted
                FROM guestbook
                ORDER BY date_submitted DESC";

        foreach ( $stmt = $dbh->query($sql) as $entry )
            {
            $date_submitted = date("F j, Y g:i a", strtotime($entry["date_submitted"]));
            printf("
                <strong>Posted by <a href='mailto:%s'>%s</a> on %s</strong><br>",
                $entry["guest_email"],
                $entry["guest_name"],
                $date_submitted
            );
            print "<p><em>".wordwrap($entry["guest_message"])."</em></p>";
            printf("%s | %s",
                "<a href='post.php?op=edit&entry={$entry['id']}' title='Edit comment'>Edit</a>",
                "<a href='post.php?op=del&entry={$entry['id']}' title='Delete comment'>Delete</a>"
            );
            print "<p>-------------------------------------------------------------------------------</p>";
            }

        print "<br><a href='post.php'>Add new message</a>";
        }
    else
        {
        print "<em>Guestbook seems to be empty! :-( <br>Wouldn't you like to be the first? Go ahead, click <a href='post.php'>here!</a>";
        }
    $dbh = null;
    ?>
</body>
</html>