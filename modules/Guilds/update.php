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

// pre-loads HTTP data
$member = InputData::read('member');
$player = $ots->createObject('Player');
$player->load( InputData::read('id') );
$rank = $player->getRank();
$new = $ots->createObject('GuildRank');
$new->load($member['rank_id']);
$guild = $rank->getGuild();

// check if ranks are from same guild
if( $new->getGuild()->getId() != $guild->getId() )
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Guilds.DifferentGuild'];
    return;
}

// if not a gamemaster checks if user is a leader
if( !User::hasAccess(3) && Toolbox::guildAccess($guild) < $rank->getLevel() )
{
    throw new NoAccessException();
}

// updates member
$player->setRank($new);
$player->setGuildNick($member['guildnick']);
$player->save();

// moves to guilds list
InputData::write('id', $guild->getId() );
OTSCMS::call('Guilds', 'display');

?>
