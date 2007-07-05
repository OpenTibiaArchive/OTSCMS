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
    AJAX template component object.
*/

class TemplateComponent extends DataContainer
{
    // root template resource
    protected $owner;

    public function __construct(OTSTemplate $template)
    {
        $this->owner = $template;
    }

    // displays component
    public function display()
    {
        $component = XMLToolbox::createElement('component');

        // parses component data
        XMLToolbox::parseOut($this->data, $component);

        return $component;
    }

    // as after createComponent() this instance will be returned code can referr to custom components

    public function __call($method, $args)
    {
    }
}

?>
