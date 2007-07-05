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

// loads item data from HTTP
$container = InputData::read('container');
$profile = new CMS_Profile( (int) InputData::read('id') );

// gets last maximum container ID
$max = $db->query('SELECT MAX(`id`) + 1 AS `max` FROM [containers]')->fetch();

// first container must have slot > 9 (slots 0 to 9 are body slots)
$max = $max['max'] > 9 ? $max['max'] : 10;

// 1xx for depots
if( floor($max / 100) == 1)
{
    $max += 100;
}

// allocates new item in database
$row = new CMS_Container();
$row->create($max, $profile['id']);

// inserts new item
$row['content'] = $container['content'];
$row['slot'] = $container['slot'];
$row['count'] = (int) $container['count'];
$row->save();

// fetches profile name
$profile = explode('.', $profile['name']);

// there is nothing to display
// redirects internaly to profile page
InputData::write('gender', $profile[0]);
InputData::write('vocation', $profile[1]);
OTSCMS::call('Character', 'settings');

?>
