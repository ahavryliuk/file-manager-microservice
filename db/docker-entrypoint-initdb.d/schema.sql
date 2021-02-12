CREATE DATABASE IF NOT EXISTS `fm_db`;

USE `fm_db`;

CREATE TABLE `file`
(
    `id`            integer AUTO_INCREMENT NOT NULL PRIMARY KEY,
    `external_id`   varchar(255)           NOT NULL,
    `token`         varchar(255)           NOT NULL,
    `name`          varchar(255)           NOT NULL,
    `resource_url`  varchar(255)           NOT NULL,
    `resource_meta` text                   NULL,
    `deleted`       boolean                NOT NULL DEFAULT false,
    `created_at`    datetime               NOT NULL DEFAULT current_timestamp()
);

INSERT INTO `file` VALUES (
                           1,
                           '81be7b67e5fbc5aed6676699a690a0b8746a5738',
                           '11b95e044a0acc22846b9c9acaf79826de392643',
                           '2ec32348fd17aa00edd0cc09d4046d17469e8924.jpg',
                           '/var/www/config/../storage/2ec32348fd17aa00edd0cc09d4046d17469e8924.jpg',
                           '{"client_file_name":"42825959927.jpg","client_file_size":33837,"client_file_media_type":"image\/jpeg","client_file_path":"\/tmp\/phpDbLekc"}',
                           0,
                           current_timestamp()
                           );