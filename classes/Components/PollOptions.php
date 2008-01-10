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
    Poll options edition form.
*/

class ComponentPollOptions extends TemplateComponent
{
    // displays component
    public function display()
    {
        // translation
        $language = OTSCMS::getResource('Language');

        // form table
        $table = XMLToolbox::createElement('table');
        $tbody = XMLToolbox::createElement('tbody');
        $table->setAttribute('id', 'optionsList');

        $caption = XMLToolbox::createElement('caption');
        $caption->addContent($language['Modules.Poll.EditOptions']);
        $table->addContent($caption);

        // form fields
        foreach($this['options'] as $id => $option)
        {
            // table row
            $row = XMLToolbox::createElement('tr');
            $row->setAttribute('id', 'optionID_' . $id);

            // field cell
            $td = XMLToolbox::createElement('td');
            $input = XMLToolbox::createElement('input');

            $td->setAttribute('class', 'formLeft');
            $input->setAttribute('id', 'optionValue_' . $id);
            $input->setAttribute('type', 'text');
            $input->setAttribute('value', $option);

            $td->addContent($input);
            $row->addContent($td);

            // control cell
            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formRight');

            $input = XMLToolbox::createElement('input');

            $input->setAttribute('type', 'button');
            $input->setAttribute('value', $language['main.admin.UpdateSubmit']);
            $input->setAttribute('onclick', 'return pageOptions.update(' . $id . ');');

            $td->addContent($input);

            $input = XMLToolbox::createElement('input');

            $input->setAttribute('type', 'button');
            $input->setAttribute('value', $language['main.admin.DeleteSubmit']);
            $input->setAttribute('onclick', 'if( confirm(Language[0]) ) { return pageOptions.remove(' . $id . '); } else { return false; }');

            $td->addContent($input);

            $row->addContent($td);
            $tbody->addContent($row);
        }

        // new option row
        $row = XMLToolbox::createElement('tr');
        $td = XMLToolbox::createElement('td');
        $input = XMLToolbox::createElement('input');

        $td->setAttribute('class', 'formLeft');
        $input->setAttribute('id', 'optionNew');
        $input->setAttribute('type', 'text');

        $td->addContent($input);
        $row->addContent($td);

        $td = XMLToolbox::createElement('td');
        $input = XMLToolbox::createElement('input');
        $td->setAttribute('class', 'formRight');
        $input->setAttribute('type', 'button');
        $input->setAttribute('value', $language['main.admin.InsertSubmit']);
        $input->setAttribute('onclick', 'return pageOptions.insert();');

        $td->addContent($input);
        $row->addContent($td);
        $tbody->addContent($row);

        $table->setAttribute('class', 'formTable');
        $table->addContent($tbody);

        // outputs only form element
        return XMLToolbox::saveXML($table);
    }
}

?>
