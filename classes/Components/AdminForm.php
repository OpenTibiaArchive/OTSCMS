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
    Universal administration panel edition form component object.
*/

class ComponentAdminForm extends TemplateComponent
{
    // displays component
    public function display()
    {
        // translation
        $language = OTSCMS::getResource('Language');

        // composes form
        $form = XMLToolbox::createElement('form');

        // main attributes
        $form->setAttribute('action', $this['action']);
        $form->setAttribute('method', 'post');

        // optional ID attribute
        if( isset($this['id']) )
        {
            $form->setAttribute('id', $this['id']);
        }

        // for files upload
        if( isset($this['enctype']) )
        {
            $form->setAttribute('enctype', $this['enctype']);
        }

        // form table
        $table = XMLToolbox::createElement('table');
        $tbody = XMLToolbox::createElement('tbody');

        // form fields
        foreach($this->fields as $field)
        {
            // table row
            $row = XMLToolbox::createElement('tr');

            // label cell
            if($field['type'] != self::FieldSeparator)
            {
                $td = XMLToolbox::createElement('td');
                $td->setAttribute('class', 'formLeft');
                $td->nodeValue = $field['label'] . ': ';
                $row->appendChild($td);
            }

            // field cell
            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formRight');

            // form field
            switch($field['type'])
            {
                // text input field
                case self::FieldText:
                    $element = XMLToolbox::createElement('input');
                    $element->setAttribute('type', 'text');
                    $element->setAttribute('name', $field['name']);

                    // default value
                    if( isset($field['value']) )
                    {
                        $element->setAttribute('value', $field['value']);
                    }

                    $td->appendChild($element);
                    break;

                // separator label
                case self::FieldSeparator:
                    // modify new row
                    $td->removeAttribute('class');
                    $td->setAttribute('colspan', 2);
                    $td->appendChild($field['label'] instanceof DOMNode ? $field['label'] : XMLToolbox::createTextNode($field['label']) );
                    break;

                // drop-down box
                case self::FieldSelect:
                    $element = XMLToolbox::createElement('select');
                    $element->setAttribute('name', $field['name']);

                    // select options
                    foreach($field['value']['options'] as $value => $label)
                    {
                        $option = XMLToolbox::createElement('option');
                        $option->setAttribute('value', $value);
                        $option->nodeValue = $label;

                        // default value
                        if( isset($field['value']['selected']) && $field['value']['selected'] == $value)
                        {
                            $option->setAttribute('selected', 'selected');
                        }

                        $element->appendChild($option);
                    }

                    $td->appendChild($element);
                    break;

                // enabled/disabled switch
                case self::FieldIsEnabled:
                    $OnOff = $language['Components.AdminForm'];

                    $element = XMLToolbox::createElement('label');

                    $input = XMLToolbox::createElement('input');
                    $input->setAttribute('name', $field['name']);
                    $input->setAttribute('type', 'radio');
                    $input->setAttribute('value', '1');

                    // default option
                    if($field['value'])
                    {
                        $input->setAttribute('checked', 'checked');
                    }

                    $element->appendChild($input);
                    $element->appendChild( XMLToolbox::createTextNode($OnOff['Enable']) );
                    $td->appendChild($element);

                    // separator
                    $td->appendChild( XMLToolbox::createTextNode(' ') );

                    $element = XMLToolbox::createElement('label');

                    $input = XMLToolbox::createElement('input');
                    $input->setAttribute('name', $field['name']);
                    $input->setAttribute('type', 'radio');
                    $input->setAttribute('value', '0');

                    // default option
                    if(!$field['value'])
                    {
                        $input->setAttribute('checked', 'checked');
                    }

                    $element->appendChild($input);
                    $element->appendChild( XMLToolbox::createTextNode($OnOff['Disable']) );
                    $td->appendChild($element);
                    break;

                // large text area
                case self::FieldArea:
                    $element = XMLToolbox::createElement('textarea');
                    $element->setAttribute('name', $field['name']);
                    $element->setAttribute('cols', '30');
                    $element->setAttribute('rows', '10');

                    // field contents
                    $element->nodeValue = isset($field['value']) ? $field['value'] : '';

                    $td->appendChild($element);
                    break;

                // radio options
                case self::FieldRadio:
                    foreach($field['value']['options'] as $value => $label)
                    {
                        $element = XMLToolbox::createElement('label');
                        $input = XMLToolbox::createElement('input');
                        $input->setAttribute('name', $field['name']);
                        $input->setAttribute('type', 'radio');
                        $input->setAttribute('value', $value);
                        $element->appendChild($input);
                        $element->appendChild( XMLToolbox::createTextNode($label) );

                        // default value
                        if( isset($field['value']['selected']) && $field['value']['selected'] == $value)
                        {
                            $input->setAttribute('checked', 'checked');
                        }

                        $td->appendChild($element);
                        $td->appendChild( XMLToolbox::createElement('br') );
                    }
                    break;

                // file upload
                case self::FieldFile:
                    $element = XMLToolbox::createElement('input');
                    $element->setAttribute('type', 'file');
                    $element->setAttribute('name', $field['name']);
                    $td->appendChild($element);
                    break;

                // just a label
                case self::FieldLabel:
                    $td->nodeValue = $field['value'];
                    break;

                // text input field
                case self::FieldPassword:
                    $element = XMLToolbox::createElement('input');
                    $element->setAttribute('type', 'password');
                    $element->setAttribute('name', $field['name']);

                    // default value
                    if( isset($field['value']) )
                    {
                        $element->setAttribute('value', $field['value']);
                    }

                    $td->appendChild($element);
                    break;

                // colors drop-down
                case self::FieldColors:
                    $element = XMLToolbox::createElement('select');
                    $element->setAttribute('name', $field['name']);

                    $option = XMLToolbox::createElement('option');
                    $option->setAttribute('value', '');
                    $option->nodeValue = $language['Modules.Character.ValueInherit'];

                    // default value
                    if( empty($field['value']) )
                    {
                        $option->setAttribute('selected', 'selected');
                    }

                    $element->appendChild($option);

                    // select options
                    foreach( array(0 => 'FFFFFF', 1 => 'FFD4BF', 2 => 'FFE9BF', 3 => 'FFFFBF', 4 => 'E9FFBF', 5 => 'D4FFBF', 6 => 'BFFFBF', 7 => 'BFFFD4', 8 => 'BFFFE9', 9 => 'BFFFFF', 10 => 'BFE9FF', 11 => 'BFD4FF', 12 => 'BFBFFF', 13 => 'D4BFFF', 14 => 'E9BFFF', 15 => 'FFBFFF', 16 => 'FFBFE9', 17 => 'FFBFD4', 18 => 'FFBFBF', 19 => 'DADADA', 20 => 'BF9F8F', 21 => 'BFAF8F', 22 => 'BFBF8F', 23 => 'AFBF8F', 24 => '9FBF8F', 25 => '8FBF8F', 26 => '8FBF9F', 27 => '8FBFAF', 28 => '8FBFBF', 29 => '8FAFBF', 30 => '8F9FBF', 31 => '8F8FBF', 32 => '9F8FBF', 33 => 'AF8FBF', 34 => 'BF8FBF', 35 => 'BF8FAF', 36 => 'BF8F9F', 37 => 'BF8F8F', 38 => 'B6B6B6', 39 => 'BF7F5F', 40 => 'BFAF8F', 41 => 'BFBF5F', 42 => '9FBF5F', 43 => '7FBF5F', 44 => '5FBF5F', 45 => '5FBF7F', 46 => '5FBF9F', 47 => '5FBFBF', 48 => '5F9FBF', 49 => '5F7FBF', 50 => '5F5FBF', 51 => '7F5FBF', 52 => '9F5FBF', 53 => 'BF5FBF', 54 => 'BF5F9F', 55 => 'BF5F7F', 56 => 'BF5F5F', 57 => '919191', 58 => 'BF6A3F', 59 => 'BF943F', 60 => 'BFBF3F', 61 => '94BF3F', 62 => '6ABF3F', 63 => '3FBF3F', 64 => '3FBF6A', 65 => '3FBF94', 66 => '3FBFBF', 67 => '3F94BF', 68 => '3F6ABF', 69 => '3F3FBF', 70 => '6A3FBF', 71 => '943FBF', 72 => 'BF3FBF', 73 => 'BF3F94', 74 => 'BF3F6A', 75 => 'BF3F3F', 76 => '6D6D6D', 77 => 'FF5500', 78 => 'FFAA00', 79 => 'FFFF00', 80 => 'AAFF00', 81 => '54FF00', 82 => '00FF00', 83 => '00FF54', 84 => '00FFAA', 85 => '00FFFF', 86 => '00A9FF', 87 => '0055FF', 88 => '0000FF', 89 => '5500FF', 90 => 'A900FF', 91 => 'FE00FF', 92 => 'FF00AA', 93 => 'FF0055', 94 => 'FF0000', 95 => '484848', 96 => 'BF3F00', 97 => 'BF7F00', 98 => 'BFBF00', 99 => '7FBF00', 100 => '3FBF00', 101 => '00BF00', 102 => '00BF3F', 103 => '00BF7F', 104 => '00BFBF', 105 => '007FBF', 106 => '003FBF', 107 => '0000BF', 108 => '3F00BF', 109 => '7F00BF', 110 => 'BF00BF', 111 => 'BF007F', 112 => 'BF003F', 113 => 'BF0000', 114 => '242424', 115 => '7F2A00', 116 => '7F5500', 117 => '7F7F00', 118 => '557F00', 119 => '2A7F00', 120 => '007F00', 121 => '007F2A', 122 => '007F55', 123 => '007F7F', 124 => '00547F', 125 => '002A7F', 126 => '00007F', 127 => '2A007F', 128 => '54007F', 129 => '7F007F', 130 => '7F0055', 131 => '7F002A', 132 => '7F0000') as $id => $color)
                    {
                        $option = XMLToolbox::createElement('option');
                        $option->setAttribute('value', $id);
                        $option->setAttribute('style', 'background-color: #' . $color . ';');
                        $option->nodeValue = $id;

                        // default value
                        if( isset($field['value']) && $field['value'] == $id)
                        {
                            $option->setAttribute('selected', 'selected');
                        }

                        $element->appendChild($option);
                    }

                    $td->appendChild($element);
                    break;
            }

            $row->appendChild($td);
            $tbody->appendChild($row);
        }

        $row = XMLToolbox::createElement('tr');
        $td = XMLToolbox::createElement('td');
        $field = XMLToolbox::createElement('input');
        $field->setAttribute('type', 'submit');
        $field->setAttribute('value', $this['submit']);
        $td->setAttribute('colspan', '2');
        $td->appendChild($field);
        $row->appendChild($td);
        $tbody->appendChild($row);

        $table->setAttribute('class', 'formTable');
        $table->appendChild($tbody);
        $form->appendChild($table);

        // outputs only form element
        return XMLToolbox::saveXML($form);
    }

    // field type constants
    const FieldText = 0;
    const FieldSeparator = 1;
    const FieldSelect = 2;
    const FieldIsEnabled = 3;
    const FieldArea = 4;
    const FieldRadio = 5;
    const FieldFile = 6;
    const FieldLabel = 7;
    const FieldPassword = 8;
    const FieldColors = 9;

    // form fields
    private $fields = array();

    // adds new field to form
    public function addField($name, $type, $label = null, $value = null)
    {
        $this->fields[] = array('name' => $name, 'type' => $type, 'label' => $label, 'value' => $value);
    }
}

?>
