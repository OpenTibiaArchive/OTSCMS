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

// ajax handlers of OTAdmin tools
$template->addJavaScript('otadmin');

// creation form
$form = $template->createComponent('AdminForm');
$form['action'] = 'admin/module=OTAdmin&command=insert';
$form['submit'] = $language['main.admin.InsertSubmit'];
$form['id'] = 'otadminForm';

// form fields
$form->addField('otadmin[name]', ComponentAdminForm::FieldText, $language['Modules.Online.Name']);
$form->addField('otadmin[content]', ComponentAdminForm::FieldText, $language['Modules.Online.Content']);
$form->addField('otadmin[port]', ComponentAdminForm::FieldText, $language['Modules.Online.Port']);
$form->addField('otadmin[password]', ComponentAdminForm::FieldText, $language['Modules.OTAdmin.Password']);

// restrictions list
$list = $template->createComponent('TableList');
$list['id'] = 'otadminsTable';
$list['caption'] = $language['Modules.OTAdmin.Caption'];
$list->addField('name', $language['Modules.Online.Name']);
$list->addField('content', $language['Modules.Online.Content']);
$list->addField('password', $language['Modules.OTAdmin.PasswordHeader']);
$list->addAction('remove', $language['main.admin.DeleteSubmit']);
$list->addAction('panel', $language['Modules.OTAdmin.PanelSubmit']);
$list->module = 'OTAdmin';
$list->idPrefix = 'otadminID_';

$otadmins = array();

foreach( $db->query('SELECT `id`, `name`, `content`, `port`, `password` FROM [otadmin]') as $otadmin)
{
    $otadmin['content'] = $otadmin['content'] . ':' . $otadmin['port'];
    $otadmin['password'] = empty($otadmin['password']) ? $language['Modules.OTAdmin.PasswordNo'] : $language['Modules.OTAdmin.PasswordYes'];

    // saves row in results
    $otadmins[] = $otadmin;
}

$list['list'] = $otadmins;

?>
