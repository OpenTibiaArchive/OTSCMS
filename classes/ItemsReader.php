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
    Class for items.xml file content iteration.
*/

class ItemsReader implements ArrayAccess, Iterator
{
    // items
    private $items = array();

    // loads file
    public function __construct($file)
    {
        // reads server side items
        $xml = new DOMDocument();
        $xml->load($file);

        foreach( $xml->getElementsByTagName('item') as $tag)
        {
            $slot = false;
            $container = false;

            // searches for slot in which item can be put
            foreach( $tag->getElementsByTagName('attribute') as $attribute)
            {
                if( $attribute->getAttribute('key') == 'slotType')
                {
                    $slot = true;
                }
                elseif( $attribute->getAttribute('key') == 'containerSize')
                {
                    $container = true;
                }
            }

            // not wearable
            if(!$slot)
            {
                continue;
            }

            $this->items[ $tag->getAttribute('id') ] = array('name' => $tag->getAttribute('name'), 'container' => $container);
        }
    }

    // ArrayAccess interface as wrapper for items property

    public function offsetExists($name)
    {
        return isset($this->items[$name]);
    }

    public function offsetGet($name)
    {
        return $this->items[$name];
    }

    public function offsetSet($name, $value)
    {
        $this->items[$name] = $value;
    }

    public function offsetUnset($name)
    {
        unset($this->items[$name]);
    }

    // Iterator interface

    public function current()
    {
        return current($this->items);
    }

    public function next()
    {
        next($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function valid()
    {
        return key($this->items) !== null;
    }

    public function rewind()
    {
        reset($this->items);
    }
}

?>
