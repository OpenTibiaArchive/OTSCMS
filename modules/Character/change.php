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

// loads data in correct order
$character = new OTS_Player();
$character->load( InputData::read('id') );

// checks if the character that user wants to edit is his/her
if(!$character->loaded || $character->account->id != User::$number)
{
    throw new HandledException('NotOwner');
}

// comment edition form
$form = $template->createComponent('AdminForm');
$form['action'] = 'characters/' . $character->id . '/save';
$form['submit'] = $language['Modules.Character.ChangeSubmit'];
$form->addField('', ComponentAdminForm::FieldLabel, $language['Modules.Character.Name'], $character->name);
$form->addField('character[comment]', ComponentAdminForm::FieldArea, $language['Modules.Character.Comment'], $character->getCustomField('comment') );

?>
