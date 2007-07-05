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

$template->addJavaScript('poll');

if( User::hasAccess(3) )
{
    // new poll form
    $form = $template->createComponent('AdminForm');
    $form['action'] = 'poll.php?command=insert';
    $form['submit'] = $language['main.admin.InsertSubmit'];
    $form['id'] = 'pollForm';

    // form fields
    $form->addField('poll[name]', ComponentAdminForm::FieldText, $language['Modules.Poll.Name']);
    $form->addField('poll[content]', ComponentAdminForm::FieldArea, $language['Modules.Poll.Content']);
}

$list = $template->createComponent('ItemsList');
$list['header'] = $language['Modules.Poll.PollsList'];

// fetches polls list
$list['list'] = Toolbox::dumpRecords( $db->query('SELECT `id` AS `key`, `name` AS `value` FROM [polls]') );
$list['link'] = 'poll.php?command=display&id=';
$list['rowID'] = 'pollID_';

// admin actions
$list['module'] = 'Poll';
$list->actions = User::hasAccess(3);

?>
