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

$template->addJavaScript('statistics');

// HTTP variables
$list = InputData::read('list');
$page = InputData::read('page');

// kills ids
$skills = array('fist' => 0, 'club' => 1, 'sword' => 2, 'axe' => 3, 'distance' => 4, 'shielding' => 5, 'fishing' => 6);

// checks if the given mode is the valid list type
if( !in_array($list, array(7 => 'experience', 'maglevel') + $skills) )
{
    $list = 'experience';
}

// experience statistics
if( in_array($list, array('experience', 'maglevel') ) )
{
    OTSCMS::call('Statistics', 'highscores');
    return;
}

$limit = $config['statistics.page'];

// reads count of all reocrds
$pages = $db->query('SELECT COUNT(`id`) AS `count` FROM [player_skills] WHERE `skillid` = ' . $skills[$list])->fetch();
$pages = ceil($pages['count'] / $limit);

// checks if the site is valid
$page = $page < 0 ? 0 : ($page > $pages - 1 ? $pages - 1 : $page);

$pager = $template->createComponent('StatisticsPager');
$pager['list'] = $list;
$pager['page'] = $page;

// generates links
$pager['left'] = array('show' => $page > 0, 'from' => ($page - 1) * $limit + 1, 'to' => $page * $limit);
$pager['right'] = array('show' => $page < $pages - 1, 'from' => ($page + 1) * $limit + 1, 'to' => ($page + 2) * $limit);

$scores = array();

// reads top scores from given range
$i = $page * $limit;

// reads top scores from given range
// we must use fetchAll() to make use of SQLite_Results wrapper quote stripping
foreach( $db->query('SELECT `name`, `value` FROM [player_skills] WHERE `skillid` = ' . $skills[$list] . ' LIMIT ' . $limit . ' OFFSET ' . ($page * $limit) ) as $row)
{
    $scores[++$i] = $row;
}

$pager['scores'] = $scores;

?>
