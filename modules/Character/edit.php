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

// loads player fro edition
$player = new OTS_Player();
$player->load( InputData::read('id') );

// creates edition form
$form = $template->createComponent('AdminForm');
$form['action'] = '/admin/module=Character&command=update&id=' . $player->id;
$form['submit'] = $language['main.admin.UpdateSubmit'];

$accounts = array();

// accounts list
foreach( new OTS_Accounts_List() as $account)
{
    $accounts[$account->id] = $account->id;
}

$groups = array();

// groups list
foreach( new OTS_Groups_List() as $group)
{
    $groups[$group->id] = $group->name;
}

// edition fields
$form->addField('player[name]', ComponentAdminForm::FieldText, $language['Modules.Character.Name'], $player->name);
$form->addField('player[account_id]', ComponentAdminForm::FieldSelect, $language['Modules.Account.AccountNumber'], array('options' => $accounts, 'selected' => $player->account->id) );
$form->addField('player[group_id]', ComponentAdminForm::FieldSelect, $language['Modules.Character.Group'], array('options' => $groups, 'selected' => $player->group->id) );
$form->addField('player[experience]', ComponentAdminForm::FieldText, $language['Modules.Character.Experience'], $player->experience);
$form->addField('player[maglevel]', ComponentAdminForm::FieldText, $language['Modules.Character.MagicLevel'], $player->magLevel);
$form->addField('player[comment]', ComponentAdminForm::FieldArea, $language['Modules.Character.Comment'], $player->getCustomField('comment') );

?>
