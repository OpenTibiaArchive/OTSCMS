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

$template->addJavaScript('news');

// new news form
$form = $template->createComponent('AdminForm');
$form['action'] = 'admin/module=News&command=insert';
$form['submit'] = $language['main.admin.InsertSubmit'];
$form['id'] = 'newsForm';

foreach($template['languages'] as $translation)
{
    $file = new OTSTranslation($config['directories.languages'] . $translation . '/');

    // given translation fields
    $form->addField('', ComponentAdminForm::FieldSeparator, $translation);
    $form->addField('news[name][' . $translation . ']', ComponentAdminForm::FieldText, $file['Modules.News.Name']);
    $form->addField('news[content][' . $translation . ']', ComponentAdminForm::FieldArea, $file['Modules.News.Content']);
}

// news list
$list = $template->createComponent('TableList');
$list['id'] = 'newssTable';
$list['caption'] = $language['Modules.News.Caption'];
$list->addField('name', $language['Modules.News.Name']);
$list->addField('date_time', $language['Modules.News.DateTime']);
$list->addAction('remove', $language['main.admin.DeleteSubmit']);
$list->addAction('edit', $language['main.admin.EditSubmit']);
$list->module = 'News';
$list->idPrefix = 'newsID_';

$news = array();

// reads news translations
foreach( $db->query('SELECT `id`, `name`, `date_time` FROM [news]') as $record)
{
    $news[] = array('id' => $record['id'], 'name' => Toolbox::languagePart($record['name'], $config['site.language']), 'date_time' => date($config['site.date_format'], $record['date_time']) );
}

$list['list'] = $news;

?>
