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

$template->addJavaScript('pm');

// restrictions list
$list = $template->createComponent('TableList');
$list->addField('name', $language['Modules.PM.Name']);
$list->addField('from', $language['Modules.PM.From']);
$list->addField('date_time', $language['Modules.PM.DateTime']);
$list->addField('actions', $language['main.admin.Actions']);
$list->idPrefix = 'pmID_';

$pms = array();

// reads all messages to players from current account
foreach( $db->query('SELECT `id`, `name`, `read`, `date_time`, `from` FROM [private_messages] WHERE `to_account` = ' . User::$number) as $pm)
{
    $root = XMLToolbox::createDocumentFragment();

    // new message
    if(!$pm['read'])
    {
        $span = XMLToolbox::createElement('span');
        $span->setAttribute('style', 'font-weight: bold;');
        $span->addContent($language['Modules.PM.unread']);

        $root->addContents('[', $span, '] ');
    }

    // display link
    $a = XMLToolbox::createElement('a');
    $a->setAttribute('href', 'message/' . $pm['id']);
    $a->addContent($pm['name']);
    $root->addContent($a);

    // author profile link
    $link = XMLToolbox::createElement('a');
    $link->setAttribute('href', 'characters/' . urlencode($pm['from']) );
    $link->addContent($pm['from']);

    // delete link
    $delete = XMLToolbox::createElement('a');
    $delete->setAttribute('href', 'message/' . $pm['id'] . '/delete');
    $delete->setAttribute('onclick', 'if( confirm(Language[0]) ) { return pagePM.Delete(' . $pm['id'] . '); } else { return false; }');
    $delete->addContent($language['main.admin.DeleteSubmit']);

    $reply = XMLToolbox::createElement('a');
    $reply->setAttribute('href', 'message/' . $pm['id'] . '/reply');
    $reply->addContent($language['Modules.PM.ReplySubmit']);

    $forward = XMLToolbox::createElement('a');
    $forward->setAttribute('href', 'message/' . $pm['id'] . '/forward');
    $forward->addContent($language['Modules.PM.ForwardSubmit']);

    $actions = XMLToolbox::createDocumentFragment();
    $actions->addContents($delete, ' | ', $reply, ' | ', $forward);

    $pms[] = array('id' => $pm['id'], 'name' => $root, 'from' => $link, 'date_time' => date($config['site.date_format'], $pm['date_time']), 'actions' => $actions);
}

$list['list'] = $pms;

?>
