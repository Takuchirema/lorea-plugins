CREATE TABLE IF NOT EXISTS `prefix_river_atomid_mapping` (
  `river_id` int(11) NOT NULL,
  `atom_id` text NOT NULL,
  PRIMARY KEY (`river_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
