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

// loads poll info
$poll = new CMS_Poll( (int) InputData::read('id') );

// edition form
$form = $template->createComponent('AdminForm');
$form['action'] = '/admin/module=Poll&command=update&id=' . $poll['id'];
$form['submit'] = $language['main.admin.UpdateSubmit'];
$form['id'] = 'pollForm';

// form fields
$form->addField('poll[name]', ComponentAdminForm::FieldText, $language['Modules.Poll.Name'], $poll['name']);
$form->addField('poll[content]', ComponentAdminForm::FieldArea, $language['Modules.Poll.Content'], $poll['content']);

// for AJAX compatibility
$form['data'] = $poll;

// options edit
$edit = $template->createComponent('PollOptions');
$edit['options'] = $db->query('SELECT `id` AS `key`, `name` AS `value` FROM [options] WHERE `poll` = ' . $poll['id'])->fetchAll(PDO::FETCH_KEY_PAIR);

?>
