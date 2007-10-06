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
$member = $ots->createObject('Player');
$member->load( InputData::read('id') );
$rank = $member->getRank();
$guild = $rank->getGuild();

// if not a gamemaster checks if user can modify current member record
if( !User::hasAccess(3) && Toolbox::guildAccess($guild) < $rank->getLevel() )
{
    throw new NoAccessException();
}

$ranks = array();

// guild ranks list
foreach( $guild->getGuildRanks() as $guildRank)
{
    $ranks[ $guildRank->getId() ] = $guildRank->getName();
}

// edition form
$form = $template->createComponent('AdminForm');
$form['action'] = '/admin/module=Guilds&command=update&id=' . $member->getId();
$form['submit'] = $language['main.admin.UpdateSubmit'];

$form->addField('', ComponentAdminForm::FieldLabel, $language['Modules.Guilds.EditCharacter'], $member->getName() );
$form->addField('member[rank_id]', ComponentAdminForm::FieldSelect, $language['Modules.Guilds.EditRank'], array('options' => $ranks, 'selected' => $rank->getId() ) );
$form->addField('member[guildnick]', ComponentAdminForm::FieldText, $language['Modules.Guilds.EditTitle'], $member->getGuildNick() );

?>
