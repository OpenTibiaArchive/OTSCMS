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

$template->addJavaScript('guilds');

$guild = new OTS_Guild();
$guild->load( InputData::read('id') );
$access = Toolbox::guildAccess($guild);

// if not a gamemaster checks if user is a leader
if( !User::hasAccess(3) && $access < 2)
{
    throw new NoAccessException();
}

// for insertion form action
$js = $template->createComponent('RAW');
$js['content'] = '<script type="text/javascript">

GuildID = ' . $guild->id . ';

</script>';

// new rank form
$form = $template->createComponent('AdminForm');
$form['action'] = '/admin/module=Guilds&command=new&rank[guild_id]=' . $guild->id;
$form['submit'] = $language['Modules.Guilds.NewSubmit'];
$form['id'] = 'ranksForm';

$form->addField('rank[name]', ComponentAdminForm::FieldText, $language['Modules.Guilds.NewName']);
$form->addField('rank[level]', ComponentAdminForm::FieldRadio, $language['Modules.Guilds.NewLevel'], array('options' => array(1 => $language['Modules.Guilds.Level_1'], 2 => $language['Modules.Guilds.Level_2'], 3 => $language['Modules.Guilds.Level_3']) ) );

// current ranks table
$table = $template->createComponent('TableList');
$table['id'] = 'ranksList';
$table->addField('name', $language['Modules.Guilds.ListRank']);
$table->addField('actions', $language['main.admin.Actions']);
$table->idPrefix = 'rankID_';

$ranks = array();

// reads current guild ranks
foreach($guild as $rank)
{
    // skips leader rank if it's not leader on current account
    if($access == 2 && $rank->level == 3)
    {
        continue;
    }

    // deletion link
    $link = XMLToolbox::createElement('a');
    $link->setAttribute('href', '/admin/module=Guilds&command=delete&id=' . $rank->id);
    $link->setAttribute('onclick', 'if( confirm(\'' . $language['main.admin.ConfirmDelete'] . '\') ) { return pageGuilds.Delete(' . $rank->id . '); } else { return false; }');
    $link->addContent($language['main.admin.DeleteSubmit']);

    $ranks[] = array('id' => $rank->id, 'name' => $rank->name . ' (' . $language['Modules.Guilds.Level_' . $rank->level] . ')', 'actions' => $link);
}

$table['list'] = $ranks;

// membership requests
$list = $template->createComponent('TableList');
$list['id'] = 'requestsList';
$list->addField('name', $language['Modules.Character.Name']);
$list->addField('actions', $language['main.admin.Actions']);
$list->addAction('accept', $language['Modules.Guilds.AcceptSubmit']);
$list->addAction('reject', $language['Modules.Guilds.RejectSubmit']);
$list->idPrefix = 'requestID_';
$list->module = 'Guilds';

$requests = array();
new RequestsDriver($guild);

// reads membership requests
foreach($guild->requests as $player)
{
    $requests[$player->id] = $player->name;
}

$list['list'] = $requests;
Session::write('guild', $guild->id);

?>
