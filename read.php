<!DOCTYPE html>
<html>
<head>
    <title>Guestbook entries</title>
    <style>
        ul{
            margin: 0 auto; 
            width: 395px;
            list-style-type: none;
        }
        li {
            display:inline;
        }
        
        li.selected {
            text-decoration: none;
            color: black;
            font-weight: bold;
            background-color: #c0c0c0;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h2>Guestbook entries</h2>
    <hr>

    <?php
    error_reporting(E_ALL);
    session_start();    // For paginated results tracking
    
    require "connect.php";
    //require "DB.class.php";
    require "Pager.class.php";

    //if ( $conn = new DB('test','root','AdminSQL') ){
    //    $conn->getInstance();
    //    exit;
    //}

    $page_url = htmlentities($_SERVER['PHP_SELF']); // avoid injection
    $limit = 5; // results per page
    $range_options = array('min' => 1, 'max' => $_SESSION['total_records']);
    
    // Get total records from DB
    if ( ! isset($_SESSION['total_records']) )
        {
        try {
            $rows = $dbh->query("SELECT COUNT(id) FROM guestbook");
            $_SESSION['total_records'] = $rows->fetchColumn();
            }
        catch (Exception $e) {
            $_SESSION['total_records'] = 0;
            }
        }
    else
        {
        // Check page number for pagination
        if ( ! filter_has_var(INPUT_GET, 'page') )
            {
            $page = 1;  // no page was set    
            }
        // Check page value within limits
        elseif ( ! filter_var($_GET['page'], FILTER_VALIDATE_INT, $range_options) )
            {
            $page = 1;  // validation was not possible
            }
        else
            {
            $page = (int)$_GET['page']; // sets the page
            }    
            
        // Build pagination links
        if ( $_SESSION['total_records'] == 0 )
            {
            print "<em>Guestbook seems to be empty! :-( <br>Wouldn't you like to be the first? Go ahead, click <a href='post.php'>here!</a>";
            //$content = 'No records available';
            }
        else
            {
            // Prepare pagination vars
            $pager = Pager::getPagerData($_SESSION['total_records'], $limit, $page);
            $offset = $pager->offset;
            $limit = $pager->limit;
            $page = $pager->page;
            $pagination = '<ul>';
            
            // If first page, no PREVIOUS link required
            if ( $page != 1 )
                {
                $pagination .= '<li><a href=' . $page_url . '?page=' . ($page - 1) . '>&lt;&lt; PREV </a></li>';
                }
            
            // Create pagination
            for ( $i=1; $i <= $pager->num_pages; $i++ )
                {
                if ( $i == $pager->page ) // current page
                    {
                    $pagination .= '<li class=selected>' . $i . '</li>';
                    }
                else
                    {
                    $pagination .= '<li><a href=' . $page_url . '?page=' . $i . '>' . $i . '</a></li>' . "\n";
                    }
                }
            
            // If last page, no NEXT link required
            if ( $page < $pager->num_pages )
                {
                $menu .= '<li><a href=' . $page_url . '?page=' . ($page + 1) . 'NEXT &gt;&gt;</a></li>';
                }
            
            $pagination .= '</ul>';
            
            // Retrieve records from DB
            //if ( $stmt = $dbh->query("SELECT COUNT(*) FROM guestbook") )
            //    {
                print "<h5>Total entries in guestbook: {$_SESSION['total_records']} </h5>";
                //$sql = "SELECT id, guest_name, guest_email, guest_message, date_submitted
                //        FROM guestbook
                //        ORDER BY date_submitted DESC";
                $sql = "SELECT id, guest_name, guest_email, guest_message, date_submitted
                        FROM guestbook
                        LIMIT $limit 
                        OFFSET $offset";
    
                print $pagination .'<br><br>';
    
                foreach ( $stmt = $dbh->query($sql) as $entry )
                    {
                    $date_submitted = date('F j, Y g:i a', strtotime($entry['date_submitted']));
                    printf("<strong>Posted by <a href='mailto:%s'>%s</a> on %s</strong><br>",
                        $entry['guest_email'],
                        $entry['guest_name'],
                        $date_submitted
                    );
                    print '<p><em>'. wordwrap($entry['guest_message'], 100, '<br>') .'</em></p>';
                    printf("%s | %s",
                        "<a href='post.php?op=edit&entry={$entry['id']}' title='Edit comment'>Edit</a>",
                        "<a href='post.php?op=del&entry={$entry['id']}' title='Delete comment'>Delete</a>"
                    );
                    print "<p>-------------------------------------------------------------------------------</p>";
                    }
                
                print "<br><a href='post.php'>Add new message</a>";
            //    }
            //else
            //    {
            //    print "<em>Guestbook seems to be empty! :-( <br>Wouldn't you like to be the first? Go ahead, click <a href='post.php'>here!</a>";
            //    }            
            }
        }

    $dbh = null;
    ?>
</body>
</html>