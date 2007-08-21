<?php
/*
    This file is part of OTSCMS (http://www.otscms.com/) project.

    Copyright (C) 2005 - 2007 Wrzasq (wrzasq@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$id = (int) InputData::read('id');

// loads invitation
$invite = $db->query('SELECT [invites].`name` AS `name`, [invites].`content` AS `content`, {players}.`account_id` AS `account_id`, {guild_ranks}.`id` AS `rank` FROM {players}, [invites], {guild_ranks} WHERE {guild_ranks}.`guild_id` = [invites].`content` AND {guild_ranks}.`level` = 1 AND {players}.`id` = [invites].`name` AND [invites].`id` = ' . $id)->fetch();

// checks if user is really owner of character
if($invite['account_id'] != User::$number)
{
    throw new HandledException('NotOwner');
}

// deletes invitation
$db->query('DELETE FROM [invites] WHERE `id` = ' . $id);

// adds player to guild with default rank
$player = $ots->createObject('Player');
$player->load($invite['name']);

if( $player->isLoaded() )
{
    $player->setRankId($invite['rank']);
    $player->save();
}

// moves to guild page
InputData::write('id', $invite['content']);
OTSCMS::call('Guilds', 'display');

?>
