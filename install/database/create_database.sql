--
-- Database: `ota`
--

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

CREATE TABLE IF NOT EXISTS `application` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_bin NOT NULL,
  `icon_file` varchar(30) COLLATE utf8_bin NOT NULL,
  `version` varchar(30) COLLATE utf8_bin NOT NULL,
  `date_upload` date NOT NULL,
  `id_developer` varchar(30) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `developer`
--

CREATE TABLE IF NOT EXISTS `developer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(40) COLLATE utf8_bin NOT NULL,
  `password` varchar(40) COLLATE utf8_bin NOT NULL,
  `first_name` varchar(40) COLLATE utf8_bin NOT NULL,
  `last_name` varchar(40) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `invite`
--

CREATE TABLE IF NOT EXISTS `invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(30) COLLATE utf8_bin NOT NULL,
  `token` varchar(30) COLLATE utf8_bin NOT NULL,
  `status` enum('sent','udid','app') COLLATE utf8_bin NOT NULL,
  `date_sent` date NOT NULL,
  `id_developer` int(11) NOT NULL,
  `id_application` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `testing_device`
--

CREATE TABLE IF NOT EXISTS `testing_device` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(30) COLLATE utf8_bin NOT NULL,
  `udid` varchar(30) COLLATE utf8_bin NOT NULL,
  `date_creation` date NOT NULL,
  `id_developer` int(30) NOT NULL,
  `id_invite` int(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `testing_device_application`
--

CREATE TABLE IF NOT EXISTS `testing_device_application` (
  `id_testing_device` int(11) NOT NULL,
  `id_application` int(11) NOT NULL,
  `date_install` date NOT NULL,
  PRIMARY KEY (`id_testing_device`,`id_application`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
