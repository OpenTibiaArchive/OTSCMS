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
    List of objects.
*/

class ComponentItemsList extends TemplateComponent
{
    // displays component
    public function display()
    {
        // translation
        $language = OTSCMS::getResource('Language');

        $root = XMLToolbox::createDocumentFragment();

        // header
        $div = XMLToolbox::createElement('div');
        $div->setAttribute('class', 'contentHeader');
        $div->nodeValue = $this['header'];
        $root->appendChild($div);

        // list
        $ul = XMLToolbox::createElement('ul');
        $ul->setAttribute('id', 'page' . $this['module'] . 's');

        // list items
        foreach($this['list'] as $key => $item)
        {
            $li = XMLToolbox::createElement('li');

            // optional ID attribute
            if( isset($this['rowID']) )
            {
                $li->setAttribute('id', $this['rowID'] . $key);
            }

            // requires links
            if( isset($this['link']) )
            {
                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', $this['link'] . urlencode($key) );

                if( isset($this['rowID']) )
                {
                    $a->setAttribute('id', $this['rowID'] . $key . '_a');
                }

                $a->nodeValue = $item;

                $li->appendChild($a);
            }
            // simple element
            else
            {
                $li->nodeValue = $item;
            }

            // admin actions
            if($this->actions)
            {
                $li->appendChild( XMLToolbox::createTextNode(' ') );

                $a = XMLToolbox::createElement('a');
                $img = XMLToolbox::createElement('img');
                $a->setAttribute('href', 'admin.php?module=' . $this->module . '&command=edit&id=' . $key);
                $a->setAttribute('onclick', 'return page' . $this->module . '.edit(' . $key . ');');
                $img->setAttribute('alt', $language['main.admin.EditSubmit']);
                $img->setAttribute('src', $this->owner->getSkinPath() . 'images/edit.gif');
                $a->appendChild($img);
                $li->appendChild($a);

                $li->appendChild( XMLToolbox::createTextNode(' ') );

                $a = XMLToolbox::createElement('a');
                $img = XMLToolbox::createElement('img');
                $a->setAttribute('href', 'admin.php?module=' . $this->module . '&command=remove&id=' . $key);
                $a->setAttribute('onclick', 'if( confirm(Language[0]) ) { return page' . $this['module'] . '.remove(' . $key . '); } else { return false; }');
                $img->setAttribute('alt', $language['main.admin.DeleteSubmit']);
                $img->setAttribute('src', $this->owner->getSkinPath() . 'images/delete.gif');
                $a->appendChild($img);
                $li->appendChild($a);
            }

            $ul->appendChild($li);
        }

        $root->appendChild($ul);

        // outputs message block
        return XMLToolbox::saveXML($root);
    }

    // admin actions API

    public $actions = false;
}

?>
