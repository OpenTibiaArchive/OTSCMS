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

// loads data from form
$id = InputData::read('id');
$player = InputData::read('player');
$row = $ots->createObject('Player');
$row->load($id);

// checks if the names are different
// if not then we dont need to change anything
if($player['name'] != $row->getName() )
{
    // checks if the name isn't already used
    $row->find($player['name']);

    if( $row->isLoaded() )
    {
        throw new HandledException('NameUsed');
    }

    // re-loads current player
    $row->load($id);
}

// finds experience level based on points
for($level = 1; 50 * $level * (($level + 1) * ($level + 1) - 5 * $level + 7) / 3 <= $player['experience']; $level++);

$account = $ots->createObject('Account');
$account->load($player['account_id']);
$group = $ots->createObject('Group');
$group->load($player['group_id']);

// updates character informations
$row->setName($player['name']);
$row->setAccount($account);
$row->setGroup($group);
$row->setExperience($player['experience']);
$row->setLevel($player['level']);
$row->setMagLevel($player['maglevel']);
$row->save();
$row->setCustomField('comment', $player['comment']);

// there is nothing to display
// redirects internaly to management page
OTSCMS::call('Character', 'manage');

?>
