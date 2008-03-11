<?php
/*
    This file is part of OTSCMS (http://www.otscms.com/) project.

    Copyright (C) 2005 - 2008 Wrzasq (wrzasq@gmail.com)

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
$player = new OTS_Player( (int) InputData::read('id') );
$rank = $player->rank;
$new = new OTS_GuildRank( (int) $member['rank_id']);
$guild = $rank->guild;

// check if ranks are from same guild
if($new->guild->id != $guild->id)
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Guilds.DifferentGuild'];
    return;
}

// if not a gamemaster checks if user is a leader
if( !User::hasAccess(3) && Toolbox::guildAccess($guild) < $rank->level)
{
    throw new NoAccessException();
}

// updates member
$player->rank = $new;
$player->guildNick = $member['guildnick'];
$player->save();

// moves to guilds list
InputData::write('id', $guild->id);
OTSCMS::call('Guilds', 'display');

?>
