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

// pre-loads HTTP data
$news = InputData::read('news');

// loads news
$row = new CMS_News( (int) InputData::read('id') );

$names = array();

// connects all titles into one
foreach($news['name'] as $translation => $name)
{
    $names[] = '__' . $translation . '__' . $name;
}

$contents = array();

// does same with news contents
foreach($news['content'] as $translation => $content)
{
    $contents[] = '__' . $translation . '__' . $content;
}

// updates news and saves it in database
$row['name'] = implode('<![_lang_]!>', $names);
$row['content'] = implode('<![_lang_]!>', $contents);
$row->save();

// there is nothing to display
// redirects internaly to management page
OTSCMS::call('News', 'manage');

?>
