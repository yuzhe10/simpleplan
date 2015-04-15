CREATE TABLE `member` (
   `UID` bigint(20) unsigned not null auto_increment,
   `RID` bigint(20) unsigned not null default 0,
   `UserName` varchar(20) default 'unnamed',
   `Password` varchar(40) not null default ' ',
   `Email` varchar(60) not null,
   `MSISDN` varchar(20) default '',
   `RegDate` int(10) not null default '0',
   `RegIP` varchar(15) default '',
   `LastIP` varchar(15) default '',
   `LastVisit` int(10) not null default '0',
   PRIMARY KEY (`UID`),
   UNIQUE KEY (`Email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `plan` (
    `PID` bigint(20) unsigned not null auto_increment,
    `UID` bigint(20) unsigned not null,
    `CID` bigint(20) unsigned not null,
    `Order` int unsigned default 9999,
    `Start` int(10) default 0,
    `Finish` int(10) default 0,
    `Spend` int default 0,
    `ETA` int(10) default 0, 
    `Status` tinyint default 0,
    `Done` tinyint default 0,
    `Recycle` tinyint default 0,
    `Title` text not null,
    `Note` text null,
    PRIMARY KEY (`PID`,`UID`,`CID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `category` (
    `CID` bigint(20) unsigned not null auto_increment,
    `UID` bigint(20) unsigned not null,
    `Name` varchar(100) not null default '',
    PRIMARY KEY (`CID`,`UID`),
    UNIQUE KEY (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `role` (
    `RID` bigint(20) unsigned not null auto_increment,
    `Privilege` bigint unsigned not null default 0,
    `Name` varchar(100) not null default '',
    PRIMARY KEY (`RID`),
    UNIQUE KEY (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `action` (
    `AID` bigint(20) unsigned not null auto_increment,
    `Number` int not null default 0,
    `Name` varchar(100) not null default '',
    PRIMARY KEY (`AID`),
    UNIQUE KEY (`Number`,`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `recurrence` (
   `RID` bigint(20) not null auto_increment,
   `PID` bigint(20) not null,
   `RepeatType` int(11) not null default '0',
   `SelectDays` int(11) not null default '0',
   `SelectWeeks` int(11) not null default '0',
   `Weekday1` int(2) not null default '0',
   `Weekday2` int(2) not null default '0',
   `Weekday3` int(2) not null default '0',
   `Weekday4` int(2) not null default '0',
   `Weekday5` int(2) not null default '0',
   `Weekday6` int(2) not null default '0',
   `Weekday7` int(2) not null default '0',
   `SelectMonths` int(11) not null default '0',
   `MonthByMonthDay` int(11) not null default '0',
   `MonthByDay` int(11) not null default '0',
   `SelectYears` int(11) not null default '0',
   `OriginalDate` int(10) not null default '0',
   PRIMARY KEY (`RID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;