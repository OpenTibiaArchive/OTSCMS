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

// file mode handler
$template->addJavaScript('download');

// loads given file from database
$download = new CMS_Download( (int) InputData::read('id') );

// edition form
$form = $template->createComponent('AdminForm');
$form['action'] = 'admin/module=Download&command=update&id=' . $download['id'];
$form['submit'] = $language['main.admin.UpdateSubmit'];
$form['id'] = 'downloadForm';
$form['enctype'] = 'multipart/form-data';

// form fields
$form->addField('download[name]', ComponentAdminForm::FieldText, $language['Modules.Download.Name'], $download['name']);
$form->addField('download[content]', ComponentAdminForm::FieldArea, $language['Modules.Download.Content'], $download['content']);
$form->addField('download[binary]', ComponentAdminForm::FieldRadio, $language['Modules.Download.Binary'], array('options' => array(0 => $language['Modules.Download.BinaryLink'], 1 => $language['Modules.Download.BinaryFile']), 'selected' => $download['binary']) );
$form->addField('download[file]', $download['binary'] ? ComponentAdminForm::FieldFile : ComponentAdminForm::FieldText, $language['Modules.Download.File'], $download['file']);

// for AJAX compatibility
$form['data'] = $download;

?>
