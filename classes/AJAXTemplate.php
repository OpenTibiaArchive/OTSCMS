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
    AJAX data container class.
*/

class OTSTemplate extends DataContainer
{
    // components
    private $components = array();

    // for compatibility purposes
    public function __construct($skin)
    {
        $this['baseHref'] = '/' . $this->skin;
    }

    // returns own instance to handle data assigning
    public function createComponent($class)
    {
        $component = new TemplateComponent($this);
        $this->components[] = $component;
        return $component;
    }

    public function appendComponent(TemplateComponent $component)
    {
        $this->components[] = $component;
    }

    // outputs AJAX XML
    public function display($module, $command)
    {
        // without this browser won't treat message as XML
        header('Content-Type: text/xml');

        // XML document
        $xml = new DOMDocument('1.0', 'utf-8');

        // root element
        $data = $xml->createElement('otscms');
        $data->setAttribute('module', $module);
        $data->setAttribute('command', $command);

        XMLToolbox::parseOut($this->data, $data);

        $components = $xml->createElement('components');

        // appends component data
        foreach($this->components as $component)
        {
            $node = $xml->importNode( $component->display(), true);
            $components->appendChild($node);
        }

        $data->appendChild($components);
        $xml->appendChild($data);

        // outputs XML data
        echo $xml->saveXML();
    }

    // for compatibility purposes
    public function addJavaScript($file)
    {
    }

    // loads components classes
    public static function __autoload($class)
    {
        if( preg_match('/^Component/', $class) )
        {
            $config = OTSCMS::getResource('Config');

            // loads component class
            include $config['directories.classes'] . 'Components/' . preg_replace('/^Component/', '', $class) . '.php';
        }
    }
}

// AJAX component output
OTSCMS::setDriver('TemplateComponent', 'AJAXComponent');

?>
