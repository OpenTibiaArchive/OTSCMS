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

// javascript form handling
$template->addJavaScript('download');

// administration option
if( User::hasAccess(3) )
{
    $form = $template->createComponent('AdminForm');
    $form['action'] = '/admin/module=Download&command=insert';
    $form['submit'] = $language['main.admin.InsertSubmit'];
    $form['id'] = 'downloadForm';
    $form['enctype'] = 'multipart/form-data';

    // form fields
    $form->addField('download[name]', ComponentAdminForm::FieldText, $language['Modules.Download.Name']);
    $form->addField('download[content]', ComponentAdminForm::FieldArea, $language['Modules.Download.Content']);
    $form->addField('download[binary]', ComponentAdminForm::FieldRadio, $language['Modules.Download.Binary'], array('options' => array(0 => $language['Modules.Download.BinaryLink'], 1 => $language['Modules.Download.BinaryFile']), 'selected' => 0) );
    $form->addField('download[file]', ComponentAdminForm::FieldText, $language['Modules.Download.File']);
}

// objects list template
$list = $template->createComponent('ObjectsList');
$list['module'] = 'Download';

$download = array();

// parses HTML describes into XML trees
foreach( $db->query('SELECT `id`, `name`, `content` FROM [download]') as $row)
{
    $download[] = array('id' => $row['id'], 'name' => $row['name'], 'content' => XMLToolbox::inparse($row['content']) );
}

$list['list'] = $download;

?>
