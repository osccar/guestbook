-- MySQL table create
CREATE TABLE guestbook (
   ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
   GuestName CHAR(255),
   GuestEmail CHAR(255),
   GuestMessage TEXT,
   DateSubmitted INT
);

-- Sqlite table create
CREATE TABLE "main"."guestbook" (
   "id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL,
   "guest_name" CHAR(255) NOT NULL,
   "guest_email" CHAR(255) NOT NULL,
   "guest_message" TEXT NOT NULL,
   "date_submitted" NOT NULL DEFAULT CURRENT_DATE
   );
