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

$pm = $db->query('SELECT `name`, `content` FROM [private_messages] WHERE `to_account` = ' . User::$number . ' AND `id` = ' . (int) InputData::read('id') )->fetch();

// checks if it's current user message
if( empty($pm) )
{
    throw new NoAccessException();
}

// moves to sending form with reply data
InputData::write('name', 'Fw: ' . $pm['name']);
InputData::write('content', '[quote]' . $pm['content'] . '[/quote]');
OTSCMS::call('PM', 'new');

?>
