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

// reverts gpc_magic_quotes effects
function _compat_revert_magic_quotes(array &$input)
{
    // unescapes each array element
    foreach( array_keys($input) as $key)
    {
        $value = $input[$key];
        unset($input[$key]);

        // strips recursively
        if( is_array($value) )
        {
            _compat_revert_magic_quotes($value);
        }
        // strips slashes from value
        else
        {
            $value = stripslashes($value);
        }

        // strips key quotes
        $input[ stripslashes($key) ] = $value;
    }
}

// checks if magic quotes are enabled
if( get_magic_quotes_gpc() )
{
    // removes magic quotes effect from input tables
    _compat_revert_magic_quotes($_GET);
    _compat_revert_magic_quotes($_POST);
    _compat_revert_magic_quotes($_REQUEST);
    _compat_revert_magic_quotes($_COOKIE);
}

// reverts register_globals effects
function _compat_revert_register_globals(array $input)
{
    // unset all of global elements
    foreach( array_keys($input) as $key)
    {
        // don't delete reserved variables
        if($key == 'GLOBALS' || $key == '_GET' || $key == '_POST' || $key == '_REQUEST' || $key == '_COOKIE' || $key == '_SESSION' || $key == '_FILES' || $key == '_SERVER' || $key == '_ENV')
        {
            continue;
        }

        unset($GLOBALS[$key]);
    }
}

// at all this shouldn't be required but in case of some "bad code"...
// protection agains register_globals
if( ini_get('register_globals') )
{
    _compat_revert_register_globals($_GET);
    _compat_revert_register_globals($_POST);
    _compat_revert_register_globals($_REQUEST);
    _compat_revert_register_globals($_COOKIE);
    _compat_revert_register_globals($_SESSION);
}

?>
