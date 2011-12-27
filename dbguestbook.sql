-- MySQL table create
CREATE TABLE guestbook (
   id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
   guest_name CHAR(50),
   guest_email CHAR(100),
   guest_message TEXT,
   date_submitted INT
);

-- Sqlite3 table create
CREATE TABLE "main"."guestbook" (
   "id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL,
   "guest_name" CHAR(50) NOT NULL,
   "guest_email" CHAR(100) NOT NULL,
   "guest_message" TEXT NOT NULL,
   "date_submitted" NOT NULL DEFAULT CURRENT_DATE
   );
