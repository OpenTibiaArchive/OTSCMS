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

// ajax handlers of IP bans management
$template->addJavaScript('ipban');

// edition form
$form = $template->createComponent('AdminForm');
$form['action'] = '/admin/module=IPBan&command=insert';
$form['submit'] = $language['main.admin.InsertSubmit'];
$form['id'] = 'ipbanForm';

// form fields
$form->addField('ipban[ip]', ComponentAdminForm::FieldText, $language['Modules.IPBan.Name']);
$form->addField('ipban[mask]', ComponentAdminForm::FieldText, $language['Modules.IPBan.Mask']);

// bans list
$list = $template->createComponent('TableList');
$list['id'] = 'ipbansTable';
$list['caption'] = $language['Modules.IPBan.Caption'];
$list->addField('ip', $language['Modules.IPBan.Name']);
$list->addField('mask', $language['Modules.IPBan.Mask']);
$list->addField('actions', $language['main.admin.Actions']);

$ipbans = array();

// loads banned ips
foreach( $db->query('SELECT `ip`, `mask` FROM {bans} WHERE `type` = 1') as $ipban)
{
    // creates actions links
    $actions = XMLToolbox::createElement('a');
    $actions->setAttribute('href', '/admin/module=IPBan&command=remove&ipban[ip]=' . $ipban['ip'] . '&ipban[mask]=' . $ipban['mask']);
    $actions->setAttribute('onclick', 'if( confirm(Language[0]) ) { return pageIPBan.remove(' . $ipban['ip'] . ', ' . $ipban['mask'] . '); } else { return false; }');
    $actions->addContent($language['Modules.IPBan.Unban']);

    $ipbans[] = array('ip' => long2ip($ipban['ip']), 'mask' => long2ip($ipban['mask']), 'actions' => $actions);
}

$list['list'] = $ipbans;

?>
