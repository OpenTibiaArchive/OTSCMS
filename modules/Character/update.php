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

$ots = POT::getInstance();

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
$level = 1;
while(50 / 3 * pow($level + 1, 3) - 100 * pow($level + 1, 2) + (850 / 3) * ($level + 1) - 200 <= $player['experience'])
{
    $level++;
}

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

$update = $db->prepare('UPDATE {players} SET `comment` = :comment WHERE `id` = :id');
$update->execute( array(':comment' => $player['comment'], ':id' => $row->getId() ) );

// there is nothing to display
// redirects internaly to management page
OTSCMS::call('Character', 'manage');

?>
