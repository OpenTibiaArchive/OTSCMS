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

// deletes forum board recursively
function deleteBoard($db, $id)
{
    // deletes all sub-boards
    foreach( $db->query('SELECT `id` FROM [boards] WHERE `upperid` = ' . $id) as $board)
    {
        deleteBoard($db, $board['id']);
    }

    // deletes board topic replies
    foreach( $db->query('SELECT `id` FROM [posts] WHERE `istopic` = 1 AND `upperid` = ' . $id) as $topic)
    {
        $db->exec('DELETE FROM [posts] WHERE `istopic` = 0 AND `upperid` = ' . $topic['id']);
    }

    // deletes board itself and it's topics
    $db->exec('DELETE FROM [posts] WHERE `istopic` = 1 AND `upperid` = ' . $id);
    $db->exec('DELETE FROM [boards] WHERE `id` = ' . $id);
}

// loads board (upperid will be used for redirection)
$board = new CMS_Board( (int) InputData::read('id') );

// deletes forum
deleteBoard($db, $board['id']);

// redirects to main page
InputData::write('id', $board['upperid']);
OTSCMS::call('Forum', 'board');

?>
