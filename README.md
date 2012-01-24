## Guestbook comments application
- Keeps history of user comments
- Comments are stored in MySQL database
- Allows editing and deleting comments (password protected)

---

## Design

    The whole application is done with HTML5 and CSS3

    **NOTE**
    Visual aesthetics was not the priority here, so feel free to redesign for your personal pleasure!

## Core

    I decided to make this application just using server side language and keep it as clean and simple
    as possible without getting javascript in the mix.

    - PHP 5.3
    - HTML5
    - CSS3

## Extra functionality

    - None at this point
    - No JavaScript has been used in this version.

---

### Instructions
    1. Download all content to a new folder in the publicly accessible folder of the web server (eg: /var/www or /lampp/htdocs)
    2. Create new database or use an existing one and use 'guestbook.sql' to create the table
    3. Edit 'connect.php' and insert correct credentials to access the database
    4. Insert list of unaccepted words in user comments in the $unacceptable array (file: read.php)
    4. Open 'read.php' and start using
    5. Happy commenting!