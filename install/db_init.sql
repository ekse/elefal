-- Base de données: `elefal`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `elefal_books`
-- 

CREATE TABLE `elefal_books` (
  `bookID` bigint(20) NOT NULL auto_increment,
  `title` tinytext collate utf8_bin,
  `author` tinytext collate utf8_bin,
  `bookYear` int(11) default NULL,
  `sellerKey` varchar(20) collate utf8_bin NOT NULL default '',
  `status` enum('lost','instock','expected','sold','returned','notset') collate utf8_bin NOT NULL default 'notset',
  `lastUpdate` datetime default NULL,
  `price` float default NULL,
  PRIMARY KEY  (`bookID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `elefal_sellers`
-- 

CREATE TABLE `elefal_sellers` (
  `sellerKey` varchar(20) collate utf8_bin NOT NULL default '',
  `firstName` tinytext collate utf8_bin NOT NULL,
  `lastName` tinytext collate utf8_bin NOT NULL,
  `email` tinytext collate utf8_bin NOT NULL,
  `phone` tinytext collate utf8_bin NOT NULL,
  PRIMARY KEY  (`sellerKey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
