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

// loads player fro edition
$player = POT::getInstance()->createObject('Player');
$player->load( InputData::read('id') );

$comment = $db->query('SELECT `comment` FROM {players} WHERE `id` = ' . $player->getId() );

// creates edition form
$form = $template->createComponent('AdminForm');
$form['action'] = '/admin/module=Character&command=update&id=' . $player->getId();
$form['submit'] = $language['main.admin.UpdateSubmit'];

// edition fields
$form->addField('player[name]', ComponentAdminForm::FieldText, $language['Modules.Character.Name'], $player->getName() );
$form->addField('player[account_id]', ComponentAdminForm::FieldSelect, $language['Modules.Account.AccountNumber'], array('options' => Toolbox::dumpRecords( $db->query('SELECT `id` AS `key`, `id` AS `value` FROM {accounts}') ), 'selected' => $player->getAccount()->getId() ) );
$form->addField('player[group_id]', ComponentAdminForm::FieldSelect, $language['Modules.Character.Group'], array('options' => Toolbox::dumpRecords( $db->query('SELECT `id` AS `key`, `name` AS `value` FROM {groups}') ), 'selected' => $player->getGroup()->getId() ) );
$form->addField('player[experience]', ComponentAdminForm::FieldText, $language['Modules.Character.Experience'], $player->getExperience() );
$form->addField('player[maglevel]', ComponentAdminForm::FieldText, $language['Modules.Character.MagicLevel'], $player->getMagLevel() );
$form->addField('player[comment]', ComponentAdminForm::FieldArea, $language['Modules.Character.Comment'], $comment['comment']);

?>
