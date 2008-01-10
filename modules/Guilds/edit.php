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

// reads member
$member = new OTS_Player();
$member->load( InputData::read('id') );
$rank = $member->rank;
$guild = $rank->guild();

// if not a gamemaster checks if user can modify current member record
if( !User::hasAccess(3) && Toolbox::guildAccess($guild) < $rank->level)
{
    throw new NoAccessException();
}

$ranks = array();

// guild ranks list
foreach($guild as $guildRank)
{
    $ranks[$guildRank->id] = $guildRank->name;
}

// edition form
$form = $template->createComponent('AdminForm');
$form['action'] = 'admin/module=Guilds&command=update&id=' . $member->id;
$form['submit'] = $language['main.admin.UpdateSubmit'];

$form->addField('', ComponentAdminForm::FieldLabel, $language['Modules.Guilds.EditCharacter'], $member->name);
$form->addField('member[rank_id]', ComponentAdminForm::FieldSelect, $language['Modules.Guilds.EditRank'], array('options' => $ranks, 'selected' => $rank->id) );
$form->addField('member[guildnick]', ComponentAdminForm::FieldText, $language['Modules.Guilds.EditTitle'], $member->guildNick);

?>
