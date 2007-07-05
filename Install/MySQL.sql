SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE {players} ADD `comment` TEXT;
ALTER TABLE {accounts} ADD `signature` TEXT;
ALTER TABLE {accounts} ADD `avatar` VARCHAR(255);
ALTER TABLE {accounts} ADD `website` VARCHAR(255);

DROP TABLE IF EXISTS [settings];

CREATE TABLE [settings] (
    `name` VARCHAR(255),
    `content` TEXT,
    UNIQUE KEY (`name`)
);

DROP TABLE IF EXISTS [online];

CREATE TABLE [online] (
    `id` SERIAL,
    `name` VARCHAR(255),
    `content` VARCHAR(255),
    `port` INT,
    `maximum` INT DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`content`, `port`)
);

DROP TABLE IF EXISTS [links];

CREATE TABLE [links] (
    `id` SERIAL,
    `name` VARCHAR(255),
    `content` VARCHAR(255),
    PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS [access];

CREATE TABLE [access] (
    `id` SERIAL,
    `name` VARCHAR(255),
    `content` INTEGER,
    PRIMARY KEY (`id`),
    UNIQUE (`name`)
);

DROP TABLE IF EXISTS [pms];

CREATE TABLE [pms] (
    `id` SERIAL,
    `name` VARCHAR(255),
    `content` TEXT,
    `from` INT NOT NULL,
    `to` INT NOT NULL,
    `read` INT DEFAULT 0,
    `date_time` INT UNSIGNED,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`from`) REFERENCES {players} (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`to`) REFERENCES {players} (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

DROP TABLE IF EXISTS [logs];

CREATE TABLE [logs] (
    `id` SERIAL,
    `name` VARCHAR(255),
    `content` INT UNSIGNED,
    `date_time` INT UNSIGNED,
    PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS [download];

CREATE TABLE [download] (
    `id` SERIAL,
    `name` VARCHAR(255),
    `content` TEXT,
    `binary` TINYINT,
    `file` BLOB,
    PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS [gallery];

CREATE TABLE [gallery] (
    `id` SERIAL,
    `name` VARCHAR(255),
    `content` TEXT,
    `binary` TINYINT,
    `file` BLOB,
    PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS [polls];

CREATE TABLE [polls] (
    `id` SERIAL,
    `name` VARCHAR(255),
    `content` TEXT,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

DROP TABLE IF EXISTS [options];

CREATE TABLE [options] (
    `id` SERIAL,
    `name` VARCHAR(255),
    `poll` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`poll`) REFERENCES [polls] (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

DROP TABLE IF EXISTS [votes];

CREATE TABLE [votes] (
    `name` BIGINT UNSIGNED NOT NULL,
    `content` INT NOT NULL,
    FOREIGN KEY (`name`) REFERENCES [options] (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`content`) REFERENCES {accounts} (`id`) ON DELETE CASCADE,
    UNIQUE KEY (`name`, `content`)
) ENGINE = InnoDB;

DROP TABLE IF EXISTS [boards];

CREATE TABLE [boards] (
    `id` SERIAL,
    `name` VARCHAR(255),
    `content` VARCHAR(255),
    `upperid` INT,
    PRIMARY KEY (`id`),
    KEY (`upperid`)
);

DROP TABLE IF EXISTS [posts];

CREATE TABLE [posts] (
    `id` SERIAL,
    `name` VARCHAR(255),
    `istopic` TINYINT,
    `upperid` INT,
    `closed` TINYINT,
    `pinned` TINYINT,
    `content` TEXT,
    `poster` INT NOT NULL,
    `date_time` INT UNSIGNED,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`poster`) REFERENCES {players} (`id`),
    KEY (`upperid`)
);

DROP TABLE IF EXISTS [profiles];

CREATE TABLE [profiles] (
    `id` SERIAL,
    `name` VARCHAR(3),
    `skill0` INT,
    `skill1` INT,
    `skill2` INT,
    `skill3` INT,
    `skill4` INT,
    `skill5` INT,
    `skill6` INT,
    `health` INT,
    `healthmax` INT,
    `direction` INT,
    `experience` INT,
    `lookbody` INT,
    `lookfeet` INT,
    `lookhead` INT,
    `looklegs` INT,
    `looktype` INT,
    `maglevel` INT,
    `mana` INT,
    `manamax` INT,
    `manaspent` INT,
    `soul` INT,
    `cap` INT,
    `food` INT,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

DROP TABLE IF EXISTS [containers];

CREATE TABLE [containers] (
    `id` SERIAL,
    `content` INT,
    `slot` INT,
    `count` INT,
    `profile` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`profile`) REFERENCES [profiles] (`id`) ON DELETE CASCADE,
    KEY (`slot`)
) ENGINE = InnoDB;

DROP TABLE IF EXISTS [news];

CREATE TABLE [news] (
    `id` SERIAL,
    `name` VARCHAR(255),
    `content` TEXT,
    `date_time` INT,
    PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS [invites];

CREATE TABLE [invites] (
    `id` SERIAL,
    `name` INT NOT NULL,
    `content` INT NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`name`) REFERENCES {players} (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`content`) REFERENCES {guilds} (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

DROP TABLE IF EXISTS [requests];

CREATE TABLE [requests] (
    `id` SERIAL,
    `name` INT NOT NULL,
    `content` INT NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`name`) REFERENCES {players} (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`content`) REFERENCES {guilds} (`id`) ON DELETE CASCADE
) ENGINE = InnoDB;

DROP VIEW IF EXISTS [posts_with_authors];

CREATE VIEW [posts_with_authors]
AS SELECT
    [posts].`id` AS `id`,
    [posts].`name` AS `name`,
    [posts].`content` AS `content`,
    [posts].`istopic` AS `istopic`,
    [posts].`upperid` AS `upperid`,
    [posts].`closed` AS `closed`,
    [posts].`pinned` AS `pinned`,
    [posts].`date_time` AS `date_time`,
    {accounts}.`avatar` AS `avatar`,
    {accounts}.`signature` AS `signature`,
    {players}.`id` AS `author`,
    {players}.`name` AS `poster`
FROM
    [posts], {accounts}, {players}
WHERE
    [posts].`poster` = {players}.`id` AND {players}.`account_id` = {accounts}.`id`;

DROP VIEW IF EXISTS [private_messages];

CREATE VIEW [private_messages]
AS SELECT
    [pms].`id` AS `id`,
    [pms].`name` AS `name`,
    [pms].`content` AS `content`,
    [pms].`read` AS `read`,
    [pms].`date_time` AS `date_time`,
    {accounts}.`avatar` AS `avatar`,
    `froms`.`name` AS `from`,
    `tos`.`name` AS `to`,
    `froms`.`account_id` AS `from_account`,
    `tos`.`account_id` AS `to_account`
FROM
    [pms], {accounts},
    {players} AS `froms`,
    {players} AS `tos`
WHERE
    [pms].`to` = `tos`.`id` AND [pms].`from` = `froms`.`id` AND {accounts}.`id` = `froms`.`account_id`;

DROP VIEW IF EXISTS [guild_members];

CREATE VIEW [guild_members]
AS SELECT
    {players}.`id` AS `id`,
    {players}.`name` AS `name`,
    {players}.`account_id` AS `account`,
    {players}.`guildnick` AS `guildnick`,
    {guild_ranks}.`id` AS `rank_id`,
    {guild_ranks}.`name` AS `rank`,
    {guild_ranks}.`level` AS `level`,
    {guilds}.`id` AS `guild_id`,
    {guilds}.`name` AS `guild`
FROM
    {guilds}, {guild_ranks}, {players}
WHERE
    {players}.`rank_id` = {guild_ranks}.`id` AND {guild_ranks}.`guild_id` = {guilds}.`id`;

DROP VIEW IF EXISTS [player_skills];

CREATE VIEW [player_skills]
AS SELECT
    {players}.`id` AS `id`,
    {players}.`name` AS `name`,
    {player_skills}.`skillid` AS `skillid`,
    {player_skills}.`value` AS `value`
FROM
    {players}, {player_skills}, {groups}
WHERE
    {groups}.`id` = {players}.`group_id` AND {players}.`id` = {player_skills}.`player_id` AND {groups}.`access` < 3
ORDER BY
    {player_skills}.`value` DESC;

SET FOREIGN_KEY_CHECKS = 1;
