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

/*
    Forum trace path.
*/

class ComponentForumTrace extends TemplateComponent
{
    // displays component
    public function display()
    {
        // translation
        $language = OTSCMS::getResource('Language');

        $div = XMLToolbox::createElement('div');

        // to save path in reverse order
        $path = array();

        // loads all steps until root board
        while($this['id'])
        {
            $board = new CMS_Board($this['id']);
            $path[] = array('id' => $board['id'], 'name' => $board['name']);
            $this['id'] = $board['upperid'];
        }

        // first step
        $a = XMLToolbox::createElement('a');
        $a->setAttribute('href', 'forum/');
        $a->addContent($language['main.forum']);
        $div->addContent($a);

        // composes forum path
        foreach( array_reverse($path) as $step)
        {
            $a = XMLToolbox::createElement('a');
            $a->setAttribute('href', 'forum/' . $step['id']);
            $a->addContent($step['name']);
            $div->addContents(' ', XMLToolbox::createEntityReference('raquo'), ' ', $a);
        }

        // outputs trace links
        return XMLToolbox::saveXML($div);
    }
}

?>
