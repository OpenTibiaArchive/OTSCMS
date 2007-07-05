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
    Library object.
*/

class ComponentLibraryPage extends TemplateComponent
{
    // displays component
    public function display()
    {
        $root = XMLToolbox::createDocumentFragment();

        // header
        $div = XMLToolbox::createElement('div');
        $div->setAttribute('class', 'contentHeader');
        $div->addContent($this['header']);
        $root->addContent($div);

        // image
        $image = XMLToolbox::createElement('img');
        $image->setAttribute('style', 'border: 1px solid #000000;');
        $image->setAttribute('src', $this['image']);
        $image->setAttribute('alt', $this['name']);
        $div = XMLToolbox::createElement('div');
        $div->setAttribute('style', 'width: 100%; text-align: right;');
        $div->addContent($image);
        $root->addContent($div);

        // name
        $p = XMLToolbox::createElement('p');
        $p->setAttribute('style', 'font-weight: bold;');
        $p->addContent($this['name']);
        $root->addContent($p);

        // labels
        foreach($this->labels as $field => $label)
        {
            $p = XMLToolbox::createElement('p');
            $bold = XMLToolbox::createElement('span');
            $bold->setAttribute('style', 'font-weight: bold;');
            $bold->addContent($label . ':');
            $p->addContents($bold, ' ', $this[$field]);
            $root->addContent($p);
        }

        // outputs message block
        return XMLToolbox::saveXML($root);
    }

    // infos
    private $labels = array();

    public function addLabel($field, $label)
    {
        $this->labels[$field] = $label;
    }
}

?>
