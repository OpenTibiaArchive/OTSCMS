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
    BBCode editor.
*/

class ComponentBBEditor extends TemplateComponent
{
    // requires javascript part
    public function __construct(OTSTemplate $template)
    {
        parent::__construct($template);

        $this->owner->addJavaScript('bb');
    }

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
        $form->setAttribute('id', 'bbeditor');

        // form table
        $table = XMLToolbox::createElement('table');
        $tbody = XMLToolbox::createElement('tbody');

        // title
        $row = XMLToolbox::createElement('tr');

        $td = XMLToolbox::createElement('td');
        $td->setAttribute('class', 'formLeft');
        $td->nodeValue = $language['Modules.' . $this->module . '.Name'] . ': ';
        $row->appendChild($td);

        $td = XMLToolbox::createElement('td');
        $td->setAttribute('class', 'formRight');
        $element = XMLToolbox::createElement('input');
        $element->setAttribute('type', 'text');
        $element->setAttribute('name', 'bb[name]');
        $element->setAttribute('value', $this['fields']['name']);
        $td->appendChild($element);
        $row->appendChild($td);

        $tbody->appendChild($row);

        // from
        $row = XMLToolbox::createElement('tr');

        $td = XMLToolbox::createElement('td');
        $td->setAttribute('class', 'formLeft');
        $td->nodeValue = $language['Modules.' . $this->module . '.From'] . ': ';
        $row->appendChild($td);

        $td = XMLToolbox::createElement('td');
        $td->setAttribute('class', 'formRight');
        $element = XMLToolbox::createElement('select');
        $element->setAttribute('name', 'bb[' . $this->fromName . ']');

        // user characters
        foreach($this['characters'] as $character)
        {
            $option = XMLToolbox::createElement('option');
            $option->setAttribute('value', $character['name']);
            $option->nodeValue = $character['name'];
            $element->appendChild($option);
        }

        $td->appendChild($element);
        $row->appendChild($td);

        $tbody->appendChild($row);

        // to field
        if($this->toField)
        {
            $row = XMLToolbox::createElement('tr');

            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formLeft');
            $td->nodeValue = $language['Modules.' . $this->module . '.To'] . ': ';
            $row->appendChild($td);

            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formRight');
            $element = XMLToolbox::createElement('input');
            $element->setAttribute('type', 'text');
            $element->setAttribute('name', 'bb[to]');
            $element->setAttribute('value', $this['fields']['to']);
            $td->appendChild($element);
            $row->appendChild($td);

            $tbody->appendChild($row);
        }

        // bb content editor itself
        $row = XMLToolbox::createElement('tr');

        $td = XMLToolbox::createElement('td');
        $td->setAttribute('class', 'formLeft');
        $td->nodeValue = $language['Modules.' . $this->module . '.Content'] . ': ';
        $row->appendChild($td);

        $td = XMLToolbox::createElement('td');
        $td->setAttribute('class', 'formRight');

        $bbtable = XMLToolbox::createElement('table');
        $bbeditor = XMLToolbox::createElement('tbody');

        // font formating
        $tr = XMLToolbox::createElement('tr');

        // font style
        $cell = XMLToolbox::createElement('td');
        $element = XMLToolbox::createElement('select');

        $option = XMLToolbox::createElement('option');
        $option->setAttribute('value', '');
        $option->nodeValue = $language['Components.BBEditor.font'];
        $element->appendChild($option);

        foreach( array('Arial', 'Arial Narrow', 'Book Antiqua', 'Century Gothic', 'Comic Sans MS', 'Courier New', 'Fixedsys', 'Franklin Gothic Medium', 'Garamond', 'Georgia', 'Impact', 'Lucida Console', 'Lucida Sans Unicode', 'Microsoft Sans Serif', 'Palatino Linotype', 'System', 'Tahoma', 'Times New Roman', 'Trebuchet MS', 'Verdana') as $font)
        {
            $option = XMLToolbox::createElement('option');
            $option->setAttribute('value', $font);
            $option->setAttribute('style', 'font-family: \'' . $font . '\', sans-serif;');
            $option->nodeValue = $font;
            $element->appendChild($option);
        }

