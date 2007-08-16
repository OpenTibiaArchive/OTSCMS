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
    Generic class for handling SQL records in object-oriented way.
*/

abstract class ActiveRecord extends DataContainer implements Iterator
{
    // record fields
    protected $data = array();

    // database connection
    protected $db;

    // allows to create new record with automatical loading or startup data
    final public function __construct($data = null)
    {
        // database handle
        $this->db = OTSCMS::getResource('DB');

        // ready record
        if( is_array($data) )
        {
            $this->data = $data;
        }
        // or loads record by id
        elseif( isset($data) )
        {
            $this->load($data);
        }
    }

    // loads record by given ID
    abstract public function load($id);

    // saves current record
    abstract public function save();

    // Iterator interface

    public function current()
    {
        return current($this->data);
    }

    public function next()
    {
        next($this->data);
    }

    public function key()
    {
        return key($this->data);
    }

    public function valid()
    {
        return key($this->data) !== null;
    }

    public function rewind()
    {
        reset($this->data);
    }
}

?>
