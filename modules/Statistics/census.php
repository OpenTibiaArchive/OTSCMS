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

// loads census statistics
$genderCensus = $db->query('SELECT `sex` AS `key`, COUNT(`id`) AS `value` FROM {players} GROUP BY `sex`')->fetchAll(PDO::FETCH_KEY_PAIR);
$vocationCensus = $db->query('SELECT `vocation` AS `key`, COUNT(`id`) AS `value` FROM {players} GROUP BY `vocation`')->fetchAll(PDO::FETCH_KEY_PAIR);

// prepares calculation variables
$genderCount = array_sum($genderCensus) / 100;
$vocationCount = array_sum($vocationCensus) / 100;

$vocations = array();

// creates vocations array
foreach( $ots->getVocationsList() as $id => $name)
{
    $vocations[$name] = (int) $vocationCensus[$id] . ' (' . @intval($vocationCensus[$id] / $vocationCount) . '%)';
}

// gender census table
// there could be 0 as a total number of characters
// so we have to controll division by zero error
$gender = $template->createComponent('TableData');
$gender['caption'] = $language['Modules.Statistics.CensusGender'];
$gender['data'] = array($language['main.gender0'] => (int) $genderCensus[0] . ' (' . @intval($genderCensus[0] / $genderCount) . '%)', $language['main.gender1'] => (int) $genderCensus[1] . ' (' . @intval($genderCensus[1] / $genderCount) . '%)');

// vocation census table
$vocation = $template->createComponent('TableData');
$vocation['caption'] = $language['Modules.Statistics.CensusVocation'];
$vocation['data'] = $vocations;

?>
