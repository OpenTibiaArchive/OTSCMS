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

$guild = new OTS_Guild( (int) InputData::read('id') );

$isLeader = false;
$isVice = User::hasAccess(3);
$isMember = false;
$isInvited = false;

// loads invited list
if(User::$logged)
{
    $isInvited = $db->query('SELECT COUNT([invites].`id`) AS `count` FROM [invites], {players} WHERE [invites].`content` = ' . $guild['id'] . ' AND [invites].`name` = {players}.`id` AND {players}.`account_id` = ' . User::$number)->fetch();
    $isInvited = $isInvited['count'] > 0;

    // checks if currently logged account is member of this guild
    switch( Toolbox::guildAccess($guild['id'], User::$number) )
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
$table['caption'] = $guild['name'] . $language['Modules.Guilds.DisplayHeader'];
$table['id'] = 'membersTable';
$table->idPrefix = 'memberID_';

$members = array();
$done = array();

// loads members
foreach( $db->query('SELECT `id`, `name`, `guildnick`, `rank`, `rank_id`, `level` FROM [guild_members] WHERE `guild_id` = ' . $guild['id'] . ' ORDER BY `level` DESC') as $row)
{
    // leader can't be kicked
    if($isVice)
    {
        $actions = XMLToolbox::createDocumentFragment();

        // edition link
        $edit = XMLToolbox::createElement('a');
        $edit->setAttribute('href', 'guild.php?command=edit&id=' . $row['id']);
        $edit->nodeValue = $language['main.admin.EditSubmit'];
        $actions->appendChild($edit);

        // kick link
        if($row['level'] < 3)
        {
            $kick = XMLToolbox::createElement('a');
            $kick->setAttribute('href', 'guild.php?command=kick&id=' . $row['id']);
            $kick->setAttribute('onclick', 'if( confirm(\'' . $language['Modules.Guilds.ConfirmKick'] . '\') ) { return pageGuilds.kick(' . $row['id'] . '); } else { return false; }');
            $kick->nodeValue = $language['Modules.Guilds.KickSubmit'];
            $actions->appendChild( XMLToolbox::createTextNode(' | ') );
            $actions->appendChild($kick);
        }
    }
    else
    {
        $actions = '';
    }

    // character view link
    $name = XMLToolbox::createDocumentFragment();
    $link = XMLToolbox::createElement('a');
    $link->setAttribute('href', 'character.php?name=' . urlencode($row['name']) );
    $link->nodeValue = $row['name'];
    $name->appendChild($link);

    // guild nick
    if( !empty($row['guildnick']) )
    {
        $name->appendChild( XMLToolbox::createTextNode(' (' . $row['guildnick'] . ')') );
    }

    // only first row of given rank will be labeled
    $members[] = array('id' => $row['id'], 'rank' => $done[ $row['rank_id'] ] ? '' : $row['rank'], 'name' => $name, 'actions' => $actions);

    // marks current rank as done
    $done[ $row['rank_id'] ] = true;
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
    $links[] = array('link' => 'guild.php?command=remove&id=' . $guild['id'], 'label' => $language['main.admin.DeleteSubmit'], 'confirm' => $language['main.admin.ConfirmDelete']);
}

if($isVice)
{
    $links[] = array('link' => 'guild.php?command=invite&id=' . $guild['id'], 'label' => $language['Modules.Guilds.InviteSubmit']);
    $links[] = array('link' => 'guild.php?command=manage&id=' . $guild['id'], 'label' => $language['Modules.Guilds.ManageSubmit']);
}

if($isMember && !$isLeader)
{
    $links[] = array('link' => 'guild.php?command=leave&id=' . $guild['id'], 'label' => $language['Modules.Guilds.LeaveSubmit']);
}

if($isInvited)
{
    $links[] = array('link' => 'guild.php?command=join&id=' . $guild['id'], 'label' => $language['Modules.Guilds.JoinSubmit']);
}

if(!$isMember && User::$logged)
{
    $links[] = array('link' => 'guild.php?command=request&id=' . $guild['id'], 'label' => $language['Modules.Guilds.RequestSubmit']);
}

if( !empty($links) )
{
    $link = $template->createComponent('Links');
    $link['links'] = $links;
}

?>
