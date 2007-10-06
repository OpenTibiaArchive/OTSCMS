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
        $td->addContent($language['Modules.' . $this->module . '.Name'] . ': ');
        $row->addContent($td);

        $td = XMLToolbox::createElement('td');
        $td->setAttribute('class', 'formRight');
        $element = XMLToolbox::createElement('input');
        $element->setAttribute('type', 'text');
        $element->setAttribute('name', 'bb[name]');
        $element->setAttribute('value', $this['fields']['name']);
        $td->addContent($element);
        $row->addContent($td);

        $tbody->addContent($row);

        // from
        $row = XMLToolbox::createElement('tr');

        $td = XMLToolbox::createElement('td');
        $td->setAttribute('class', 'formLeft');
        $td->addContent($language['Modules.' . $this->module . '.From'] . ': ');
        $row->addContent($td);

        $td = XMLToolbox::createElement('td');
        $td->setAttribute('class', 'formRight');
        $element = XMLToolbox::createElement('select');
        $element->setAttribute('name', 'bb[' . $this->fromName . ']');

        // user characters
        foreach($this['characters'] as $character)
        {
            $option = XMLToolbox::createElement('option');
            $option->setAttribute('value', $character->getName() );
            $option->addContent( $character->getName() );
            $element->addContent($option);
        }

        $td->addContent($element);
        $row->addContent($td);

        $tbody->addContent($row);

        // to field
        if($this->toField)
        {
            $row = XMLToolbox::createElement('tr');

            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formLeft');
            $td->addContent($language['Modules.' . $this->module . '.To'] . ': ');
            $row->addContent($td);

            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formRight');
            $element = XMLToolbox::createElement('input');
            $element->setAttribute('type', 'text');
            $element->setAttribute('name', 'bb[to]');
            $element->setAttribute('value', $this['fields']['to']);
            $td->addContent($element);
            $row->addContent($td);

            $tbody->addContent($row);
        }

        // bb content editor itself
        $row = XMLToolbox::createElement('tr');

        $td = XMLToolbox::createElement('td');
        $td->setAttribute('class', 'formLeft');
        $td->addContent($language['Modules.' . $this->module . '.Content'] . ': ');
        $row->addContent($td);

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
        $option->addContent($language['Components.BBEditor.font']);
        $element->addContent($option);

        foreach( array('Arial', 'Arial Narrow', 'Book Antiqua', 'Century Gothic', 'Comic Sans MS', 'Courier New', 'Fixedsys', 'Franklin Gothic Medium', 'Garamond', 'Georgia', 'Impact', 'Lucida Console', 'Lucida Sans Unicode', 'Microsoft Sans Serif', 'Palatino Linotype', 'System', 'Tahoma', 'Times New Roman', 'Trebuchet MS', 'Verdana') as $font)
        {
            $option = XMLToolbox::createElement('option');
            $option->setAttribute('value', $font);
            $option->setAttribute('style', 'font-family: \'' . $font . '\', sans-serif;');
            $option->addContent($font);
            $element->addContent($option);
        }

        $element->setAttribute('onchange', 'fontformat(this.options[this.selectedIndex].value, \'FONT\');');
        $element->setAttribute('id', 'fontselect');
        $cell->addContents($element, XMLToolbox::createEntityReference('nbsp') );
        $tr->addContent($cell);

        // size
        $cell = XMLToolbox::createElement('td');
        $element = XMLToolbox::createElement('select');

        $option = XMLToolbox::createElement('option');
        $option->setAttribute('value', '');
        $option->addContent($language['Components.BBEditor.size']);
        $element->addContent($option);

        foreach( array(1 => 4, 8, 12, 16, 20, 24, 28) as $pix => $size)
        {
            $option = XMLToolbox::createElement('option');
            $option->setAttribute('value', $pix);
            $option->addContent($size);
            $element->addContent($option);
        }

        $element->setAttribute('onchange', 'fontformat(this.options[this.selectedIndex].value, \'SIZE\');');
        $element->setAttribute('id', 'sizeselect');
        $cell->addContents($element, XMLToolbox::createEntityReference('nbsp') );
        $tr->addContent($cell);

        // color
        $cell = XMLToolbox::createElement('td');
        $element = XMLToolbox::createElement('select');

        $option = XMLToolbox::createElement('option');
        $option->setAttribute('value', '');
        $option->addContent($language['Components.BBEditor.color']);
        $element->addContent($option);

        foreach( array('000000' => 'Black', 'A0522D' => 'Sienna', '556B2F' => 'DarkOliveGreen', '006400' => 'DarkGreen', '483D8B' => 'DarkSlateBlue', '000080' => 'Navy', '4B0082' => 'Indigo', '2F4F4F' => 'DarkSlateGray', '8B0000' => 'DarkRed', 'FF8C00' => 'DarkOrange', '808000' => 'Olive', '008000' => 'Green', '008080' => 'Teal', '0000FF' => 'Blue', '708090' => 'SlateGray', '696969' => 'DimGray', 'FF0000' => 'Red', 'F4A460' => 'SandyBrown', '9ACD32' => 'YellowGreen', '8FBC8F' => 'SeaGreen', '48D1CC' => 'MediumTurquoise', '4169E1' => 'RoyalBlue', '800080' => 'Purple', '808080' => 'Gray', 'FF00FF' => 'Magenta', 'FF8C00' => 'Orange', 'FFFF00' => 'Yellow', '00FF00' => 'Lime', '00FFFF' => 'Cyan', '00BFFF' => 'DeepSkyBlue', '9932CC' => 'DarkOrchid', 'C0C0C0' => 'Silver', 'FFC0CB' => 'Pink', 'F5DEB3' => 'Wheat', 'FFFACD' => 'LemonChiffon', '98FB98' => 'PaleGreen', 'AFEEEE' => 'PaleTurquoise', 'ADD8E6' => 'LightBlue', 'DDA0DD' => 'Plum', 'FFFFFF' => 'White') as $rgb => $color)
        {
            $option = XMLToolbox::createElement('option');
            $option->setAttribute('value', $color);
            $option->setAttribute('style', 'color: #' . $rgb . ';');
            $option->addContent($color);
            $element->addContent($option);
        }

        $element->setAttribute('onchange', 'fontformat(this.options[this.selectedIndex].value, \'COLOR\');');
        $element->setAttribute('id', 'colorselect');
        $cell->addContents($element, XMLToolbox::createEntityReference('nbsp') );

        $tr->addContent($cell);

        $bbeditor->addContent($tr);

        // text formating buttons
        $tr = XMLToolbox::createElement('tr');
        $cell = XMLToolbox::createElement('td');
        $subTable = XMLToolbox::createElement('table');
        $subTBody = XMLToolbox::createElement('tbody');
        $buttons = XMLToolbox::createElement('tr');

        // separator - for further use
        $separator = XMLToolbox::createElement('td');
        $img = XMLToolbox::createElement('img');
        $img->setAttribute('src', $this->owner['baseHref'] . 'bb/separator.gif');
        $img->setAttribute('alt', '');
        $img->setAttribute('class', 'bbseparator');
        $separator->addContent($img);

        // button options
        foreach( array('bold' => 'bbcode(\'B\')', 'italic' => 'bbcode(\'I\')', 'underline' => 'bbcode(\'U\')', false, 'justifyleft' => 'bbcode(\'ALIGN\', \'left\')', 'justifycenter' => 'bbcode(\'CENTER\')', 'justifyright' => 'bbcode(\'ALIGN\', \'right\')', false, 'orderlist' => 'dolist(1)', 'unorderedlist' => 'dolist()', false, 'insertimage' => 'bbcode(\'IMG\')', 'createlink' => 'namedlink(\'URL\')', 'email' => 'namedlink(\'EMAIL\')', false, 'code' => 'bbcode(\'CODE\')', 'php' => 'bbcode(\'PHP\')', false, 'quote' => 'bbcode(\'QUOTE\')', false, 'sub' => 'bbcode(\'SUB\')', 'sup' => 'bbcode(\'SUB\')') as $label => $code)
        {
            // separator
            if(!$code)
            {
                $buttons->addContent( $separator->cloneNode(true) );
                continue;
            }

            // option image
            $button = XMLToolbox::createElement('td');
            $img = XMLToolbox::createElement('img');
            $img->setAttribute('onclick', $code . ';');
            $img->setAttribute('class', 'bbbutton');
            $img->setAttribute('src', $this->owner['baseHref'] . 'bb/' . $label . '.gif');
            $img->setAttribute('alt', $language['Components.BBEditor.' . $label]);
            $button->addContent($img);
            $buttons->addContent($button);
        }

        $subTBody->addContent($buttons);
        $subTable->addContent($subTBody);
        $cell->setAttribute('colspan', '3');
        $cell->addContent($subTable);
        $tr->addContent($cell);
        $bbeditor->addContent($tr);

        // textarea field
        $tr = XMLToolbox::createElement('tr');
        $cell = XMLToolbox::createElement('td');
        $element = XMLToolbox::createElement('textarea');
        $element->addContent( empty($this['fields']['content']) ? ' ' : $this['fields']['content']);
        $element->setAttribute('name', 'bb[content]');
        $element->setAttribute('rows', '10');
        $element->setAttribute('cols', '54');
        $cell->addContent($element);
        $cell->setAttribute('colspan', '3');
        $tr->addContent($cell);
        $bbeditor->addContent($tr);

        $bbtable->setAttribute('cellpadding', '0');
        $bbtable->setAttribute('cellspacing', '0');
        $bbtable->setAttribute('id', 'bbtable');
        $bbtable->addContent($bbeditor);
        $td->addContent($bbtable);
        $row->addContent($td);
        $tbody->addContent($row);

        // new topic admin actions
        if($this->adminActions)
        {
            $row = XMLToolbox::createElement('tr');

            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formLeft');
            $td->addContent($language['Components.BBEditor.Admin'] . ': ');
            $row->addContent($td);

            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'formRight');
            $element = XMLToolbox::createElement('select');
            $element->setAttribute('name', 'bb[after]');

            $nothing = XMLToolbox::createElement('option');
            $nothing->setAttribute('value', '0');
            $nothing->setAttribute('selected', 'selected');
            $nothing->addContent($language['Components.BBEditor.AfterNothing']);

            $pin = XMLToolbox::createElement('option');
            $pin->setAttribute('value', '1');
            $pin->addContent($language['Components.BBEditor.AfterPin']);

            $close = XMLToolbox::createElement('option');
            $close->setAttribute('value', '2');
            $close->addContent($language['Components.BBEditor.AfterClose']);

            $both = XMLToolbox::createElement('option');
            $both->setAttribute('value', '3');
            $both->addContent($language['Components.BBEditor.AfterPinClose']);
            $element->addContents($nothing, $pin, $option, $both);

            $td->addContent($element);
            $row->addContent($td);

            $tbody->addContent($row);
        }

        // submit button
        $row = XMLToolbox::createElement('tr');
        $td = XMLToolbox::createElement('td');
        $field = XMLToolbox::createElement('input');
        $field->setAttribute('type', 'submit');
        $field->setAttribute('value', $language['Modules.' . $this->module . '.SendSubmit']);
        $td->setAttribute('colspan', '2');
        $td->addContent($field);
        $row->addContent($td);
        $tbody->addContent($row);

        $table->setAttribute('class', 'formTable');
        $table->addContent($tbody);
        $form->addContent($table);

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
