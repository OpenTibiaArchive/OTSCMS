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
    Signup form.
*/

class ComponentSignup extends TemplateComponent
{
    // displays component
    public function display()
    {
        $div = XMLToolbox::createElement('div');

        // form elements
        $input = XMLToolbox::createElement('input');
        $input->setAttribute('type', 'text');
        $input->setAttribute('name', $this['name']);
        $input->setAttribute('id', $this['name']);

        $submit = XMLToolbox::createElement('input');
        $submit->setAttribute('type', 'submit');
        $submit->setAttribute('value', $this['submit']);

        $div->addContents($this['text'], XMLToolbox::createElement('br'), $input, ' ', $submit);

        // signup form
        $form = XMLToolbox::createElement('form');
        $form->setAttribute('action', $this['action']);
        $form->setAttribute('method', 'post');
        $form->setAttribute('onsubmit', $this['onsubmit']);
        $form->addContent($div);

        // outputs component XHTML
        return XMLToolbox::saveXML($form);
    }
}

?>
