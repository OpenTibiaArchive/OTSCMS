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
    Poll display component.
*/

class ComponentPollVoting extends TemplateComponent
{
    // displays component
    public function display()
    {
        // translation
        $language = OTSCMS::getResource('Language');

        // root layer
        $div = XMLToolbox::createElement('div');
        $div->setAttribute('id', 'pollRoot');

        // question describe
        $p = XMLToolbox::createElement('p');
        $p->setAttribute('class', 'indented');
        $p->nodeValue = $this['content'];
        $div->appendChild($p);

        // form table
        $table = XMLToolbox::createElement('table');
        $tbody = XMLToolbox::createElement('tbody');
        $table->setAttribute('id', 'pollTable');

        // voting form if allowed
        if($this['canVote'])
        {
            $root = XMLToolbox::createElement('form');
            $root->setAttribute('id', 'pollForm');
            $root->setAttribute('action', 'poll.php?command=vote');
            $root->setAttribute('method', 'post');
            $root->appendChild($table);
        }
        // otherwise table is our root element
        else
        {
            $root = $table;
        }

        $caption = XMLToolbox::createElement('caption');
        $caption->nodeValue = $this['name'];
        $table->appendChild($caption);

        // poll options
        foreach($this['options'] as $option)
        {
            // table row
            $row = XMLToolbox::createElement('tr');

            // label cell
            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formLeft');
            $td->nodeValue = $option['name'] . ': ';
            $row->appendChild($td);

            // field cell
            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formRight');

            // voting option
            if($this['canVote'])
            {
                $input = XMLToolbox::createElement('input');
                $input->setAttribute('type', 'radio');
                $input->setAttribute('name', 'id');
                $input->setAttribute('value', $option['id']);
                $td->appendChild($input);
                $td->setAttribute('id', 'pollOption_' . $option['id']);
            }
            // just display votes count
            else
            {
                $td->nodeValue = $option['count'];
            }

            $row->appendChild($td);
            $tbody->appendChild($row);
        }

        // voting submit
        if($this['canVote'])
        {
            $row = XMLToolbox::createElement('tr');
            $td = XMLToolbox::createElement('td');

            $input = XMLToolbox::createElement('input');
            $input->setAttribute('type', 'submit');
            $input->setAttribute('value', $language['Modules.Poll.VoteSubmit']);
            $td->appendChild($input);
            $td->setAttribute('colspan', '2');

            $row->appendChild($td);
            $row->setAttribute('id', 'pollSubmit');
            $tbody->appendChild($row);
        }

        $table->setAttribute('class', 'formTable');
        $table->appendChild($tbody);
        $div->appendChild($root);

        // outputs only table element
        echo XMLToolbox::saveXML($div);
    }
}

?>
