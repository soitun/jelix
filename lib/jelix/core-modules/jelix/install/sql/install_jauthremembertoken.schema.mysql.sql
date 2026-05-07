
CREATE TABLE IF NOT EXISTS `%%PREFIX%%jauthremembertoken`
(
  `token_hash` varchar(128) NOT NULL,
  `series_hash` varchar(128) NOT NULL,
  `login` varchar(255) NOT NULL,
  `expires_at` integer NOT NULL default 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY  (`login`,`token_hash`),
  INDEX (`login`, `series_hash`)
) DEFAULT CHARSET=utf8;