        $element->setAttribute('onchange', 'fontformat(this.options[this.selectedIndex].value, \'FONT\');');
        $element->setAttribute('id', 'fontselect');
        $cell->appendChild($element);
        $cell->appendChild( XMLToolbox::createEntityReference('nbsp') );
        $tr->appendChild($cell);

        // size
        $cell = XMLToolbox::createElement('td');
        $element = XMLToolbox::createElement('select');

        $option = XMLToolbox::createElement('option');
        $option->setAttribute('value', '');
        $option->nodeValue = $language['Components.BBEditor.size'];
        $element->appendChild($option);

        foreach( array(1 => 4, 8, 12, 16, 20, 24, 28) as $pix => $size)
        {
            $option = XMLToolbox::createElement('option');
            $option->setAttribute('value', $pix);
            $option->nodeValue = $size;
            $element->appendChild($option);
        }

        $element->setAttribute('onchange', 'fontformat(this.options[this.selectedIndex].value, \'SIZE\');');
        $element->setAttribute('id', 'sizeselect');
        $cell->appendChild($element);
        $cell->appendChild( XMLToolbox::createEntityReference('nbsp') );
        $tr->appendChild($cell);

        // color
        $cell = XMLToolbox::createElement('td');
        $element = XMLToolbox::createElement('select');

        $option = XMLToolbox::createElement('option');
        $option->setAttribute('value', '');
        $option->nodeValue = $language['Components.BBEditor.color'];
        $element->appendChild($option);

        foreach( array('000000' => 'Black', 'A0522D' => 'Sienna', '556B2F' => 'DarkOliveGreen', '006400' => 'DarkGreen', '483D8B' => 'DarkSlateBlue', '000080' => 'Navy', '4B0082' => 'Indigo', '2F4F4F' => 'DarkSlateGray', '8B0000' => 'DarkRed', 'FF8C00' => 'DarkOrange', '808000' => 'Olive', '008000' => 'Green', '008080' => 'Teal', '0000FF' => 'Blue', '708090' => 'SlateGray', '696969' => 'DimGray', 'FF0000' => 'Red', 'F4A460' => 'SandyBrown', '9ACD32' => 'YellowGreen', '8FBC8F' => 'SeaGreen', '48D1CC' => 'MediumTurquoise', '4169E1' => 'RoyalBlue', '800080' => 'Purple', '808080' => 'Gray', 'FF00FF' => 'Magenta', 'FF8C00' => 'Orange', 'FFFF00' => 'Yellow', '00FF00' => 'Lime', '00FFFF' => 'Cyan', '00BFFF' => 'DeepSkyBlue', '9932CC' => 'DarkOrchid', 'C0C0C0' => 'Silver', 'FFC0CB' => 'Pink', 'F5DEB3' => 'Wheat', 'FFFACD' => 'LemonChiffon', '98FB98' => 'PaleGreen', 'AFEEEE' => 'PaleTurquoise', 'ADD8E6' => 'LightBlue', 'DDA0DD' => 'Plum', 'FFFFFF' => 'White') as $rgb => $color)
        {
            $option = XMLToolbox::createElement('option');
            $option->setAttribute('value', $color);
            $option->setAttribute('style', 'color: #' . $rgb . ';');
            $option->nodeValue = $color;
            $element->appendChild($option);
        }

        $element->setAttribute('onchange', 'fontformat(this.options[this.selectedIndex].value, \'COLOR\');');
        $element->setAttribute('id', 'colorselect');
        $cell->appendChild($element);
        $cell->appendChild( XMLToolbox::createEntityReference('nbsp') );

        $tr->appendChild($cell);

        $bbeditor->appendChild($tr);

        // text formating buttons
        $tr = XMLToolbox::createElement('tr');
        $cell = XMLToolbox::createElement('td');
        $subTable = XMLToolbox::createElement('table');
        $subTBody = XMLToolbox::createElement('tbody');
        $buttons = XMLToolbox::createElement('tr');

        // separator - for further use
        $separator = XMLToolbox::createElement('td');
        $img = XMLToolbox::createElement('img');
        $img->setAttribute('src', $this->owner->getSkinPath() . 'bb/separator.gif');
        $img->setAttribute('alt', '');
        $img->setAttribute('class', 'bbseparator');
        $separator->appendChild($img);

