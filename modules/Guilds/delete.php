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
$rank = $ots->createObject('GuildRank');
$rank->load( InputData::read('id') );
$guild = $rank->getGuild();

// if not a gamemaster checks if user is a leader
if( !User::hasAccess(3) && Toolbox::guildAccess($guild) < $rank->getLevel() )
{
    throw new NoAccessException();
}

// gets any other rank from that guild with same access level
$new = null;

foreach( $guild->getGuildRanks() as $guildRank)
{
    if( $rank->getId() != $guildRank->getId() && $rank->getLevel() == $guildRank->getLevel() )
    {
        $new = $guildRank;
        break;
    }
}

// moves all members from old rank to new
foreach( $new->getPlayers() as $player)
{
    $player->getRank($new);
    $player->save();
}

// removes rank
$ots->createObject('GuildRanks_List')->deleteGuildRank($rank);

// displays creation form
InputData::write('id', $guild->getId() );
OTSCMS::call('Guilds', 'manage');

?>
