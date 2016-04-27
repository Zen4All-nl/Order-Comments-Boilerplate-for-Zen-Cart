--
-- Tabelstructuur voor tabel `order_comments`
--

DROP TABLE IF EXISTS `order_comments`;
CREATE TABLE IF NOT EXISTS `order_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `order_comments_content`
--

DROP TABLE IF EXISTS `order_comments_content`;
CREATE TABLE IF NOT EXISTS `order_comments_content` (
  `comment_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `comment_title` varchar(64) NOT NULL DEFAULT '',
  `comment_content` text,
  PRIMARY KEY (`comment_id`,`language_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;