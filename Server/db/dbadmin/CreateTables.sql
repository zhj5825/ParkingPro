CREATE TABLE IF NOT EXISTS `user_accounts` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
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

CREATE TABLE IF NOT EXISTS `credit_cards` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UserId` int(10) unsigned NOT NULL,
  `CreditCardNum` varchar(20) NOT NULL,
  `CreditCardExpMonth` char(2) NOT NULL,
  `CreditCardExpYear` char(2) NOT NULL,
  `CreditCardAddress` varchar(255) NOT NULL,
  `CreditCardCity` varchar(255) NOT NULL,
  `CreditCardState` varchar(255) NOT NULL,
  `CreditCardCountry` varchar(255) NOT NULL,
  `CreditCardZipcode` varchar(10) NOT NULL,
  `CreditCardName` varchar(10) NOT NULL,
  `CreditCardSecurityCode` varchar(10) NOT NULL,
  PRIMARY KEY (`ID`)
 ) 
 COMMENT='CreditCards'
 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
