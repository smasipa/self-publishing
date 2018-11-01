CREATE TABLE folders(
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`created` INT(10) UNSIGNED NOT NULL,
	`modified` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE publications(
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`folder_id`	INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`user_id` INT(10) UNSIGNED NOT NULL,
	`title` VARCHAR(100),
	`text` TEXT,
	`created` INT(10) UNSIGNED NOT NULL,
	`modified` INT(10) UNSIGNED NOT NULL,
	`description` TINYTEXT,
	`price` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`accessibility` VARCHAR(50),
	`allow_user_comments` TINYINT UNSIGNED NOT NULL DEFAULT '1',
	`views` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `title` (`title`),
	KEY `folder_id` (`folder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE images(
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`item_type` VARCHAR(20) NOT NULL,
	`item_id` INT(10) UNSIGNED,
	`name` VARCHAR(128),
	`storage_location_id` TINYINT(4) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	KEY `item_type` (`item_type`,`item_id`),
	UNIQUE KEY `name` (`name`),
	KEY `storage_location_id` (`storage_location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE users(
	`id` INT(10) UNSIGNED AUTO_INCREMENT NOT NULL,
	`username` VARCHAR(50) NOT NULL,
	`first_name` VARCHAR(50),
	`last_name` VARCHAR(50) NOT NULL,
	`email` VARCHAR(128),
	`password` VARCHAR(200),
	`account_type` VARCHAR(15) NOT NULL DEFAULT 'standard',
	`activated` TINYINT(2) NOT NULL,
	`created` INT(10) NOT NULL,
	`expires` INT(10) NOT NULL,
	`expired` TINYINT(2) NOT NULL,
	`modified` INT(10) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `username` (`username`),
	KEY `first_name` (`first_name`),
	KEY `last_name` (`last_name`),
	KEY `email` (`email`),
	KEY `account_type` (`account_type`),
	KEY `expires` (`expires`),
	KEY `expired` (`expired`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE sellers_details(
	`id` INT(10) UNSIGNED AUTO_INCREMENT NOT NULL,
	`account_number` INT(30) UNSIGNED NOT NULL,
	`first_name` VARCHAR(50),
	`last_name` VARCHAR(50),
	`email` VARCHAR(128) NOT NULL,
	`phone_number` INT(10) UNSIGNED NOT NULL,
	`bank_name` VARCHAR(128),
	`created` INT(10) NOT NULL,
	`modified` INT(10) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE admins(
	`user_id` INT(10) UNSIGNED,
	`email` VARCHAR(128),
	`authority` TINYINT(2) NOT NULL DEFAULT '1',
	`created` INT(10) NOT NULL,
	`modified` INT(10),
	UNIQUE KEY `user_id` (`user_id`),
	UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE user_profiles(
	`id` INT(10) UNSIGNED AUTO_INCREMENT,
	`user_id` INT(10) UNSIGNED,
	`about` TEXT,
	`publications` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE documents(
	`id` INT(10) UNSIGNED AUTO_INCREMENT,
	`document_name` VARCHAR(50) NOT NULL,
	`document_type` VARCHAR(50) NOT NULL,
	`item_type` VARCHAR(20) NOT NULL,
	`item_id` INT(10) UNSIGNED NOT NULL,
	`size` INT(10) UNSIGNED NOT NULL,
	`storage_location_id` TINYINT(4) UNSIGNED NOT NULL,
	`price` INT(10) UNSIGNED,
	`accessibility` VARCHAR(20),
	`created` INT(10) UNSIGNED,
	`modified` INT(10) UNSIGNED,
	PRIMARY KEY (`id`),
	UNIQUE KEY `document_name` (`document_name`),
	KEY `document_type` (`document_type`),
	KEY `storage_location_id` (`storage_location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE file_storage_locations(
	`id` INT(10) UNSIGNED AUTO_INCREMENT,
	`location_name` VARCHAR(128) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE purchases(
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`user_account_type` VARCHAR(20) NOT NULL,
	`item_type` VARCHAR(20) NOT NULL,
	`item_id` INT(10) UNSIGNED NOT NULL,
	`payment_amount` INT(10) UNSIGNED  NOT NULL,
	`transaction_id` VARCHAR(60) NOT NULL,
	`created` INT(10) UNSIGNED NOT NULL,
	`status` VARCHAR(20) NOT NULL,
	PRIMARY KEY (`id`),
	KEY  `user_id` (`user_id`),
	UNIQUE KEY `transaction_id` (`transaction_id`),
	KEY `item_type` (`item_type`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
CREATE TABLE carts(
	`user_id` INT(10) UNSIGNED NOT NULL,
	`item_type` VARCHAR(20) NOT NULL,
	`item_id` INT(10) UNSIGNED NOT NULL,
	`created` INT(10) UNSIGNED NOT NULL,
	`modified` INT(10) UNSIGNED NOT NULL,
	KEY `item_type` (`item_type`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE comments(
	`id` INT(10) UNSIGNED AUTO_INCREMENT,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`item_type` VARCHAR(20) NOT NULL,
	`item_id` INT(10) UNSIGNED NOT NULL,
	`created` INT(10) UNSIGNED NOT NULL,
	`modified` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	KEY  `user_id` (`user_id`),
	KEY `item_type` (`item_type`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE tags(
	`id` INT(10) UNSIGNED AUTO_INCREMENT,
	`name` VARCHAR(128),
	`created` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY  `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE items_tags(
	`id` INT(10) UNSIGNED AUTO_INCREMENT,
	`tag_name` VARCHAR(128),
	`item_type` VARCHAR(20),
	`item_id` INT(11),
	`created` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	KEY  `tag_name` (`tag_name`),
	KEY `item_type` (`item_type`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE recently_viewed_items(
	`user_id` INT(10) UNSIGNED NOT NULL,
	`item_type` VARCHAR(20) NOT NULL,
	`item_id` INT(10) UNSIGNED NOT NULL,
	`created` INT(10) UNSIGNED NOT NULL,
	KEY `user_id` (`user_id`),
	KEY `item_type` (`item_type`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE favourites(
	`user_id` INT(10) UNSIGNED NOT NULL,
	`item_type` VARCHAR(20) NOT NULL,
	`item_id` INT(10) UNSIGNED NOT NULL,
	`created` INT(10) UNSIGNED NOT NULL,
	KEY `user_id` (`user_id`),
	KEY `item_type` (`item_type`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE reports(
	`id` INT(10) UNSIGNED AUTO_INCREMENT,
	`ip_address` VARCHAR(15) NOT NULL,
	`message` TEXT NOT NULL,
	`created` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE ratings(
	`user_id` INT(10) UNSIGNED NOT NULL,
	`item_type` VARCHAR(20) NOT NULL,
	`item_id` INT(10) UNSIGNED NOT NULL,
	`stars` TINYINT(2) UNSIGNED NOT NULL,
	`created` INT(10) UNSIGNED NOT NULL,
	`modified` INT(10) UNSIGNED NOT NULL,
	KEY `user_id` (`user_id`),
	KEY `item_type` (`item_type`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE user_activities(
	`user_id` INT(10) UNSIGNED NOT NULL,
	`item_type` VARCHAR(20) NOT NULL,
	`item_id` INT(10) UNSIGNED NOT NULL,
	`activity_type` VARCHAR(40) NOT NULL,
	`activity` TEXT,
	`created` INT(10) UNSIGNED NOT NULL,
	KEY `user_id` (`user_id`),
	KEY `activity_type` (`activity_type`),
	KEY `item_type` (`item_type`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE password_reset(
	`code` VARCHAR(10),
	`user_id` INT(10) UNSIGNED NOT NULL,
	`expired` TINYINT(2) UNSIGNED NOT NULL,
	`created` INT(10) UNSIGNED NOT NULL,
	KEY `user_id` (`user_id`),
	UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE users_feedback(
	`id` INT(10) UNSIGNED AUTO_INCREMENT,
	`ip_address` VARCHAR(15) NOT NULL,
	`feedback_type` VARCHAR(20) NOT NULL,
	`text` TEXT,
	`created` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	KEY (`ip_address`),
	KEY `feedback_type` (`feedback_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;