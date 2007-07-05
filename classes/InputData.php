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
    HTTP data handler.
*/

class InputData
{
    // URL data
    private static $data = array();

    // friendly URL
    private static $friendly = array();

    // initiates URL data
    public static function init()
    {
        // saves current variables
        self::$data = $_REQUEST;

        // splits firendly URL into array
        if( isset($_SERVER['PATH_INFO']) )
        {
            // we have to skip first / character in PATH_INFO, otherwise there will be blank first character
            self::$friendly = explode('/', substr($_SERVER['PATH_INFO'], 1) );

            // sets PHP_SELF variable to real path without friendly URL
            $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
        }
    }

    // returns HTTP data variable
    public static function read($name)
    {
        // check if there is variable in GET/POST method request
        if( !isset(self::$data[$name]) )
        {
            // if not get it from friendly URL data
            self::$data[$name] = array_shift(self::$friendly);
        }

        return self::$data[$name];
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
