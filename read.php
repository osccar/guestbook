<?php
    /**
     * READ
     *
     * This file has two purposes:
     *      - read/show comments (using pagination)
     *      - delete a selected comment
     *
     *  TODO: it has basic input filtering and sanitization but some further filtering should be done
     *      in order to make it much more robust.
     *
     */

    // JUST FOR DEVELOPMENT ############
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', TRUE); // set to 'FALSE' for production
    // #################################

    session_start();
    require "connect.php";
    require "Pager.class.php";

    $page_url     = htmlentities($_SERVER['PHP_SELF']); // avoids injection
    $limit        = 4; // results per page
    $deleted      = '';
    $messages     = array();
    $unacceptable = array('fuck', 'ass', 'shit');

    // Sanitize and filter GET vars
    if ( filter_has_var(INPUT_GET, 'entry') ) {
        $entry_id = (int) @filter_input( INPUT_GET, 'entry', FILTER_SANITIZE_STRING );        
    }

    if ( filter_has_var(INPUT_GET, 'op') ) {
        $option = @filter_input( INPUT_GET, 'op', FILTER_SANITIZE_STRING );        
    }

    /**
     * Basic requirements for all comments:
     *
     *  - all text from comment field must be checked for unacceptable words! (line 147)
     *
     */
    
    /*
        TODO extra filtering can be added if needed (not required)
    */
    
    // Delete comment
    if ( isset($option) && $option==='del' && isset($entry_id) )
        {
        $sql = "DELETE FROM guestbook WHERE id = $entry_id";

        if ( $dbh->exec($sql) )
            {
            // update total comments count
            $rows = $dbh->query("SELECT COUNT(id) FROM guestbook");
            $_SESSION['total_records'] = $rows->fetchColumn();

            $messages[] = "<p>Your comment has been deleted.</p>";
            }
        else 
            {
            $messages[] = "<p>Sorry. Your comment could not be deleted</p>";
            }
        
        }
?>

<!DOCTYPE html>
<html lang=en>
<head>
    <meta charset=utf-8>
    <title>Simple Guestbook</title>
    <!--[if gte IE 9]>
        <style type="text/css">
            .gradient { filter: none; }
        </style>
    <![endif]-->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Simple Guestbook</h2>

    <?php

    // Get total records from database
    if ( !isset($_SESSION['total_records']) )
        {
        try {
            $rows = $dbh->query("SELECT COUNT(id) FROM guestbook");
            $_SESSION['total_records'] = $rows->fetchColumn();
            }
        catch (Exception $e) 
            {
            $_SESSION['total_records'] = 0;
            // Empty Guestbook !!
            $messages[] = "<em>Guestbook seems to be empty! :-( <br>Wouldn't you like to be the first?";
            }
        }

    print "<h5>{$_SESSION['total_records']} comments in the guestbook</h5>";
    print "<p id=new><a class='green' href='post.php'>New comment</a></p>";

    // Show feedback to user, if any
    if ( count($messages) )
        {
        foreach ( $messages as $message ) {
            print "<div class=feedback-info>$message</div>";
        }
        print "<p>Return to <a href='read.php'><span>comments</span></a></p>";
        }
    else
        {
        /** PAGINATION *****************************/
        
        $range_options = array('min' => 1, 'max' => $_SESSION['total_records']);

        // Check page values before seting up new pagination links
        if ( !filter_has_var(INPUT_GET, 'page') ) 
            {
            // Check page number for pagination
            $page = 1;  // no page was set            
            }
        elseif ( !filter_var($_GET['page'], FILTER_VALIDATE_INT, $range_options) )
            {
            // Check page value within limits
            $page = 1;  // validation was not possible
            }
        else
            {
            $page = (int) $_GET['page']; // sets the page
            }
            
        // Pagination setup. Fetch total amount records
        try {
            $rows = $dbh->query("SELECT COUNT(id) FROM guestbook");
            $_SESSION['total_records'] = $rows->fetchColumn();
            } 
        catch (Exception $e) 
            {
            $messages[] = "Error. Could not retrieve total count of records.";
            }

        $pager  = Pager::getPagerData($_SESSION['total_records'], $limit, $page);
        $offset = $pager->offset;
        $limit  = $pager->limit;
        $page   = $pager->page;

        // Pagination markup
        
        $pagination = "<div id='gb-pagination'><ul id=pager>";

        // If first page, no PREVIOUS link required
        if ( $page != 1 ) 
            {
            $pagination .= '<li><a href=' . $page_url . '?page=' . ($page - 1) . '>&lt;&lt;</a></li>';
            }
        
        // Build pagination links
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
            $pagination .= '<li><a href=' . $page_url . '?page=' . ($page + 1) . '>&gt;&gt;</a></li>';
            }
        $pagination .= '</ul></div>';
        
        /****************************************/

        /** COMMENTS ****************************/
        
        // Show comments, if any
        if ( $_SESSION['total_records'] )
            {
            $sql = "SELECT id, guest_name, guest_email, guest_message, date_submitted
                    FROM guestbook
                    ORDER BY date_submitted DESC
                    LIMIT $limit
                    OFFSET $offset";

            print "<div id=gb-comments>";
            foreach ( $stmt = $dbh->query($sql) as $entry )
                {
                // Remove unacceptable words
                $guest_message = str_ireplace($unacceptable, "***", $entry['guest_message']);

                // Format date/time output
                $date_submitted = date('g:i a -- F j, Y', strtotime($entry['date_submitted']));

                $record = '<article class="gb-entry gradient">';
                //$record .= sprintf("<h4><a class=guest-name href='mailto:%s'>%s</a></h4>", $entry['guest_email'], $entry['guest_name']);
                $record .= sprintf("<p id=guest-name>%s</p>", $entry['guest_name']);
                $record .= '<p><em>'. $guest_message .'</em></p>';
                $record .= '<p id=date-submit><span>'. $date_submitted .'</span></p>';
                $record .= sprintf("
                    <div class=edit-del-btns>%s %s</div>",
                    "<a class='button cyan' href='post.php?op=edit&entry={$entry['id']}'><span>Edit</span></a>",
                    "<a class='button red' href='read.php?op=del&entry={$entry['id']}'><span>Delete</span></a>"
                    );
                $record .= "</article>\n";
                print $record;
                }
            print "</div>";

            // Only show pagination in case of minimum number of comments
            if ( $_SESSION['total_records'] > $limit )
                {
                print $pagination;
                }
            }
        }

    // reset feedback messages
    $messages = '';

    // Close connection
    $dbh = NULL;
    ?>
</body>
</html>