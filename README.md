# Simple CTF Host
A very simple hosting system for presenting a small CTF event. Originally created for an introductory CTF for Nassau University, LI, NY. Provides quick installation and some neat features such as:

 - Accounts and progress tracking
 - Challenge scoring system
 - Leaderboard
 - Web administration utilities
 - Ability to easily add, modify, delete challenges
 - Ability to allow files to be added to challenges for users to download

## Creating the Database Tables
```
CREATE TABLE IF NOT EXISTS `accounts` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
  	`username` varchar(50) NOT NULL,
  	`password_hash` varchar(255) NOT NULL,
  	`registration_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	`last_logon` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`is_admin` BIT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `challenges` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
  	`name` TINYTEXT NOT NULL,
	`text` TEXT(65535) NOT NULL,
	`category` TINYTEXT NOT NULL,
	`subcategory` TINYTEXT NOT NULL,
	`difficulty` int(11) NOT NULL,
	`flag` TINYTEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `challenge_files` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`challenge_id` int(11) NOT NULL,
	`location` TINYTEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `solves` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`challenge_id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`solve_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
```

# Promote a User to Administrator
```
UPDATE accounts SET is_admin=1 WHERE id=2;
```