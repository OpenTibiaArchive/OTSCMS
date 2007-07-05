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
    Universal management listings object.
*/

class ComponentTableList extends TemplateComponent
{
    // displays component
    public function display()
    {
        // translation
        $language = OTSCMS::getResource('Language');

        // list table
        $table = XMLToolbox::createElement('table');
        $table->setAttribute('class', 'listTable');

        // optional ID attribute
        if( isset($this['id']) )
        {
            $table->setAttribute('id', $this['id']);
        }

        if( isset($this['caption']) )
        {
            $caption = XMLToolbox::createElement('caption');
            $caption->nodeValue = $this['caption'];
            $table->appendChild($caption);
        }

        // headers
        $thead = XMLToolbox::createElement('thead');
        $tr = XMLToolbox::createElement('tr');

        foreach($this->fields as $name => $label)
        {
            $th = XMLToolbox::createElement('th');
            $th->nodeValue = $label;
            $tr->appendChild($th);
        }

        // checks if there are actions for this table
        if( !empty($this->actions) )
        {
            $th = XMLToolbox::createElement('th');
            $th->nodeValue = $language['main.admin.Actions'];
            $tr->appendChild($th);
        }

        $thead->appendChild($tr);
        $table->appendChild($thead);

        $tbody = XMLToolbox::createElement('tbody');

        // table content
        foreach($this['list'] as $item)
        {
            // table row
            $row = XMLToolbox::createElement('tr');

            // row id
            if( !empty($this->idPrefix) )
            {
                $row->setAttribute('id', $this->idPrefix . $item['id']);
            }

            // row fields
            foreach($this->fields as $name => $label)
            {
                $td = XMLToolbox::createElement('td');
                $td->appendChild($item[$name] instanceof DOMNode ? $item[$name] : XMLToolbox::createTextNode( htmlspecialchars($item[$name]) ) );
                $row->appendChild($td);
            }

            // actions
            if( !empty($this->actions) )
            {
                $td = XMLToolbox::createElement('td');

                $next = false;

                // actions are referred by ID
                foreach($this->actions as $action => $label)
                {
                    // separator
                    if($next)
                    {
                        $td->appendChild( XMLToolbox::createTextNode(' | ') );
                    }

                    // action link
                    $a = XMLToolbox::createElement('a');
                    $a->setAttribute('href', 'admin.php?module=' . $this->module . '&command=' . $action . '&id=' . $item['id']);
                    // remove action needs confirmation
                    $a->setAttribute('onclick', $action == 'remove' || $action == 'pop' ? 'if( confirm(Language[0]) ) { return page' . $this->module . '.' . $action . '(' . $item['id'] . '); } else { return false; }' : 'return page' . $this->module . '.' . $action . '(' . $item['id'] . ');');
                    $a->nodeValue = $label;
                    $td->appendChild($a);

                    $next = true;
                }

                $row->appendChild($td);
            }

            $tbody->appendChild($row);
        }

        $table->appendChild($tbody);

        // outputs table
        return XMLToolbox::saveXML($table);
    }

    // table columns
    private $fields = array();

    // adds new field to form
    public function addField($name, $label)
    {
        $this->fields[$name] = $label;
    }

    // actions API

    private $actions = array();
    public $module;
    public $idPrefix;

    public function addAction($name, $label)
    {
        $this->actions[$name] = $label;
    }
}

?>
