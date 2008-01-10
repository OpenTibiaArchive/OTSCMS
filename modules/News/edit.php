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

// gets id from URL or form
$news = new CMS_News( (int) InputData::read('id') );

// new news form
$form = $template->createComponent('AdminForm');
$form['action'] = 'admin/module=News&command=update&id=' . $news['id'];
$form['submit'] = $language['main.admin.UpdateSubmit'];
$form['id'] = 'newsForm';

$data = array('name' => array(), 'content' => array() );

foreach($template['languages'] as $translation)
{
    $file = new OTSTranslation($config['directories.languages'] . $translation . '/');

    $name = Toolbox::languagePart($news['name'], $translation);
    $content = Toolbox::languagePart($news['content'], $translation);

    // given translation fields
    $form->addField('', ComponentAdminForm::FieldSeparator, $translation);
    $form->addField('news[name][' . $translation . ']', ComponentAdminForm::FieldText, $file['Modules.News.Name'], $name);
    $form->addField('news[content][' . $translation . ']', ComponentAdminForm::FieldArea, $file['Modules.News.Content'], $content);

    $data['name'][$translation] = $name;
    $data['content'][$translation] = $content;
}

// for AJAX compatibility
$form['data'] = $data;

?>
