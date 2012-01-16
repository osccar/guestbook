-- MySQL

--
-- Table structure for table `guestbook`
--

CREATE TABLE IF NOT EXISTS `guestbook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guest_name` varchar(100) NOT NULL,
  `guest_pass` char(41) NOT NULL,
  `guest_email` varchar(100) NOT NULL,
  `guest_message` char(250) NOT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;