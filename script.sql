CREATE DATABASE lpiv_queue_processing;
use lpiv_queue_processing;

CREATE TABLE jobs(
	`id` INTEGER AUTO_INCREMENT NOT NULL,
	`payload` TEXT NOT NULL,
	`created_at` DATETIME NOT NULL,
	`updated_at` DATETIME NOT NULL,
	CONSTRAINT `pk_jobs` PRIMARY KEY (`id`)
);

CREATE TABLE subscribers(
	`id` INTEGER AUTO_INCREMENT NOT NULL,
	`email` VARCHAR(50) NOT NULL,
	`name` VARCHAR(50) NULL,
	`phone` VARCHAR(50) NULL,
	`taginternals` TEXT NULL,
	`created_at` DATETIME NOT NULL,
	`updated_at` DATETIME NOT NULL,
	CONSTRAINT `pk_subscribers` PRIMARY KEY (`id`)
);