        // button options
        foreach( array('bold' => 'bbcode(\'B\')', 'italic' => 'bbcode(\'I\')', 'underline' => 'bbcode(\'U\')', false, 'justifyleft' => 'bbcode(\'ALIGN\', \'left\')', 'justifycenter' => 'bbcode(\'CENTER\')', 'justifyright' => 'bbcode(\'ALIGN\', \'right\')', false, 'orderlist' => 'dolist(1)', 'unorderedlist' => 'dolist()', false, 'insertimage' => 'bbcode(\'IMG\')', 'createlink' => 'namedlink(\'URL\')', 'email' => 'namedlink(\'EMAIL\')', false, 'code' => 'bbcode(\'CODE\')', 'php' => 'bbcode(\'PHP\')', false, 'quote' => 'bbcode(\'QUOTE\')', false, 'sub' => 'bbcode(\'SUB\')', 'sup' => 'bbcode(\'SUB\')') as $label => $code)
        {
            // separator
            if(!$code)
            {
                $buttons->appendChild( $separator->cloneNode(true) );
                continue;
            }

            // option image
            $button = XMLToolbox::createElement('td');
            $img = XMLToolbox::createElement('img');
            $img->setAttribute('onclick', $code . ';');
            $img->setAttribute('class', 'bbbutton');
            $img->setAttribute('src', $this->owner->getSkinPath() . 'bb/' . $label . '.gif');
            $img->setAttribute('alt', $language['Components.BBEditor.' . $label]);
            $button->appendChild($img);
            $buttons->appendChild($button);
        }

        $subTBody->appendChild($buttons);
        $subTable->appendChild($subTBody);
        $cell->setAttribute('colspan', '3');
        $cell->appendChild($subTable);
        $tr->appendChild($cell);
        $bbeditor->appendChild($tr);

        // textarea field
        $tr = XMLToolbox::createElement('tr');
        $cell = XMLToolbox::createElement('td');
        $element = XMLToolbox::createElement('textarea');
        $element->nodeValue = empty($this['fields']['content']) ? ' ' : $this['fields']['content'];
        $element->setAttribute('name', 'bb[content]');
        $element->setAttribute('rows', '10');
        $element->setAttribute('cols', '54');
        $cell->appendChild($element);
        $cell->setAttribute('colspan', '3');
        $tr->appendChild($cell);
        $bbeditor->appendChild($tr);

        $bbtable->setAttribute('cellpadding', '0');
        $bbtable->setAttribute('cellspacing', '0');
        $bbtable->setAttribute('id', 'bbtable');
        $bbtable->appendChild($bbeditor);
        $td->appendChild($bbtable);
        $row->appendChild($td);
        $tbody->appendChild($row);

        // new topic admin actions
        if($this->adminActions)
        {
            $row = XMLToolbox::createElement('tr');

            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formLeft');
            $td->nodeValue = $language['Components.BBEditor.Admin'] . ': ';
            $row->appendChild($td);

            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formRight');
            $element = XMLToolbox::createElement('select');
            $element->setAttribute('name', 'bb[after]');

            $option = XMLToolbox::createElement('option');
            $option->setAttribute('value', '0');
            $option->setAttribute('selected', 'selected');
            $option->nodeValue = $language['Components.BBEditor.AfterNothing'];
            $element->appendChild($option);

            $option = XMLToolbox::createElement('option');
            $option->setAttribute('value', '1');
            $option->nodeValue = $language['Components.BBEditor.AfterPin'];
            $element->appendChild($option);

            $option = XMLToolbox::createElement('option');
            $option->setAttribute('value', '2');
            $option->nodeValue = $language['Components.BBEditor.AfterClose'];
            $element->appendChild($option);

            $option = XMLToolbox::createElement('option');
            $option->setAttribute('value', '3');
            $option->nodeValue = $language['Components.BBEditor.AfterPinClose'];
            $element->appendChild($option);

            $td->appendChild($element);
            $row->appendChild($td);

            $tbody->appendChild($row);
        }

        // submit button
        $row = XMLToolbox::createElement('tr');
        $td = XMLToolbox::createElement('td');
        $field = XMLToolbox::createElement('input');
        $field->setAttribute('type', 'submit');
        $field->setAttribute('value', $language['Modules.' . $this->module . '.SendSubmit']);
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

    // layout settings
    public $module;
    public $toField = false;
    public $adminActions = false;
    public $fromName = 'from';
}

?>
