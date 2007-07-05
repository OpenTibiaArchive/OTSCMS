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
$name = basename( InputData::read('name') );

// loads monster file
$monster = new DOMDocument();
$monster->load($config['directories.data'] . 'monster/' . $name . '.xml');
$monster = $monster->firstChild;

// there has to be an image for that spell - that is the way how you can select which spells should be displayed
if($monster->nodeName != 'monster' || !($extension = Toolbox::imageExists('Monsters/' . $name) ))
{
    throw new HandledException('NotToDisplay');
}

$data = $template->createComponent('LibraryPage');
$data['header'] = $language['Modules.Library.MonsterInformation'];
$data['name'] = $monster->getAttribute('name');
$data['experience'] = $monster->getAttribute('experience');
$attacks = array();
$voices = array();
$defenses = array();
$loot = array();

// reads loot items
function lootItems(DOMElement $element)
{
    $items = array();

    // reads all sub-elements of tag
    foreach( $element->getElementsByTagName('*') as $tag)
    {
        switch($tag->nodeName)
        {
            // item tag is added to loot items
            case 'item':
                $items[] = $tag->getAttribute('id');
                // there is no break - go ahead
            // both item tag and inside tag can be searched
            case 'inside':
                $items += lootItems($tag);
                break;
        }
    }

    return $items;
}

// iterates all monster informations
foreach( $monster->getElementsByTagName('*') as $element)
{
    switch($element->nodeName)
    {
        case 'health':
            // monster's health
            $data['health'] = $element->getAttribute('max');
            break;

        case 'attacks':
            // help variables
            $distance = array();
            $instant = array();
            $rune = array();

            foreach( $element->getElementsByTagName('attack') as $attack)
            {
                switch( $attack->getAttribute('type') )
                {
                    case 'melee':
                        $attacks['melee'] = 'Melee';
                        break;

                    case 'distance':
                        $distance[] = $attack->getAttribute('name');
                        break;

                    case 'instant':
                        $instant[] = '<span style="font-style: italic;">' . $attack->getAttribute('name') . '</span>';
                        break;

                    case 'rune':
                        $rune[] = $attack->getAttribute('name');
                        break;
                }
            }

            // appends all attacks
            if( !empty($distance) )
            {
                $attacks[] = 'Distance ('.implode(', ', $distance).')';
            }
            if( !empty($instant) )
            {
                $attacks[] = 'Spells ('.implode(', ', $instant).')';
            }
            if( !empty($rune) )
            {
                $attacks[] = 'Runes ('.implode(', ', $rune).')';
            }
            break;

        case 'voices':
            foreach( $element->getElementsByTagName('voice') as $sound)
            {
                // sounds
                $voices[] = '<span style="font-style: italic;">&quot;' . $sound->getAttribute('sentence') . '&quot;</span>';
            }
            break;

        case 'defenses':
            foreach( $element->getElementsByTagName('defense') as $immunity)
            {
                // immunities
                $defenses[] = $immunity->getAttribute('immunity');
            }
            break;

        case 'loot':
            $names = array();

            // loads items.xml file
            foreach( new ItemsReader($config['directories.data'] . 'items/items.xml') as $id => $item)
            {
                $names[$id] = $item['name'];
            }

            $loot = lootItems($element);
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

            break;
    }
}

// puts informations into monsters data
$data['attacks'] = empty($attacks) ? '' : XMLToolbox::inparse( implode(', ', $attacks) );
$data['voices'] = empty($voices) ? '' : XMLToolbox::inparse( implode(', ', $voices) );
$data['defenses'] = implode(', ', $defenses);
$data['loot'] = implode(', ', $loot);
$data['image'] = str_replace('\\', '/', $config['directories.images']) . 'Monsters/' . $name . $extension;

// sets labels
$data->addLabel('experience', $language['Modules.Library.MonsterExperience']);
$data->addLabel('health', $language['Modules.Library.MonsterHealth']);
$data->addLabel('attacks', $language['Modules.Library.MonsterAttacks']);
$data->addLabel('voices', $language['Modules.Library.MonsterVoices']);
$data->addLabel('defenses', $language['Modules.Library.MonsterDefenses']);
$data->addLabel('loot', $language['Modules.Library.MonsterLoot']);

?>
