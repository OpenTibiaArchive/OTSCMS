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

// loads player info
$player = new OTS_Player( (int) InputData::read('id') );
$rank = $player->rank;
$guild = $rank->guild;

// if not a gamemaster checks if user is a leader
if( !User::hasAccess(3) && Toolbox::guildAccess($guild) < 2)
{
    throw new NoAccessException();
}

// checks user rank if he is not a leader
if($rank->level == 3)
{
    throw new NoAccessException();
}

// kick user out
$player->guildNick = '';
$player->setRank();
$player->save();

// moves to guild page
InputData::write('id', $guild->id);
OTSCMS::call('Guilds', 'display');

?>
