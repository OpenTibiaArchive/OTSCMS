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
    Template class.
*/

class OTSTemplate extends DataContainer
{
    // skin settings
    private $skin;

    // components
    private $components = array();

    // creates data handlers
    public function __construct($skin)
    {
        $this->skin = $skin;
    }

    // loads component class as component
    public function createComponent($class)
    {
        $class = 'Component' . $class;
        $component = new $class($this);
        $this->components[] = $component;
        return $component;
    }

    public function appendComponent(TemplateComponent $component)
    {
        $this->components[] = $component;
    }

    // displays template
    public function display($module, $command)
    {
        $language = OTSCMS::getResource('Language');
        $config = OTSCMS::getResource('Config');
        header('Content-Type: text/html; charset="utf-8"');
        include($this->skin . 'main.php');
    }

    // inserts all components
    private function run()
    {
        foreach($this->components as $component)
        {
            $component['baseHref'] = $this->skin;
            echo $component->display();
        }
    }

    // path to skin directory for outside URLs
    public function getSkinPath()
    {
        return $this->skin;
    }

    // JavaScript files
    private $javaScripts = array();

    // adds JavaScript to template
    public function addJavaScript($file)
    {
        // checks if this file waren't already included
        if( !in_array($file, $this->javaScripts) )
        {
            $this->javaScripts[] = $file;
        }
    }
}

// adds components autoloading
OTSCMS::addAutoloadDriver( new ComponentsDriver() );

?>
