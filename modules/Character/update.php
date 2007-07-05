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
$player = InputData::read('player');
$row = new OTS_Player( (int) InputData::read('id') );

// checks if the names are different
// if not then we dont need to change anything
if($player['name'] != $row['name'])
{
    // checks if the name isn't already used
    $select = $db->prepare('SELECT COUNT(`id`) AS `count` FROM {players} WHERE `name` = :name');
    $select->execute( array(':name' => $player['name']) );
    $select = $select->fetch();

    if($select['count'])
    {
        throw new HandledException('NameUsed');
    }
}

// finds experience level based on points
$level = 1;
while(50 / 3 * pow($level + 1, 3) - 100 * pow($level + 1, 2) + (850 / 3) * ($level + 1) - 200 <= $player['experience'])
{
    $level++;
}

// updates character informations
$row['name'] = $player['name'];
$row['account_id'] = $player['account_id'];
$row['group_id'] = $player['group_id'];
$row['experience'] = $player['level'];
$row['level'] = $player['level'];
$row['maglevel'] = $player['maglevel'];
$row['comment'] = $player['comment'];
$row->save();

// there is nothing to display
// redirects internaly to management page
OTSCMS::call('Character', 'manage');

?>
