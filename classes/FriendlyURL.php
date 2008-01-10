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
    Friendly URL data handler.
*/

class InputData
{
    // URL data
    private static $data = array();

    // initiates URL data
    public static function init()
    {
        $url = new CMS_URL($_REQUEST['run']);

        // creates real query
        parse_str( preg_replace('#' . $url['name'] . '#', $url['content'], $_REQUEST['run']), $data);

        // saves current variables
        self::$data = array_merge($data, $_REQUEST);
    }

    // returns HTTP data variable
    public static function read($name)
    {
        return isset(self::$data[$name]) ? self::$data[$name] : null;
    }

    // sets new value for given input variable
    public static function write($name, $value)
    {
        self::$data[$name] = $value;
    }
}

// startup initialization
InputData::init();

?>
