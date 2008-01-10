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
$template->addJavaScript('gallery');

// loads given image from database
$gallery = new CMS_Gallery( (int) InputData::read('id') );

// edition form
$form = $template->createComponent('AdminForm');
$form['action'] = '/admin/module=Gallery&command=update&id=' . $gallery['id'];
$form['submit'] = $language['main.admin.UpdateSubmit'];
$form['id'] = 'galleryForm';
$form['enctype'] = 'multipart/form-data';

// form fields
$form->addField('gallery[name]', ComponentAdminForm::FieldText, $language['Modules.Gallery.Name'], $gallery['name']);
$form->addField('gallery[content]', ComponentAdminForm::FieldArea, $language['Modules.Gallery.Content'], $gallery['content']);
$form->addField('gallery[binary]', ComponentAdminForm::FieldRadio, $language['Modules.Download.Binary'], array('options' => array(0 => $language['Modules.Download.BinaryLink'], 1 => $language['Modules.Download.BinaryFile']), 'selected' => $gallery['binary']) );
$form->addField('gallery[file]', $gallery['binary'] ? ComponentAdminForm::FieldFile : ComponentAdminForm::FieldText, $language['Modules.Gallery.File'], $gallery['file']);

// for AJAX compatibility
$form['data'] = $gallery;

?>
