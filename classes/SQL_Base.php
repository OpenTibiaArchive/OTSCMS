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
    General SQL connection handler class with main interface.
*/

abstract class SQL_Base extends PDO
{
    // delimiters for names
    protected $leftDelimiter;
    protected $rightDelimiter;

    // prefix for OTSCMS tables
    protected $cms_prefix = '';

    // prefix for OTServ tables
    protected $ots_prefix = '';

    // universal constructor
    public function __construct($host, $user, $password, $database, $cms_prefix, $ots_prefix)
    {
        $this->cms_prefix = $cms_prefix;
        $this->ots_prefix = $ots_prefix;

        parent::__construct( $this->DNS($host, $user, $password, $database), $user, $password);

        $this->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // required for portable DNS generation
    abstract protected function DNS($host, $user, $password, $database);

    // returns field name for query
    final public function fieldName($name)
    {
        return $this->leftDelimiter . $name . $this->rightDelimiter;
    }

    // with this method we dont need to call those ugly OTSTable()/CMSTable()/fieldName() directly
    final protected function OTSCMSPrepare($sql)
    {
        //OTSTable() calls
        preg_match_all('/{(.*?)}/', $sql, $matches, PREG_SET_ORDER);
        foreach($matches as $match)
        {
            $sql = str_replace($match[0], $this->fieldName($this->ots_prefix . $match[1]), $sql);
        }

        //CMSTable() calls
        preg_match_all('/\[(.*?)\]/', $sql, $matches, PREG_SET_ORDER);
        foreach($matches as $match)
        {
            $sql = str_replace($match[0], $this->fieldName($this->cms_prefix . $match[1]), $sql);
        }

        //fieldName() calls
        preg_match_all('/`(.*?)`/', $sql, $matches, PREG_SET_ORDER);
        foreach($matches as $match)
        {
            $sql = str_replace($match[0], $this->fieldName($match[1]), $sql);
        }

        return $sql;
    }

    // binds OTSCMSPrepare() to basic functions

    public function query($sql)
    {
        return parent::query( $this->OTSCMSPrepare($sql), PDO::FETCH_ASSOC);
    }

    public function exec($sql)
    {
        return parent::exec( $this->OTSCMSPrepare($sql) );
    }

    public function prepare($sql, $options = array() )
    {
        $statement = parent::prepare( $this->OTSCMSPrepare($sql), $options);
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        return $statement;
    }
}

// database tables objects autoloading
OTSCMS::addAutoloadDriver( new CMSTablesDriver() );
OTSCMS::addAutoloadDriver( new OTSTablesDriver() );

?>
