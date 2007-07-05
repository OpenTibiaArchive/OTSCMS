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

// guild creation form
$form = $template->createComponent('AdminForm');
$form['action'] = 'guild.php?command=quit';
$form['submit'] = $language['Modules.Guilds.LeaveSubmit'];

// loads all non-leader members of this guild that belongs to currently logged account
$form->addField('id', ComponentAdminForm::FieldSelect, $language['Modules.Guilds.LeaveCharacter'], array('options' => Toolbox::dumpRecords( $db->query('SELECT {players}.`id` AS `key`, {players}.`name` AS `value` FROM {players}, {guilds}, {guild_ranks} WHERE {players}.`rank_id` AND {guild_ranks}.`id` AND {guild_ranks}.`guild_id` = {guilds}.`id` AND {players}.`id` != {guilds}.`ownerid` AND {guild_ranks}.`level` < 3 AND {guilds}.`id` = ' . (int) InputData::read('id') . ' AND {players}.`account_id` = ' . User::$number) ) ) );

?>
