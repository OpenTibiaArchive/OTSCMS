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

// loads HTTP data
$name = InputData::read('name');

// loads spells.xml file
$spells = new DOMDocument();
$spells->load($config['directories.data'] . 'spells/spells.xml');

$data = $template->createComponent('LibraryPage');

// loops through loaded spells to find our spell
foreach( $spells->getElementsByTagName('*') as $spell)
{
    // checks if the formula matches query string and if spell is enabled
    // also only spells are supported - runes are items
    if( $spell->getAttribute('name') != $name || $spell->getAttribute('enabled') != 1)
    {
        continue;
    }

    // checks if we should display it
    // there has to be an image for that spell - that is the way how you can select which spells should be displayed
    if(!($extension = Toolbox::imageExists('Spells/' . $spell->getAttribute('name') ) ))
    {
        throw new HandledException('NotToDisplay');
    }

    $data['name'] = $spell->getAttribute('name');
    $data['words'] = $spell->getAttribute('words');
    $data['maglv'] = $spell->getAttribute('maglv');
    $data['mana'] = $spell->getAttribute('mana');
    $data['image'] = str_replace('\\', '/', $config['directories.images']) . 'Spells/' . $data['name'] . $extension;

    // finds all allowed vocations
    $vocations = array();
    foreach( $spell->getElementsByTagName('vocation') as $vocation)
    {
        $vocations[] = $vocation->getAttribute('name');
    }
    $data['vocations'] = implode(', ', $vocations);

    // we've fount it, dont have to loop next items
    break;
}

// puts spells and runes into template
$data['header'] = $language['Modules.Library.SpellInformation'];

// sets labels
$data->addLabel('words', $language['Modules.Library.SpellWords']);
$data->addLabel('maglv', $language['Modules.Library.SpellMLevel']);
$data->addLabel('mana', $language['Modules.Library.SpellMana']);
$data->addLabel('vocations', $language['Modules.Library.SpellVocations']);

?>
