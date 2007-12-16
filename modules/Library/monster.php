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

// protection from hackers
$name = InputData::read('name');

// loads monster file
$monster = $ots->getMonster($name);

// there has to be an image for that spell - that is the way how you can select which spells should be displayed
if(!($extension = Toolbox::imageExists('Monsters/' . $name) ))
{
    throw new HandledException('NotToDisplay');
}

$voices = $monster->getVoices();

// composes quotes
foreach($voices as $index => $voice)
{
    $voices[$index] = '<span style="font-style: italic;">&quot;' . $voice . '&quot;</span>';
}

$loot = $monster->getLoot();

if( !empty($loot) )
{
    $names = array();

    // loads items.xml file
    $cache = new ItemsCache($db);
    $items = new OTS_ItemsList();
    $items->setCacheDriver($cache);
    $items->loadItems($config['directories.data'] . 'items');

    foreach($items as $item)
    {
        $names[ $id->getId() ] = $item->getName();
    }

    // replaces ids by names
    foreach($loot as $index => $item)
    {
        // checks if there is name for such item
        if( isset($names[$item]) )
        {
            $loot[$index] = $names[$item];
        }
        // otherwise hide that item
        else
        {
            unset($loot[$index]);
        }
    }
}

$defenses = array_merge( $monster->getDefenses(), $monster->getImmunities() );

foreach($defenses as $index => $defense)
{
    $defemses[$index] = ucfirst($defense);
}

$attacks = $monster->getAttacks();

foreach($attacks as $index => $attack)
{
    $attacks[$index] = ucfirst($attack);
}

// puts informations into monsters data
$data = $template->createComponent('LibraryPage');
$data['header'] = $language['Modules.Library.MonsterInformation'];
$data['name'] = $monster->getName();
$data['experience'] = $monster->getExperience();
$data['health'] = $monster->getHealth();
$data['voices'] = empty($voices) ? '' : XMLToolbox::inparse( implode(', ', $voices) );
$data['defenses'] = implode(', ', $defenses);
$data['attacks'] = empty($attacks) ? '' : XMLToolbox::inparse( implode(', ', $attacks) );
$data['loot'] = implode(', ', $loot);
$data['image'] = '/' . str_replace('\\', '/', $config['directories.images']) . 'Monsters/' . $name . $extension;

// sets labels
$data->addLabel('experience', $language['Modules.Library.MonsterExperience']);
$data->addLabel('health', $language['Modules.Library.MonsterHealth']);
$data->addLabel('attacks', $language['Modules.Library.MonsterAttacks']);
$data->addLabel('voices', $language['Modules.Library.MonsterVoices']);
$data->addLabel('defenses', $language['Modules.Library.MonsterDefenses']);
$data->addLabel('loot', $language['Modules.Library.MonsterLoot']);

?>
