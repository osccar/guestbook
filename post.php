<?php
    /**
     * POST.PHP
     *
     * This file has two purposes:
     *      - post a new comment
     *      - edit a selected comment (GET var 'op' set to 'edit')
     *
     * TODO: some necessary extensions needed
     *      - basic filtering and sanitization have been supplied but they should revised/improved in order to make it more robust.
     *      - although form fields are 'required', they should still be checked in case the browser has no support for it
     *      - a password recovery via e-mail
     *
     */

    // FOR DEVELOPMENT ############
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', TRUE); // set to 'FALSE' for production
    // #############################

    $unacceptable = array('enter', 'desired', 'unacceptable', 'words', 'here');
    $pepper = 'guestbk';
    $salt = '%';
    $feedback_info = '';
    $maxchars = 250;
    $error_messages = array();

    // Sanitize and filter input vars (GET & POST)
    if ( filter_has_var(INPUT_GET, 'op') )
        $option = @filter_input( INPUT_GET, 'op', FILTER_SANITIZE_STRING );

    if ( filter_has_var(INPUT_GET, 'entry') )
        $comment_id = (int) @filter_input( INPUT_GET, 'entry', FILTER_SANITIZE_STRING );

    if ( @filter_has_var(INPUT_POST, 'GuestEmail') )
        // Use guest's email as his unique identifier
        $guest_id = @filter_input( INPUT_POST, 'GuestEmail', FILTER_SANITIZE_STRING );


    /**
     * Minimal requirements for all new comments:
     *      - all fields must be filled
     *      - all fields must be sanitized before inserting to DB
     */
    if ( isset($_POST) )
        {
        foreach ( $_POST as $field => $value )
            {
            // Min. is 1 character
            if ( isset($field) && strlen(trim($value)) < 1 )
                {
                $error_messages[] = 'Please fill in all fields.';
                break;
                }
            // Minimal email validation
            if ( $field === 'GuestEmail' )
                if ( filter_var($value, FILTER_VALIDATE_EMAIL) === FALSE )
                    {
                    $error_messages[] = 'Email address is invalid.';
                    break;
                    }

            // Max. characters for comments, add ellipsis to end
            if ( $field === 'GuestMessage' )
                if ( strlen($value) > $maxchars ) {
                    $_POST['GuestMessage'] = substr($value, 0, $maxchars);
                    $_POST['GuestMessage'] = substr_replace($_POST['GuestMessage'], '...', -4);
                }
            }
        extract($_POST);
        }


    /**
     * TODO: extra filtering should be added for robustness
     *
     * - for example, limit comments to # characters some other way
     *
     * Add here, below...
     *
     */



    /** PROCESS COMMENTS ***********************************/

    // Insert new comment to DB
    if ( !isset($option) && !empty($GuestName) && !empty($GuestPass) && !empty($GuestEmail) && !empty($GuestMessage) )
        {
        require "connect.php";

        // Clean input
        $GuestName = addslashes(strip_tags(trim( $_POST['GuestName'] )));
        $GuestPass = addslashes(strip_tags(trim( $_POST['GuestPass'] . $salt . $pepper )));
        $GuestEmail = addslashes(strip_tags(trim( $_POST['GuestEmail'] )));
        $GuestMessage = addslashes(strip_tags(trim( $_POST['GuestMessage'] )));

        $sql = "INSERT INTO guestbook (guest_name, guest_pass, guest_email, guest_message)
                VALUES ('$GuestName', PASSWORD('$GuestPass') ,'$GuestEmail', '$GuestMessage')";

        // Insert comment or bust!
        if ( $stmt = $dbh->query($sql) )
            $feedback_info = "<span class='ok'>Thank you for leaving a comment.</span> View messages <a href='read.php'>here</a>.";
        else
            $feedback_info = "<span class='nok'>Sorry but your comment couldn't be added.</span> Please try again.";

        unset($_POST);
        unset($dbh);
        }


    // Insert edited comment into DB
    if ( isset($guest_id) && @$option==='edit' && (count($error_messages) == 0) )
        {
        require 'connect.php';

        $GuestPass = addslashes(strip_tags(trim( $_POST['GuestPass'] . $salt . $pepper )));
        $GuestEmail = addslashes(strip_tags(trim( $_POST['GuestEmail'] )));
        $GuestMessage = addslashes(strip_tags(trim( $_POST['GuestMessage'] )));

        $select_query = trim("
                        SELECT COUNT(id)
                        FROM guestbook
                        WHERE guest_pass = PASSWORD('$GuestPass') AND guest_email = '$GuestEmail'
        ");

        // Verify password exists
        if ( $stmt = $dbh->query($select_query) )
            {
            if ( $stmt->fetchColumn() )
                {
                $update_query = trim("
                        UPDATE guestbook
                        SET guest_message = '$GuestMessage', date_submitted = CURRENT_TIMESTAMP
                        WHERE guest_email = '$guest_id'
                ");

                if ( $dbh->exec($update_query) )
                    $feedback_info = "<p><span class='ok'>Your comment has been successfully updated.</span> View all messages <a href='read.php'>here</a>.</p>";
                else
                    $feedback_info = "<p><span class='nok'>Sorry! Your comment couldn't be edited.</span> Please try again.</p>";
                }
            else
                $error_messages[] = "<span class='nok'>Invalid password.</span>";
            }
        unset($dbh);
        }
?>

<!DOCTYPE html>
<html lang=en>
<head>
    <meta charset=utf-8>
    <title>Post comments in Guestbook</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Comment wisely!</h2>
    <br>
    <?php
    // Edit previous comment
    if ( isset($option) && $option==='edit' && isset($comment_id) )
        {
        require "connect.php";
        if ( $stmt = $dbh->query("SELECT COUNT(*) FROM guestbook") )
            {
            $sql = "SELECT guest_name, guest_email, guest_message, date_submitted
                    FROM guestbook
                    WHERE id = $comment_id";

            $stmt = $dbh->query($sql);
            $entry = $stmt->fetchAll();
            $date_submitted = date("g:i a -- F j, Y", strtotime($entry[0]['date_submitted']));
            print "<div id=comments-form>
                    <form method=post action=post.php?op=edit>
                        <p>Previously submitted at $date_submitted</p>
                        <br>
                        <p>Name</p>
                        <p><input disabled type=text name=GuestName value='{$entry[0]['guest_name']}'></p>
                        <p>Password</p>
                        <p><input required type=password name=GuestPass placeholder='Initial password'></p>
                        <p>Email</p>
                        <p><input disabled type=email name=GuestEmail value={$entry[0]['guest_email']}></p>
                        <p>Comment (250 characters max.)</p>
                        <p>
                            <textarea autofocus=true rows=10 cols=60 name=GuestMessage>{$entry[0]['guest_message']}</textarea>
                        </p>
                        <br>
                        <input type=hidden name=GuestName value='{$entry[0]['guest_name']}'>
                        <input type=hidden name=GuestEmail value={$entry[0]['guest_email']}>
                        <input type=submit class='button medium cyan' value='So let it be RE-written!'>
                    </form>
                    </div>
                    <p class='gb-pagination'>Or <a href='read.php'>return</a> to comments...</p>";
            // NOTE: hidden input fields are needed since 'disabled' input form fields are not sent with the form
            }
        }
    // Alert errors
    elseif ( count($error_messages) )
        {
        foreach ( $error_messages as $message ) 
            {
            print "<div class=feedback-info>$message</div>";            
            }
        print "<p class='gb-pagination'>Try <a href='read.php'><span>again</span></a></p>";
        }
    // New comment form
    else
        {
        if ( !empty($feedback_info) )
            {
            print "<div class=feedback-info>$feedback_info</div>";
            unset($_POST);  // clean last user input
            }
    ?>
        <div id=comments-form>
            <form method=post action=post.php>
                <p>Name</p>
                <p><input required type=text name=GuestName placeholder='John Doe'></p>
                <p>Password</p>
                <p><input required type=password name=GuestPass placeholder='Make it good!'></p>
                <p>Email</p>
                <p><input required type=email name=GuestEmail placeholder=name@domain.com></p>
                <p>Comment (250 characters max.)</p>
                <p><textarea autofocus=true rows=10 cols=60 name=GuestMessage></textarea></p>
                <br>
                <input type=submit class='button cyan' value='So let it be written!'>
            </form>
        </div>
    <?php } ?>

</body>
</html>
