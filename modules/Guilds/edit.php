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

// reads member
$member = $db->query('SELECT `id`, `name`, `rank_id`, `guild_id`, `guildnick`, `level` FROM [guild_members] WHERE `id` = ' . (int) InputData::read('id') )->fetch();

// if not a gamemaster checks if user can modify current member record
if( !User::hasAccess(3) && Toolbox::guildAccess($member['guild_id'], User::$number) < $member['level'])
{
    throw new NoAccessException();
}

// edition form
$form = $template->createComponent('AdminForm');
$form['action'] = 'guild.php?command=update&id=' . $member['id'];
$form['submit'] = $language['main.admin.UpdateSubmit'];

$form->addField('', ComponentAdminForm::FieldLabel, $language['Modules.Guilds.EditCharacter'], $member['name']);
$form->addField('member[rank_id]', ComponentAdminForm::FieldSelect, $language['Modules.Guilds.EditRank'], array('options' => Toolbox::dumpRecords( $db->query('SELECT `id` AS `key`, `name` AS `value` FROM {guild_ranks} WHERE `guild_id` = ' . $member['guild_id']) ), 'selected' => $member['rank_id']) );
$form->addField('member[guildnick]', ComponentAdminForm::FieldText, $language['Modules.Guilds.EditTitle'], $member['guildnick']);

?>
