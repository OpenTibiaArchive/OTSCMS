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

// reads post as it's id will be used for redirection
$post = new CMS_Post( (int) InputData::read('id') );

// deletes thread if it was a topic post
if($post['istopic'])
{
    $db->exec('DELETE FROM [posts] WHERE `istopic` = 0 AND `upperid` = ' . $post['id']);
}

// deletes post
$db->exec('DELETE FROM [posts] WHERE `id` = ' . $post['id']);

InputData::write('id', $post['upperid']);

// calls board or topic view
if($post['istopic'])
{
    OTSCMS::call('Forum', 'board');
}
else
{
    OTSCMS::call('Topic', 'view');
}

?>
