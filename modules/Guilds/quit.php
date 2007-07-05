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

// loads member
$character = new OTS_Player( (int) InputData::read('id') );

// checks if member belongs to current account
if($character['account_id'] != User::$number)
{
    throw new HandledException('NotOwner');
}

$guild = $db->query('SELECT {guilds}.`id` AS `id`, {guilds}.`ownerid` AS `ownerid` FROM {guilds}, {guild_ranks} WHERE {guilds}.`id` = {guild_ranks}.`guild_id` AND {guild_ranks}.`id` = ' . $character['rank_id'])->fetch();

// checks if member is a leader
if($guild['ownerid'] == $character['id'])
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Guilds.CantLeave'];
    return;
}

// clears membership data
$character['guildnick'] = '';
$character['rank_id'] = 0;
$character->save();

// moves to guild page
InputData::write('id', $guild['id']);
OTSCMS::call('Guilds', 'display');

?>
