-- Migration: initial database setup
-- Run this to recreate the database from scratch

DROP DATABASE IF EXISTS `vk-humiliation-bot`;
CREATE DATABASE `vk-humiliation-bot`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `vk-humiliation-bot`;

CREATE TABLE `users` (
    `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`         BIGINT          NOT NULL UNIQUE,
    `name`            VARCHAR(255)    NOT NULL DEFAULT 'Олег',
    `lastname`        VARCHAR(255)    NOT NULL DEFAULT 'Безымянный',
    `prev_message_id` VARCHAR(255)    NOT NULL DEFAULT '',
    `forced_left`     TINYINT         NOT NULL DEFAULT 3,
    `isSubscribed`    TINYINT(1)      NOT NULL DEFAULT 0,
    `user_info`       TEXT            NULL,
    `aliasName`       VARCHAR(255)    NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
