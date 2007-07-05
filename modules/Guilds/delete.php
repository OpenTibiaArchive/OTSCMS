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

// reads rank info
$rank = new OTS_GuildRank( (int) InputData::read('id') );

// if not a gamemaster checks if user is a leader
if( !User::hasAccess(3) && Toolbox::guildAccess($rank['guild_id'], User::$number) < $rank['level'])
{
    throw new NoAccessException();
}

// gets any other rank from that guild with same access level
$new = $db->query('SELECT `id` FROM {guild_ranks} WHERE `guild_id` = ' . $rank['guild_id'] . ' AND `level` = ' . $rank['level'])->fetch();

// moves all members from old rank to new
$db->exec('UPDATE {players} SET `rank_id` = ' . $new['id'] . ' WHERE `rank_id` = ' . $rank['id']);

// removes rank
$db->query('DELETE FROM {guild_ranks} WHERE `id` = ' . $rank['id']);

// displays creation form
InputData::write('id', $rank['guild_id']);
OTSCMS::call('Guilds', 'manage');

?>
