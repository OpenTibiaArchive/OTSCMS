ALTER TABLE {players} ADD `comment` TEXT;
ALTER TABLE {accounts} ADD `signature` TEXT;
ALTER TABLE {accounts} ADD `avatar` VARCHAR(255);
ALTER TABLE {accounts} ADD `website` VARCHAR(255);

DROP TABLE [settings];

CREATE TABLE [settings] (
    `name` VARCHAR(255),
    `content` TEXT,
    UNIQUE (`name`)
);

DROP TABLE [online];

CREATE TABLE [online] (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(255),
    `content` VARCHAR(255),
    `port` INTEGER,
    `maximum` INTEGER DEFAULT 0,
    UNIQUE (`content`, `port`)
);

DROP TABLE [links];

CREATE TABLE [links] (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(255),
    `content` VARCHAR(255)
);

DROP TABLE [access];

CREATE TABLE [access] (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(255),
    `content` INTEGER,
    UNIQUE (`name`)
);

DROP TABLE [pms];

CREATE TABLE [pms] (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(255),
    `content` TEXT,
    `from` INTEGER NOT NULL,
    `to` INTEGER NOT NULL,
    `read` INTEGER DEFAULT 0,
    `date_time` INTEGER,
    FOREIGN KEY (`from`) REFERENCES {players} (`id`),
    FOREIGN KEY (`to`) REFERENCES {players} (`id`)
);

DROP TABLE [logs];

CREATE TABLE [logs] (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(255),
    `content` INTEGER,
    `date_time` INTEGER
);

DROP TABLE [download];

CREATE TABLE [download] (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(255),
    `content` TEXT,
    `binary` INTEGER,
    `file` BLOB
);

DROP TABLE [gallery];

CREATE TABLE [gallery] (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(255),
    `content` TEXT,
    `binary` INTEGER,
    `file` BLOB
);

DROP TABLE [polls];

CREATE TABLE [polls] (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(255),
    `content` TEXT
);

DROP TABLE [options];

CREATE TABLE [options] (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(255),
    `poll` INT NOT NULL,
    FOREIGN KEY (`poll`) REFERENCES [polls] (`id`)
);

DROP TABLE [votes];

CREATE TABLE [votes] (
    `name` INT NOT NULL,
    `content` INT NOT NULL,
    FOREIGN KEY (`name`) REFERENCES [options] (`id`),
    FOREIGN KEY (`content`) REFERENCES {accounts} (`id`),
    UNIQUE (`name`, `content`)
);

DROP TABLE [boards];

CREATE TABLE [boards] (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(255),
    `content` VARCHAR(255),
    `upperid` INT KEY
);

DROP TABLE [posts];

CREATE TABLE [posts] (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(255),
    `istopic` INT,
    `upperid` INT KEY,
    `closed` INT,
    `pinned` INT,
    `content` TEXT,
    `poster` INT NOT NULL,
    `date_time` INT,
    FOREIGN KEY (`poster`) REFERENCES {players} (`id`)
);

DROP TABLE [profiles];

CREATE TABLE [profiles] (
    `id` INTEGER PRIMARY KEY,
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
    `food` INT
);

DROP TABLE [containers];

CREATE TABLE [containers] (
    `id` INTEGER PRIMARY KEY,
    `content` INT,
    `slot` INT KEY,
    `count` INT,
    `profile` INT NOT NULL,
    FOREIGN KEY (`profile`) REFERENCES [profiles] (`id`)
);

DROP TABLE [news];

CREATE TABLE [news] (
    `id` INTEGER PRIMARY KEY,
    `name` VARCHAR(255),
    `content` TEXT,
    `date_time` INT
);

DROP TABLE [invites];

CREATE TABLE [invites] (
    `id` INTEGER PRIMARY KEY,
    `name` INT NOT NULL,
    `content` INT NOT NULL,
    FOREIGN KEY (`name`) REFERENCES {players} (`id`),
    FOREIGN KEY (`content`) REFERENCES {guilds} (`id`)
);

DROP TABLE [requests];

CREATE TABLE [requests] (
    `id` INTEGER PRIMARY KEY,
    `name` INT NOT NULL,
    `content` INT NOT NULL,
    FOREIGN KEY (`name`) REFERENCES {players} (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`content`) REFERENCES {guilds} (`id`) ON DELETE CASCADE
);

DROP VIEW [posts_with_authors];

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

DROP VIEW [private_messages];

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

DROP VIEW [guild_members];

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

DROP VIEW [player_skills];

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

DROP TRIGGER {ondelete_players};

CREATE TRIGGER {ondelete_players}
BEFORE DELETE
ON {players}
FOR EACH ROW
BEGIN
    SELECT RAISE(ROLLBACK, 'DELETE on table {players} violates foreign: `ownerid` from table {guilds}')
    WHERE (SELECT `id` FROM {guilds} WHERE `ownerid` = OLD.`id`) IS NOT NULL;

    DELETE FROM {player_depotitems} WHERE `player_id` = OLD.`id`;
    DELETE FROM {player_spells} WHERE `player_id` = OLD.`id`;
    DELETE FROM {player_viplist} WHERE `player_id` = OLD.`id` OR `vip_id` = OLD.`id`;
    DELETE FROM {player_storage} WHERE `player_id` = OLD.`id`;
    DELETE FROM {player_skills} WHERE `player_id` = OLD.`id`;
    DELETE FROM {player_items} WHERE `player_id` = OLD.`id`;
    DELETE FROM {bans} WHERE `type` = 2 AND `player` = OLD.`id`;
    UPDATE {houses} SET `owner` = 0 WHERE `owner` = OLD.`id`;
    DELETE FROM [pms] WHERE `from` = OLD.`id` OR `to` = OLD.`id`;
    DELETE FROM [invites] WHERE `name` = OLD.`id`;
END;

DROP TRIGGER {ondelete_accounts};

CREATE TRIGGER {ondelete_accounts}
BEFORE DELETE
ON {accounts}
FOR EACH ROW
BEGIN
    DELETE FROM {players} WHERE `account_id` = OLD.`id`;
    DELETE FROM {bans} WHERE `type` = 3 AND `account` = OLD.`id`;
    DELETE FROM [votes] WHERE `content` = OLD.`id`;
END;

DROP TRIGGER {ondelete_guilds};

CREATE TRIGGER {ondelete_guilds}
BEFORE DELETE
ON {guilds}
FOR EACH ROW
BEGIN
    UPDATE {players} SET `guildnick` = '', `rank_id` = 0 WHERE `rank_id` IN (SELECT `id` FROM {guild_ranks} WHERE `guild_id` = OLD.`id`);
    DELETE FROM {guild_ranks} WHERE `guild_id` = OLD.`id`;
    DELETE FROM [invites] WHERE `content` = OLD.`id`;
END;

DROP TRIGGER [ondelete_polls];

CREATE TRIGGER [ondelete_polls]
BEFORE DELETE
ON [polls]
FOR EACH ROW
BEGIN
    DELETE FROM [options] WHERE `poll` = OLD.`id`;
END;

DROP TRIGGER [ondelete_options];

CREATE TRIGGER [ondelete_options]
BEFORE DELETE
ON [options]
FOR EACH ROW
BEGIN
    DELETE FROM [votes] WHERE `name` = OLD.`id`;
END;

DROP TRIGGER [ondelete_profiles];

CREATE TRIGGER [ondelete_profiles]
BEFORE DELETE
ON [profiles]
FOR EACH ROW
BEGIN
    DELETE FROM [containers] WHERE `profile` = OLD.`id`;
END;
