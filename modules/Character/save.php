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

// loads data in correct order
$character = InputData::read('character');

$row = new OTS_Player();
$row->load( InputData::read('id') );

// checks if the character that user wants to edit is his/her
if(!$row->loaded || $row->account->id != User::$number)
{
    throw new HandledException('NotOwner');
}

$row->setCustomField('comment', $character['comment']);
$row->save();

// there is nothing to display
// redirects internaly to management page
OTSCMS::call('Account', 'account');

?>
