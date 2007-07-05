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
    Tabelar data component.
*/

class ComponentTableData extends TemplateComponent
{
    // displays component
    public function display()
    {
        // form table
        $table = XMLToolbox::createElement('table');
        $tbody = XMLToolbox::createElement('tbody');

        // optional ID attribute
        if( isset($this['id']) )
        {
            $table->setAttribute('id', $this['id']);
        }

        // optional header
        if( isset($this['caption']) )
        {
            $caption = XMLToolbox::createElement('caption');
            $caption->nodeValue = $this['caption'];
            $table->appendChild($caption);
        }

        // form fields
        foreach($this['data'] as $label => $content)
        {
            // table row
            $row = XMLToolbox::createElement('tr');

            // label cell
            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formLeft');
            $td->nodeValue = $label . ': ';
            $row->appendChild($td);

            // field cell
            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formRight');

            // appends complete
            $td->appendChild($content instanceof DOMNode ? $content : XMLToolbox::createTextNode($content) );

            $row->appendChild($td);
            $tbody->appendChild($row);
        }

        $table->setAttribute('class', 'formTable');
        $table->appendChild($tbody);

        // outputs only table element
        echo XMLToolbox::saveXML($table);
    }
}

?>
