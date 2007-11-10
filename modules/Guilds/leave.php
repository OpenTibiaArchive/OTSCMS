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
$form['action'] = '/guild/quit';
$form['submit'] = $language['Modules.Guilds.LeaveSubmit'];

$guild = $ots->createObject('Guild');
$guild->load( InputData::read('id') );

$account = $ots->createObject('Account');
$account->load(User::$number);

$players = array();

// loads all non-leader members of this guild that belongs to currently logged account
foreach($account as $player)
{
    $rank = $player->getRank();

    if( isset($rank) && $rank->getLevel() < 3)
    {
        $players[ $player->getId() ] = $player->getName();
    }
}

$form->addField('id', ComponentAdminForm::FieldSelect, $language['Modules.Guilds.LeaveCharacter'], array('options' => $players) );

?>
