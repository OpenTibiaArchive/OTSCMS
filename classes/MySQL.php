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
    SQL class for MySQL.
*/

class SQL extends SQL_Base
{
    // delimiters for names in MySQL
    protected $leftDelimiter = '`';
    protected $rightDelimiter = '`';

    public function __construct($host, $user, $password, $database, $cms_prefix, $ots_prefix)
    {
        parent::__construct($host, $user, $password, $database, $cms_prefix, $ots_prefix);

        // sets MySQL query buffering on
        $this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }

    // MySQL connection DNS
    protected function DNS($host, $user, $password, $database)
    {
        // suport for connection on other port (host:port)
        if( strpos(':', $host) !== false)
        {
            $params = explode(':', $host, 3);

            $host = $params[0] . ';port=' . $params[1];
        }

        return 'mysql:host=' . $host . ';dbname=' . $database;
    }
}

?>
