CREATE TABLE IF NOT EXISTS `prefix_river_atomid_mapping` (
  `river_id` int(11) NOT NULL,
  `atom_id` text NOT NULL,
  `provenance` text,
  PRIMARY KEY (`river_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `prefix_annotation_atomid_mapping` (
  `annotation_id` int(11) NOT NULL,
  `atom_id` text NOT NULL,
  PRIMARY KEY (`annotation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
