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

$guild = $ots->createObject('Guild');
$guild->load( InputData::read('id') );

$isLeader = false;
$isVice = User::hasAccess(3);
$isMember = false;
$isInvited = false;

// loads invited list
if(User::$logged)
{
    $invites = new InvitesDriver($guild);

    foreach( $guild->listInvites() as $player)
    {
        if( $player->getAccount()->getId() == User::$number)
        {
            $isInvited = true;
            break;
        }
    }

    // checks if currently logged account is member of this guild
    switch( Toolbox::guildAccess($guild) )
    {
        // leader, vice and member
        case 3:
            $isLeader = true;
        // vice and member
        case 2:
            $isVice = true;
        // just a member
        case 1:
            $isMember = true;
            break;
    }
}

// kicking user AJAX handler
if($isVice)
{
    $template->addJavaScript('guilds');
}

// members list
$table = $template->createComponent('TableList');
$table['caption'] = $guild->getName() . $language['Modules.Guilds.DisplayHeader'];
$table['id'] = 'membersTable';
$table->idPrefix = 'memberID_';

$members = array();

// loads members
foreach( $guild->getGuildRanks() as $rank)
{
    $first = true;

    foreach( $rank->getPlayers() as $player)
    {
        // kick link
        if($isVice)
        {
            $actions = XMLToolbox::createDocumentFragment();

            // edition link
            $edit = XMLToolbox::createElement('a');
            $edit->setAttribute('href', '/admin/module=Guilds&command=edit&id=' . $player->getId() );
            $edit->addContent($language['main.admin.EditSubmit']);
            $actions->addContent($edit);

            // leader can't be kicked
            if( $rank->getLevel() < 3)
            {
                $kick = XMLToolbox::createElement('a');
                $kick->setAttribute('href', '/admin/module=Guilds&command=kick&id=' . $player->getId() );
                $kick->setAttribute('onclick', 'if( confirm(\'' . $language['Modules.Guilds.ConfirmKick'] . '\') ) { return pageGuilds.kick(' . $row['id'] . '); } else { return false; }');
                $kick->addContent($language['Modules.Guilds.KickSubmit']);
                $actions->addContents(' | ', $kick);
            }
        }
        else
        {
            $actions = '';
        }

        // character view link
        $name = XMLToolbox::createDocumentFragment();
        $link = XMLToolbox::createElement('a');
        $link->setAttribute('href', '/characters/' . urlencode( $player->getName() ) );
        $link->addContent( $player->getName() );
        $name->addContent($link);

        // guild nick
        if( strlen( $player->getGuildNick() ) > 0)
        {
            $name->addContent(' (' . $player->getGuildNick() . ')');
        }

        // only first row of given rank will be labeled
        $members[] = array('id' => $row['id'], 'rank' => $first ? $rank->getName() : '', 'name' => $name, 'actions' => $actions);

        // marks current rank as done
        $first = false;
    }
}

$table->addField('rank', $language['Modules.Guilds.ListRank']);
$table->addField('name', $language['Modules.Guilds.ListMember']);

// leader management options
if($isVice)
{
    $table->addField('actions', $language['main.admin.Actions']);
}

$table['list'] = $members;

$links = array();

// management links

if($isLeader || User::hasAccess(3) )
{
    $links[] = array('link' => '/admin/module=Guilds&command=remove&id=' . $guild->getId(), 'label' => $language['main.admin.DeleteSubmit'], 'confirm' => $language['main.admin.ConfirmDelete']);
}

if($isVice)
{
    $links[] = array('link' => '/admin/module=Guilds&command=invite&id=' . $guild->getId(), 'label' => $language['Modules.Guilds.InviteSubmit']);
    $links[] = array('link' => '/admin/module=Guilds&command=manage&id=' . $guild->getId(), 'label' => $language['Modules.Guilds.ManageSubmit']);
}

if($isMember && !$isLeader)
{
    $links[] = array('link' => '/admin/module=Guilds&command=leave&id=' . $guild->getId(), 'label' => $language['Modules.Guilds.LeaveSubmit']);
}

if($isInvited)
{
    $links[] = array('link' => '/admin/module=Guilds&command=join&id=' . $guild->getId(), 'label' => $language['Modules.Guilds.JoinSubmit']);
}

if(!$isMember && User::$logged)
{
    $links[] = array('link' => '/admin/module=Guilds&command=request&id=' . $guild->getId(), 'label' => $language['Modules.Guilds.RequestSubmit']);
}

if( !empty($links) )
{
    $link = $template->createComponent('Links');
    $link['links'] = $links;
}

?>
