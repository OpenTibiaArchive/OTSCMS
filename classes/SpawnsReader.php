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
    This code bases on oryginal OTServ code for .otbm files - file iomapotbm.cpp rev.2141
*/

/*
    Class for map.otbm file.
*/

class SpawnsReader implements ArrayAccess, Iterator
{
    // node bytes
    const ESCAPE_CHAR = 0xFD;
    const NODE_START = 0xFE;

    // map node types
    const OTBM_TOWN = 13;

    // file handler
    protected $file;

    // spawns
    private $spawns = array();

    // loads map .otbm file
    public function __construct($file)
    {
        // opens file for reading
        $this->file = fopen($file, 'rb');

        // checks if file is opened correctly
        if($this->file)
        {
            // skips version
            fseek($this->file, 4);

            // reads nodes chain
            while( !feof($this->file) )
            {
                // reads byte
                switch( ord( fgetc($this->file) ) )
                {
                    // maybe a town node
                    case self::NODE_START:
                        // reads node type
                        if( ord( fgetc($this->file) ) == self::OTBM_TOWN)
                        {
                            $id = unpack('L', fread($this->file, 4) );
                            $length = unpack('S', fread($this->file, 2) );

                            // reads town name
                            $this->spawns[ $id[1] ] = fread($this->file, $length[1]);
                        }
                        break;

                    // escape next character - it might be NODE_START character which is in fact not
                    case self::ESCAPE_CHAR:
                        fgetc($this->file);
                        break;
                }
            }
        }
    }

    // ArrayAccess interface as wrapper for spawns property

    public function offsetExists($name)
    {
        return isset($this->spawns[$name]);
    }

    public function offsetGet($name)
    {
        return $this->spawns[$name];
    }

    public function offsetSet($name, $value)
    {
        $this->spawns[$name] = $value;
    }

    public function offsetUnset($name)
    {
        unset($this->spawns[$name]);
    }

    // Iterator interface

    public function current()
    {
        return current($this->spawns);
    }

    public function next()
    {
        next($this->spawns);
    }

    public function key()
    {
        return key($this->spawns);
    }

    public function valid()
    {
        return key($this->spawns) !== null;
    }

    public function rewind()
    {
        reset($this->spawns);
    }
}

?>
