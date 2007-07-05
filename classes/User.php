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
    Handles user account logging.
*/

class User
{
    // public account info
    public static $logged = false;
    public static $number;

    // default access level
    protected static $access = -1;

    // logs user in
    public static function login($account, $password)
    {
        // reading account information from SQL
        $account = new OTS_Account($account);

        // checks password
        if($password != $account['password'])
        {
            throw new HandledException('WrongPassword');
        }

        // checks if account is active
        if($account['blocked'])
        {
            throw new HandledException('AccountBlocked');
        }

        // reads highest access level
        $maxAccess = OTSCMS::getResource('DB')->query('SELECT MAX({groups}.`access`) AS `access` FROM {players}, {groups} WHERE {players}.`account_id` = ' . (int) $account['id'] . ' AND {players}.`group_id` = {groups}.`id`')->fetch();

        // sets user information
        self::$logged = true;
        self::$number = $account['id'];
        self::$access = $maxAccess['access'];

        // default 0 access for logged player without characters
        if( is_null(self::$access) )
        {
            self::$access = 0;
        }
    }

    // checks user access level
    public static function hasAccess($access)
    {
        return self::$access >= $access;
    }
}

?>
