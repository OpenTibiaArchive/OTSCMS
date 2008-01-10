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
    Cookies handler.
*/

class Cookies
{
    // cookies settings
    private static $prefix;
    private static $path;
    private static $domain;
    private static $expire;

    // sets names prefix
    public static function init($prefix, $path, $domain, $expire)
    {
        self::$prefix = $prefix;
        self::$path = $path;
        self::$domain = $domain;
        self::$expire = $expire;
    }

    // clears session variable
    public static function unRegister()
    {
        foreach( func_get_args() as $name)
        {
            setcookie(self::$prefix . $name, '', time() - 1, self::$path, self::$domain);
        }
    }

    // returns cookie
    public static function read($name)
    {
        return isset($_COOKIE[self::$prefix . $name]) ? $_COOKIE[self::$prefix . $name] : null;
    }

    // sets cookie
    public static function write($name, $value)
    {
        setcookie(self::$prefix . $name, $value, time() + self::$expire, self::$path, self::$domain);
    }
}

// startup initialization
$config = OTSCMS::getResource('Config');
$cookies = $config['cookies'];
Cookies::init($cookies['prefix'], $cookies['path'], $cookies['domain'], $cookies['expire']);

?>
