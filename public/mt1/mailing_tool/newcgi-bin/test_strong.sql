DROP TABLE IF EXISTS `test_strongmail`;
CREATE TABLE `test_strongmail` (
  `test_id` int(11) NOT NULL auto_increment,
  `servID` int(11) NOT NULL default 0,
  `creative_id` int(11) NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `url` varchar(80) NOT NULL default '',
  `submit_datetime` datetime default NULL,
  `brand_id` int(11) NOT NULL default '0',
  `header_tag` varchar(50) default NULL,
  vsgID varchar(35) not null,
   test_type enum('DELIVERY','COMPLIANCE','EMAILREACH','CAMPAIGN'),
  `email_addr` longblob,
  campaign_id int(11) unsigned not null default 0,
  PRIMARY KEY  (`test_id`),
  KEY `test_strongmail_ind1` (`servID`)
) type=INNODB; 
