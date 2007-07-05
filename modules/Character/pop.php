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

// deletes container recursively
function deleteContainer($db, $id)
{
    // deletes all contained items
    foreach( $db->query('SELECT `id` FROM [containers] WHERE `slot` = ' . $id) as $container)
    {
        deleteContainer($db, $container['id']);
    }

    // deletes container itself
    $db->query('DELETE FROM [containers] WHERE `id` = ' . $id);
}

$id = (int) InputData::read('id');

// fetches profile name
$profile = $db->query('SELECT [profiles].`name` AS `name` FROM [containers], [profiles] WHERE [profiles].`id` = [containers].`profile` AND [containers].`id` = ' . $id)->fetch();
$profile = explode('.', $profile['name']);

// deletes item - database trigger will handle it recursively as it can be a container
deleteContainer($db, $id);

// there is nothing to display
// redirects internaly to profile page
InputData::write('gender', $profile[0]);
InputData::write('vocation', $profile[1]);
OTSCMS::call('Character', 'settings');

?>
