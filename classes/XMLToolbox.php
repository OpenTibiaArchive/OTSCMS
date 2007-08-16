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
    XML helpful routines.
*/

class XMLToolbox
{
    // global instance of DOMDocument for single fragments
    private static $xml;

    // initializes document instance
    public static function init()
    {
        self::$xml = new DOMDocument('1.0', 'utf-8');
        self::$xml->registerNodeClass('DOMElement', 'XMLElement');
        self::$xml->registerNodeClass('DOMDocumentFragment', 'XMLDocumentFragment');
        self::$xml->formatOutput = true;
    }

    // DOMDocument methods wrappers

    public static function createElement($name)
    {
        return self::$xml->createElement($name);
    }

    public static function createDocumentFragment()
    {
        return self::$xml->createDocumentFragment();
    }

    public static function createEntityReference($entity)
    {
        return self::$xml->createEntityReference($entity);
    }

    public static function saveXML(DOMNode $node)
    {
        return self::$xml->saveXML($node);
    }

    // parses sub-arrays into XML tags
    public static function parseOut($from, DOMNode $to)
    {
        $document = $to->ownerDocument;

        foreach($from as $name => $element)
        {
            // DOMNodes will just be appended to parent
            if($element instanceof DOMNode)
            {
                $tag = $document->importNode($element, true);
            }
            // arrays and objects will be parsed recoursively
            elseif( is_array($element) || is_object($element) )
            {
                if( is_numeric($name) || $name == 'field')
                {
                    $tag = $document->createElement('field');
                    $tag->setAttribute('name', $name);
                }
                else
                {
                    $tag = $document->createElement($name);
                }

                self::parseOut($element, $tag);
            }
            // flat data types will have simple tags <field name="foo" value="bar">
            elseif( is_numeric($name) || $name == 'field')
            {
                $tag = $document->createElement('field');
                $tag->setAttribute('name', $name);
                $tag->setAttribute('value', $element);
            }
            // fields with string keys will have it as it's tag name
            else
            {
                $tag = $document->createElement($name);
                $tag->setAttribute('value', $element);
            }

            // appends next child
            $to->appendChild($tag);
        }
    }

    // imports XHTML packet into current document tree
    public static function inparse($text)
    {
        // document for parsing text tree
        $xml = DOMDocument::loadXML('<?xml version="1.0" encoding="utf-8"?><root>' . $text . '</root>');

        // container for loaded tree
        $tree = self::createDocumentFragment();

        // loads tree into current container
        foreach( $xml->documentElement->childNodes as $child)
        {
            $tree->addContent($child);
        }

        // returns parsed tree
        return $tree;
    }
}

// initializes XML toolbox
XMLToolbox::init();

?>
