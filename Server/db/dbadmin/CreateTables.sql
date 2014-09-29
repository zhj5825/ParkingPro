CREATE TABLE IF NOT EXISTS `user_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UserName` varchar(255) NOT NULL default '', 
  `Email` varchar(255) default '', 
  `Password` varchar(256) NOT NULL default '',
  `RoleType` TINYINT default 0,
  `Active` BIT default 1, 

  `FirstName` varchar(255) default NULL, 
  `LastName` varchar(255) default NULL, 

  `CreatedTime` INTEGER UNSIGNED default 0, # UNIX time
  `ModifiedTime` INTEGER UNSIGNED default 0, 
  `LastLoginTime` INTEGER UNSIGNED default 0, 

  `HomeAddress` varchar(255) default NULL, 
  `HomeCity` varchar(255) default NULL, 
  `HomeState` varchar(255) default NULL, 
  `HomeCountry` varchar(255) default NULL, 
  `HomeZipcode` INTEGER UNSIGNED default NULL, 
   
  `phone` char(15) default NULL, 

  PRIMARY KEY (`id`)
 ) 
 COMMENT='UserAccounts'
 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
