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

/*
    Simple links component.
*/

class ComponentLinks extends TemplateComponent
{
    // displays component
    public function display()
    {
        // composes link layer
        $div = XMLToolbox::createElement('div');
        $div->setAttribute('class', 'right');

        // links separated by paragraphs
        if( count($this['links']) == 1)
        {
            $link = current($this['links']);
            $p = XMLToolbox::createElement('a');
            $p->setAttribute('href', $link['link']);

            // confirmation question
            if( isset($link['confirm']) )
            {
                $p->setAttribute('onclick', 'return confirm(\'' . $link['confirm'] . '\');');
            }

            $p->addContent($link['label']);
        }
        // single link
        else
        {
            $p = XMLToolbox::createDocumentFragment();

            foreach($this['links'] as $link)
            {
                $tag = XMLToolbox::createElement('p');
                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', $link['link']);

                // confirmation question
                if( isset($link['confirm']) )
                {
                    $a->setAttribute('onclick', 'return confirm(\'' . $link['confirm'] . '\');');
                }

                $a->addContent($link['label']);
                $tag->addContent($a);
                $p->addContent($tag);
            }
        }

        $div->addContent($p);

        // outputs only form element
        return XMLToolbox::saveXML($div);
    }
}

?>
