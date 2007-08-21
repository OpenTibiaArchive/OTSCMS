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

$guild = InputData::read('guild');

// checks if guild with such name exists
$count = $db->prepare('SELECT COUNT(`id`) AS `count` FROM {guilds} WHERE `name` = :name');
$count->execute( array(':name' => $guild['name']) );
$count = $count->fetch();

if($count['count'] > 0)
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Guilds.NameUsed'];
    return;
}

// loads creator data
$player = $ots->createObject('Player');
$player->load($guild['ownerid']);

// checks if user has controll over given character
if( !$player->isLoaded() || $player->getAccount()->getId() != User::$number)
{
    throw new HandledException('NotOwner');
}

// creates guild
$row = new OTS_Guild();
$row['name'] = htmlspecialchars($guild['name']);
$row['ownerid'] = $player->getId();
$row['creationdata'] = time();
$row->save();

// reads guild leader rank created by database handler
$rank = $db->query('SELECT `id` FROM {guild_ranks} WHERE `guild_id` = ' . $row['id'] . ' AND `name` = \'Leader\'')->fetch();

// updates leader rank info
$player->getRankId() = $rank['id'];
$player->save();

// moves to just-created guild page
InputData::write('id', $row['id']);
OTSCMS::call('Guilds', 'display');

?>
