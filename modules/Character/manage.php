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

// list table
$list = $template->createComponent('TableList');
$list->addField('name', $language['Modules.Character.Name']);
$list->addField('account', $language['Modules.Account.AccountNumber']);
$list->addField('level', $language['Modules.Character.Level']);
$list->addField('group', $language['Modules.Character.Group']);
$list->addAction('remove', $language['main.admin.DeleteSubmit']);
$list->addAction('edit', $language['main.admin.EditSubmit']);
$list->module = 'Character';
$list->idPrefix = 'characterID_';

$list['list'] = $db->query('SELECT {players}.`id` AS `id`, {players}.`name` AS `name`, {players}.`account_id` AS `account`, {players}.`level` AS `level`, {groups}.`name` AS `group` FROM {players}, {groups} WHERE {players}.`group_id` = {groups}.`id` ORDER BY {players}.`name`')->fetchAll();

?>