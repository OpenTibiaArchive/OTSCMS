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

// checks if the user is logged in
// if not redirects to login page
// this is default page of account module and shouldn't be forebidden for users that are not logged
if(!User::$logged)
{
    OTSCMS::call('Account', 'login');
    return;
}

$template->addJavaScript('character');
$template->addJavaScript('user');

// reads account information
$account = new OTS_Account(User::$number);

// account metainfo table
$data = $template->createComponent('TableData');
$data['caption'] = $language['Modules.Account.AccountData'];
$data['data'] = array($language['Modules.Account.AccountNumber'] => $account['id'], $language['Modules.Account.Premium'] => $account['premdays']);

// password change form
$form = $template->createComponent('AdminForm');
$form['action'] = 'account.php?command=change';
$form['submit'] = $language['Modules.Account.ChangeSubmit'];

// form fields
$form->addField('oldpassword', ComponentAdminForm::FieldPassword, $language['Modules.Account.OldPassword']);
$form->addField('newpassword', ComponentAdminForm::FieldPassword, $language['Modules.Account.NewPassword']);
$form->addField('newpassword2', ComponentAdminForm::FieldPassword, $language['Modules.Account.ReNewPassword']);

// forum profile
$profile = $template->createComponent('AdminForm');
$profile['action'] = 'forum.php?module=Account&command=save';
$profile['submit'] = $language['main.admin.UpdateSubmit'];
$profile['id'] = 'userForm';

// form fields
$profile->addField('user[signature]', ComponentAdminForm::FieldArea, $language['Modules.Account.Signature'], $account['signature']);
$profile->addField('user[avatar]', ComponentAdminForm::FieldText, $language['Modules.Account.Avatar'], $account['avatar']);
$profile->addField('user[website]', ComponentAdminForm::FieldText, $language['Modules.Account.Website'], $account['website']);

// account characters
$list = $template->createComponent('TableList');
$list['caption'] = $language['Modules.Account.CharactersData'];
$list->addField('name', $language['Modules.Character.Name']);
$list->addField('actions', $language['main.admin.Actions']);
$list->idPrefix = 'characterID_';

$characters = array();

foreach( $db->query('SELECT `id`, `name` FROM {players} WHERE `account_id` = ' . $account['id']) as $character)
{
    // actions links
    $root = XMLToolbox::createDocumentFragment();

    // delete link
    $delete = XMLToolbox::createElement('a');
    $delete->setAttribute('href', 'character.php?command=delete&id=' . $character['id']);
    $delete->setAttribute('onclick', 'if( confirm(\'' . $language['Modules.Account.DeleteConfirm'] . '\') ) { return pageCharacter.Delete(' . $character['id'] . '); } else { return false; }');
    $delete->addContent($language['main.admin.DeleteSubmit']);

    // edit link
    $edit = XMLToolbox::createElement('a');
    $edit->setAttribute('href', 'character.php?command=change&id=' . $character['id']);
    $edit->addContent($language['main.admin.EditSubmit']);

    $root->addContents($delete, ' | ', $edit);

    $characters[] = array('id' => $character['id'], 'name' => $character['name'], 'actions' => $root);
}

// reads account's characters
$list['list'] = $characters;

?>
