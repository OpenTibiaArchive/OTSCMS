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

// loads spells.xml file
$spells = new DOMDocument();
$spells->load($config['directories.data'] . 'spells/spells.xml');

$list = $template->createComponent('ItemsList');
$list['header'] = $language['Modules.Library.SpellsList'];

$temp = array();

// loops through loaded spells
foreach( $spells->getElementsByTagName('*') as $spell)
{
    $name = $spell->getAttribute('name');

    // if spell isn't enabled then skip to next
    // and there has to be an image for that spell - that is the way how you can select which spells should be displayed
    if( $spell->getAttribute('enabled') != 1 || !Toolbox::imageExists('Spells/' . $name) )
    {
        continue;
    }

    $temp[$name] = $name;
}

// puts spells into template
$list['list'] = $temp;
$list['link'] = '/spells/';

?>
