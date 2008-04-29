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

// composes list of instant spells
$list = $template->createComponent('ItemsList');
$list['header'] = $language['Modules.Library.InstantsList'];

$spells = $ots->getSpellsList();

$temp = array();

// loops through loaded spells
foreach( $spells->getInstantsList() as $spell)
{
    $spell = $spells->getInstant($spell);

    $name = $spell->name;

    // if spell isn't enabled then skip to next
    // and there has to be an image for that spell - that is the way how you can select which spells should be displayed
    if(!$spell->enabled || !Toolbox::imageExists('Spells/' . $name) )
    {
        continue;
    }

    $temp[$name] = $name;
}

// puts spells into template
$list['list'] = $temp;
$list['link'] = 'spells/instants/';

// composes list of rune spells
$list = $template->createComponent('ItemsList');
$list['header'] = $language['Modules.Library.RunesList'];

$temp = array();

// loops through loaded spells
foreach( $spells->getRunesList() as $spell)
{
    $spell = $spells->getRune($spell);

    $name = $spell->name;

    // if spell isn't enabled then skip to next
    // and there has to be an image for that spell - that is the way how you can select which spells should be displayed
    if(!$spell->enabled || !Toolbox::imageExists('Spells/' . $name) )
    {
        continue;
    }

    $temp[$name] = $name;
}

// puts spells into template
$list['list'] = $temp;
$list['link'] = 'spells/runes/';

// composes list of conjure spells
$list = $template->createComponent('ItemsList');
$list['header'] = $language['Modules.Library.ConjuresList'];

$temp = array();

// loops through loaded spells
foreach( $spells->getConjuresList() as $spell)
{
    $spell = $spells->getConjure($spell);

    $name = $spell->name;

    // if spell isn't enabled then skip to next
    // and there has to be an image for that spell - that is the way how you can select which spells should be displayed
    if(!$spell->enabled || !Toolbox::imageExists('Spells/' . $name) )
    {
        continue;
    }

    $temp[$name] = $name;
}

// puts spells into template
$list['list'] = $temp;
$list['link'] = 'spells/conjures/';

?>
