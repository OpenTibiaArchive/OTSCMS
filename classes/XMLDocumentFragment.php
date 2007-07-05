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
    DOMDocumentFragment additional routines.
*/

class XMLDocumentFragment extends DOMDocumentFragment
{
    // adds content into element
    public function addContent($content)
    {
        $this->appendChild($content instanceof DOMNode ? ( $this->ownerDocument->isSameNode($content->ownerDocument) ? $content : $this->ownerDocument->importNode($content, true) ) : $this->ownerDocument->createTextNode($content) );
    }

    // adds many pieces of content
    public function addContents()
    {
        foreach( func_get_args() as $content)
        {
            $this->addContent($content);
        }
    }
}

?>
