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

$template->addJavaScript('msp');

// coords form
$form = $template->createComponent('AdminForm');
$form['action'] = '/admin/module=MSP&command=change';
$form['submit'] = $language['Modules.MSP.ChangeSubmit'];
$form['id'] = 'mspForm';

// from fields
$form->addField('', ComponentAdminForm::FieldSeparator, $language['Modules.MSP.From']);
$form->addField('from[x]', ComponentAdminForm::FieldText, $language['Modules.MSP.X']);
$form->addField('from[y]', ComponentAdminForm::FieldText, $language['Modules.MSP.Y']);
$form->addField('from[z]', ComponentAdminForm::FieldText, $language['Modules.MSP.Z']);

// to fields
$form->addField('', ComponentAdminForm::FieldSeparator, $language['Modules.MSP.To']);
$form->addField('to[x]', ComponentAdminForm::FieldText, $language['Modules.MSP.X']);
$form->addField('to[y]', ComponentAdminForm::FieldText, $language['Modules.MSP.Y']);
$form->addField('to[z]', ComponentAdminForm::FieldText, $language['Modules.MSP.Z']);

?>
