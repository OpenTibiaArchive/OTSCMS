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

// loads server
$online = new CMS_Online( (int) InputData::read('id') );

// edition form
$form = $template->createComponent('AdminForm');
$form['action'] = 'admin/module=Online&command=update&id=' . $online['id'];
$form['submit'] = $language['main.admin.UpdateSubmit'];
$form['id'] = 'onlineForm';

// form fields
$form->addField('online[name]', ComponentAdminForm::FieldText, $language['Modules.Online.Name'], $online['name']);
$form->addField('online[content]', ComponentAdminForm::FieldText, $language['Modules.Online.Content'], $online['content']);
$form->addField('online[port]', ComponentAdminForm::FieldText, $language['Modules.Online.Port'], $online['port']);

// for AJAX compatibility
$form['data'] = $online;

?>
