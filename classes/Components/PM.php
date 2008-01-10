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
    Private message.
*/

class ComponentPM extends TemplateComponent
{
    // displays component
    public function display()
    {
        // translation
        $language = OTSCMS::getResource('Language');
        $config = OTSCMS::getResource('Config');

        // composes form
        $form = XMLToolbox::createElement('form');

        // main attributes
        $form->setAttribute('action', $this['action']);
        $form->setAttribute('method', 'post');

        // form table
        $table = XMLToolbox::createElement('table');
        $tbody = XMLToolbox::createElement('tbody');

        // from
        $row = XMLToolbox::createElement('tr');
        $td = XMLToolbox::createElement('td');
        $td->setAttribute('colspan', '2');
        $td->setAttribute('class', 'formLeft');
        $td->addContent($language['Modules.PM.From'] . ': ');
        $row->addContent($td);

        $td = XMLToolbox::createElement('td');
        $a = XMLToolbox::createElement('a');
        $a->setAttribute('href', 'characters/' . $this['pm']['from']);
        $a->addContent($this['pm']['from']);
        $td->setAttribute('colspan', '2');
        $td->setAttribute('class', 'formRight');
        $td->addContent($a);
        $row->addContent($td);
        $tbody->addContent($row);

        // to
        $row = XMLToolbox::createElement('tr');
        $td = XMLToolbox::createElement('td');
        $td->setAttribute('colspan', '2');
        $td->setAttribute('class', 'formLeft');
        $td->addContent($language['Modules.PM.To'] . ': ');
        $row->addContent($td);

        $td = XMLToolbox::createElement('td');
        $a = XMLToolbox::createElement('a');
        $a->setAttribute('href', 'characters/' . $this['pm']['to']);
        $a->addContent($this['pm']['to']);
        $td->setAttribute('colspan', '2');
        $td->setAttribute('class', 'formRight');
        $td->addContent($a);
        $row->addContent($td);
        $tbody->addContent($row);

        // subject
        $row = XMLToolbox::createElement('tr');
        $td = XMLToolbox::createElement('td');
        $td->setAttribute('colspan', '2');
        $td->setAttribute('class', 'formLeft');
        $td->addContent($language['Modules.PM.Name'] . ': ');
        $row->addContent($td);

        $td = XMLToolbox::createElement('td');
        $a = XMLToolbox::createElement('a');
        $td->setAttribute('colspan', '2');
        $td->setAttribute('class', 'formRight');
        $td->addContent($this['pm']['name']);
        $row->addContent($td);
        $tbody->addContent($row);

        // avatar
        $row = XMLToolbox::createElement('tr');
        $td = XMLToolbox::createElement('td');

        if($this->receiver)
        {
            $td->setAttribute('rowspan', '2');
        }

        if($this['pm']['avatar'])
        {
            $img = XMLToolbox::createElement('img');
            $img->setAttribute('src', $this['pm']['avatar']);
            $img->setAttribute('alt', $this['pm']['from']);
            $td->addContents($img, XMLToolbox::createElement('br') );
        }

        $td->addContent( date($config['site.date_format'], $this['pm']['date_time']) );
        $td->setAttribute('class', 'formLeft');
        $row->addContent($td);

        $td = XMLToolbox::createElement('td');
        $a = XMLToolbox::createElement('a');
        $td->setAttribute('colspan', '3');
        $td->addContent($this['pm']['content']);
        $row->addContent($td);
        $tbody->addContent($row);

        // action links
        if($this->receiver)
        {
            $delete = XMLToolbox::createElement('a');
            $delete->setAttribute('href', 'message/' . $this['pm']['id'] . '/delete');
            $delete->addContent($language['main.admin.DeleteSubmit']);

            $reply = XMLToolbox::createElement('a');
            $reply->setAttribute('href', 'message/' . $this['pm']['id'] . '/reply');
            $reply->addContent($language['Modules.PM.ReplySubmit']);

            $forward = XMLToolbox::createElement('a');
            $forward->setAttribute('href', 'message/' . $this['pm']['id'] . '/forward');
            $forward->addContent($language['Modules.PM.ForwardSubmit']);

            $row = XMLToolbox::createElement('tr');
            $td = XMLToolbox::createElement('td');
            $a = XMLToolbox::createElement('a');
            $td->setAttribute('colspan', '3');
            $td->setAttribute('class', 'right');

            $td->addContents($delete, ' | ', $reply, ' | ', $forward);

            $row->addContent($td);
            $tbody->addContent($row);
        }

        $table->setAttribute('class', 'formTable');
        $table->addContent($tbody);
        $form->addContent($table);

        // outputs only form element
        return XMLToolbox::saveXML($form);
    }

    // message owner mark
    public $receiver = false;
}

?>
