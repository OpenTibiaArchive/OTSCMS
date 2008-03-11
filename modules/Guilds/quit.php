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

// loads member
$character = new OTS_Player( (int) InputData::read('id') );

// checks if member belongs to current account
if(!$character->loaded || $character->account->id != User::$number)
{
    throw new HandledException('NotOwner');
}

$guild = $character->rank->guild;

// checks if member is a leader
if($guild->owner()->id == $character->id)
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Guilds.CantLeave'];
    return;
}

// clears membership data
$character->guildNick = '';
$character->setRank();
$character->save();

// moves to guild page
InputData::write('id', $guild->id);
OTSCMS::call('Guilds', 'display');

?>
