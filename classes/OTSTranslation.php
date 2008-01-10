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
    Translations handler.
*/

class OTSTranslation extends DataContainer
{
    // translations settings
    private $translation;

    // creates data handlers
    public function __construct($translation)
    {
        $this->translation = $translation;
    }

    // reads value
    public function __get($key)
    {
        // with references we won't have E_NOTICE
        $data = &$this->data;
        $path = array();

        // reads each key step-by-step
        // for example root.foo.bar into root -> foo -> bar
        foreach( explode('.', $key) as $arg)
        {
            // appends new path element
            $path[] = $arg;

            // checks if given translation is loaded and if not and it is loadable then loads it
            if( !isset($data[$arg]) && file_exists($this->translation . implode('/', $path) . '.php') )
            {
                $data[$arg] = require($this->translation . implode('/', $path) . '.php');
            }

            // we need to use new variable to save reference
            // simple $data = &$data[$arg] would break entire table
            $temp = &$data;
            unset($data);

            $data = &$temp[$arg];
            unset($temp);
        }

        // returns final field - note that it still can be a table
        // for example if there is root.foo.bar then root.foo would return a table with bar field
        return $data;
    }
}

?>
