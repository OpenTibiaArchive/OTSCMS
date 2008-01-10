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

$news = array();

// reads last news from database
foreach( $db->query('SELECT `name`, `date_time`, `content` FROM [news] ORDER BY `date_time` DESC LIMIT ' . $config['site.news_limit']) as $record)
{
    $news[] = array('name' => Toolbox::languagePart($record['name'], $config['site.language']), 'content' => Toolbox::languagePart($record['content'], $config['site.language']), 'date_time' => date($config['site.date_format'], $record['date_time']) );
}

// news display component
$display = $template->createComponent('NewsList');
$display['list'] = $news;

// archive link
$link = $template->createComponent('Links');
$link['links'] = array( array('label' => $language['Modules.News.Archive'], 'link' => '/archive') );

?>
