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

// reads message
$pm = $db->query('SELECT `id`, `name`, `content`, `date_time`, `from`, `to`, `to_account`, `avatar` FROM  [private_messages] WHERE (`to_account` = ' . User::$number . ' OR `from_account` = ' . User::$number . ') AND `id` = ' . (int) InputData::read('id') )->fetch();

// not his message
if( empty($pm) )
{
    throw new NoAccessException();
}

// parses message as BB code and then parses it into XML tree
$pm['content'] = XMLToolbox::inparse( BBParser::parse($pm['content']) );

$display = $template->createComponent('PM');
$display['pm'] = $pm;
$display->receiver = User::$number == $pm['to_account'];

// marks message as read
if($display->receiver && !$pm['read'])
{
    $db->exec('UPDATE [pms] SET `read` = 1 WHERE `id` = ' . $pm['id']);
}

?>
