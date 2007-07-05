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
    Pagination bar.
*/

class ComponentPages extends TemplateComponent
{
    // displays component
    public function display()
    {
        // there are no pages
        if($this['pages'] < 2)
        {
            return;
        }

        // translation
        $language = OTSCMS::getResource('Language');

        $root = XMLToolbox::createElement('div');
        $root->setAttribute('class', 'pages');
        $root->addContent($language['Components.Pages.Pages'] . ': ');

        // page links
        for($i = 1; $i <= $this['pages']; $i++)
        {
            $span = XMLToolbox::createElement('span');

            // current page
            if($i == $this['page'])
            {
                $span->setAttribute('class', 'pagesCurrent');
                $span->addContent($i);
            }
            else
            {
                $span->setAttribute('class', 'pagesLink');

                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', $this['link'] . '&page=' . $i);
                $a->addContent($i);
                $span->addContent($a);
            }

            $root->addContent($span);
        }

        // outputs pagination bar
        return XMLToolbox::saveXML($root);
    }
}

?>
