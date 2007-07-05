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

// restrictions list
$list = $template->createComponent('TableList');
$list->addField('name', $language['Modules.PM.Name']);
$list->addField('to', $language['Modules.PM.To']);
$list->addField('date_time', $language['Modules.PM.DateTime']);

$pms = array();

// reads all messages to players from current account
foreach( $db->query('SELECT `id`, `name`, `read`, `date_time`, `to` FROM [private_messages] WHERE `from_account` = ' . User::$number) as $pm)
{
    // display link
    $a = XMLToolbox::createElement('a');
    $a->setAttribute('href', 'priv.php?command=display&id=' . $pm['id']);
    $a->addContent($pm['name']);

    // target profile link
    $link = XMLToolbox::createElement('a');
    $link->setAttribute('href', 'character.php?name=' . urlencode($pm['to']) );
    $link->addContent($pm['to']);

    $pms[] = array('id' => $pm['id'], 'name' => $a, 'to' => $link, 'date_time' => date($config['site.date_format'], $pm['date_time']) );
}

$list['list'] = $pms;

?>
