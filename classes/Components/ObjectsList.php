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
    Extended list of objects.
*/

class ComponentObjectsList extends TemplateComponent
{
    // displays component
    public function display()
    {
        // translation
        $language = OTSCMS::getResource('Language');

        $root = XMLToolbox::createDocumentFragment();

        // list items
        foreach($this['list'] as $item)
        {
            // object layer
            $div = XMLToolbox::createElement('div');
            $div->setAttribute('id', strtolower($this['module']) . 'ID_' . $item['id']);

            // object title
            $header = XMLToolbox::createElement('div');
            $header->setAttribute('class', 'contentHeader');
            $header->appendChild( XMLToolbox::createTextNode($item['name']) );

            // admin controll links
            if( User::hasAccess(3) )
            {
                $header->appendChild( XMLToolbox::createTextNode(' ') );

                $a = XMLToolbox::createElement('a');
                $img = XMLToolbox::createElement('img');
                $a->setAttribute('href', $this['file'] . '?command=edit&id=' . $item['id']);
                $a->setAttribute('onclick', 'return page' . $this['module'] . '.edit(' . $item['id'] . ');');
                $img->setAttribute('alt', $language['main.admin.EditSubmit']);
                $img->setAttribute('src', $this->owner->getSkinPath() . 'images/edit.gif');
                $a->appendChild($img);
                $header->appendChild($a);

                $header->appendChild( XMLToolbox::createTextNode(' ') );

                $a = XMLToolbox::createElement('a');
                $img = XMLToolbox::createElement('img');
                $a->setAttribute('href', $this['file'] . '?command=remove&id=' . $item['id']);
                $a->setAttribute('onclick', 'if( confirm(Language[0]) ) { return page' . $this['module'] . '.remove(' . $item['id'] . '); } else { return false; }');
                $img->setAttribute('alt', $language['main.admin.DeleteSubmit']);
                $img->setAttribute('src', $this->owner->getSkinPath() . 'images/delete.gif');
                $a->appendChild($img);
                $header->appendChild($a);
            }

            $div->appendChild($header);

            // describe + mini image
            if( isset($this['mini']) )
            {
                $layer = XMLToolbox::createElement('div');
                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', $this['file'] . '?command=download&id=' . $item['id']);
                $img = XMLToolbox::createElement('img');
                $img->setAttribute('src', $this['file'] . '?command=' . $this['mini'] . '&id=' . $item['id']);
                $img->setAttribute('alt', $item['name']);
                $img->setAttribute('class', 'galleryMini');
                $a->appendChild($img);
                $layer->appendChild($a);
            }
            // standard describtion
            else
            {
                $layer = XMLToolbox::createElement('p');
                $layer->setAttribute('class', 'indented');
            }

            // describe
            $layer->appendChild($item['content'] instanceof DOMNode ? $item['content'] : XMLToolbox::createTextNode($item['content']) );
            $div->appendChild($layer);

            // download link layer
            $download = XMLToolbox::createElement('div');
            $download->setAttribute('class', 'right');
            $a = XMLToolbox::createElement('a');
            $a->setAttribute('href', $this['file'] . '?command=download&id=' . $item['id']);
            $a->nodeValue = $language['Modules.' . $this['module'] . '.DownloadSubmit'];

            $download->appendChild($a);
            $div->appendChild($download);

            $root->appendChild($div);
        }

        // outputs message block
        return XMLToolbox::saveXML($root);
    }
}

?>
