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
    Simple message object.
*/

class ComponentMessage extends TemplateComponent
{
    // displays component
    public function display()
    {
        // message part
        $message = XMLToolbox::createDocumentFragment();

        // first pharagraph
        $p = XMLToolbox::createElement('p');
        $p->appendChild($this['message'] instanceof DOMNode ? $this['message'] : XMLToolbox::createTextNode($this['message']) );
        $message->appendChild($p);

        // optional debug info
        if( isset($this['debug']) )
        {
            $pre = XMLToolbox::createElement('pre');
            $pre->nodeValue = htmlspecialchars($this['debug']);
            $message->appendChild($pre);
        }

        // optional place info
        if( isset($this['place']) )
        {
            $p = XMLToolbox::createElement('p');
            $p->appendChild($this['place'] instanceof DOMNode ? $this['place'] : XMLToolbox::createTextNode($this['place']) );
            $message->appendChild($p);
        }

        // outputs message block
        return XMLToolbox::saveXML($message);
    }
}

?>
