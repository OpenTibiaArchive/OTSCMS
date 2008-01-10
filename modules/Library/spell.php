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

// loads HTTP data
$name = InputData::read('name');
$type = InputData::read('type');

switch($type)
{
    // instant spell
    case OTS_SpellsList::SPELL_INSTANT:
        $spell = $ots->getInstant($name);
        break;

    // rune spell
    case OTS_SpellsList::SPELL_RUNE:
        $spell = $ots->getRune($name);
        break;

    // conjure spell
    case OTS_SpellsList::SPELL_CONJURE:
        $spell = $ots->getConjure($name);
        break;

    default:
        throw new HandledException('NotToDisplay');
}

// checks if spell is enabled
if(!$spell->enabled)
{
    throw new HandledException('NotToDisplay');
}

// checks if we should display it
// there has to be an image for that spell - that is the way how you can select which spells should be displayed
if(!($extension = Toolbox::imageExists('Spells/' . $spell->name) ))
{
    throw new HandledException('NotToDisplay');
}

$data = $template->createComponent('LibraryPage');

$data['name'] = $spell->name;
$data['words'] = $spell->words;
$data['maglv'] = $spell->magicLevel;
$data['mana'] = $spell->mana;
$data['soul'] = $spell->soul;
$data['image'] = '/' . str_replace('\\', '/', $config['directories.images']) . 'Spells/' . $data['name'] . $extension;

// finds all allowed vocations
$data['vocations'] = implode(', ', $spell->vocations);

// puts spells and runes into template
$data['header'] = $language['Modules.Library.SpellInformation'];

// sets labels
$data->addLabel('words', $language['Modules.Library.SpellWords']);
$data->addLabel('maglv', $language['Modules.Library.SpellMLevel']);
$data->addLabel('mana', $language['Modules.Library.SpellMana']);
$data->addLabel('soul', $language['Modules.Library.SpellSoul']);
$data->addLabel('vocations', $language['Modules.Library.SpellVocations']);

?>
