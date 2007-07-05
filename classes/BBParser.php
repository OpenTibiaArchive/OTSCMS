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
    Class for parsing BBCode in like-char*-way-from-c.
*/

class BBParser
{
    // it's string that contains characters
    private static $_buffer = '';
    private static $_length;

    // it's an internal pointer to string field that will simulate char*
    private static $_pointer = 0;

    // skips characters until given string will be found (if there is no such substring, then pointer won't change)
    protected static function expect($what)
    {
        // looks for string in buffer
        $temp = strpos(self::$_buffer, $what, self::$_pointer);

        // check if the string was found
        if($temp !== false)
        {
            self::$_pointer = $temp + strlen($what);
        }
    }

    // returns content before given string
    protected static function getBefore($what)
    {
        // looks for string in buffer
        $temp = strpos(self::$_buffer, $what, self::$_pointer);

        // check if the string was found and if not then skips to end of buffer
        if($temp === false)
        {
            $temp = self::$_length;
        }

        // veriables for substring receivemnt
        $start = self::$_pointer;
        self::$_pointer = $temp;

        // returns substring
        return substr(self::$_buffer, $start, self::$_pointer - $start);
    }

    // returns current character and restores old pointer value
    protected static function getActual($count = 1)
    {
        return substr(self::$_buffer, self::$_pointer, $count);
    }

    // returns current character and steps one character forward
    protected static function getCurrent()
    {
        $temp = substr(self::$_buffer, self::$_pointer, 1);
        self::$_pointer++;
        return $temp;
    }

    // main BB code parsing
    public static function parse($string)
    {
        // simply sets internal variables
        self::$_buffer = htmlspecialchars($string);
        self::$_pointer = 0;
        self::$_length = strlen(self::$_buffer);

        // array of opened tags
        $opened = array();

        $temp = '';

        $nextLI = false;

        // parses whole thing
        while(self::$_pointer < self::$_length)
        {
            // until the BB tag won't be found it will just return standard text
            $temp .= nl2br( self::getBefore('[') );

            // checks if the end of post was reached
            if(self::$_pointer >= self::$_length)
            {
                break;
            }

            // by default it's opening tag
            $closing = false;

            // skips opening character
            self::expect('[');

            // checks if it's closing tag
            if( self::getActual() == '/')
            {
                // set that it's closing tag and skips slash so the name could be read
                $closing = true;
                self::expect('/');
            }

            $name = '';

            $char = ord( self::getActual() );
            // you can use only a-z, A-Z characters in tag names
            while(($char >= 65 && $char <= 90) || ($char >= 97 && $char <= 122 || $char == 58 || $char == 42) && self::$_pointer < self::$_length)
            {
                $name .= self::getCurrent();
                $char = ord( self::getActual() );
            }

            // to make it more compatible
            $name = strtolower($name);

            // it's to make closing checking easier
            if($name == 'list')
            {
                $name = 'list:o';
            }

            // ends the tag
            if($closing)
            {
                // removes tag from opened array
                unset($opened[array_search($name, $opened)]);

                $attribute = '';

                // checks if there is any parameter for a tag
                // only : is suppoerted in closing tags
                if( self::getActual() == ':')
                {
                    // skips parameter begin
                    self::$_pointer++;

                    // reads parameter
                    $attribute = self::getBefore(']');
                }

                // closes the tag
                switch($name)
                {
                    // ordered list
                    case 'list:o':
                        $temp .= ($nextLI ? '</li>' : '') . '</ol>';
                        break;
                    // unordered list
                    case 'list:u':
                        $temp .= ($nextLI ? '</li>' : '') . '</ul>';
                        break;
                    // end of span tag for certain BB codes
                    case 'b':
                    case 'i':
                    case 'u':
                    case 'font':
                    case 'size':
                    case 'color':
                        $temp .= '</span>';
                        break;
                    // subscript
                    case 'sub':
                        $temp .= '</sub>';
                        break;
                    // superscript
                    case 'sup':
                        $temp .= '</sup>';
                        break;
                    // link
                    case 'url':
                    case 'mail':
                    case 'email':
                        $temp .= '</a>';
                    // ending of blocks of text
                    case 'quote':
                    case 'align':
                    case 'center':
                        $temp .= '</div>';
                        break;
                    // unsupported tags will be appended to results
                    default:
                        $temp .= '[/'.$name.']';
                }
            }
            // opens the tag
            else
            {
                // ads tag to opened tags array
                array_push($opened, $name);

                $attribute = '';

                // checks if there is any parameter for a tag
                // = and : are supported in opening tags
                if( self::getActual() == '=')
                {
                    // skips parameter begin
                    self::$_pointer++;

                    // reads parameter
                    $attribute = self::getBefore(']');
                }

                // opens the tag
                switch($name)
                {
                    // ordered list
                    case 'list:o':
                        $nextLI = false;
                        $temp .= '<ol';

                        // list types
                        switch($attribute)
                        {
                            // capital romans
                            case 'I':
                                $temp .= ' style="list-style-type: upper-roman;"';
                                break;
                            // lowercase romans
                            case 'i':
                                $temp .= ' style="list-style-type: lower-roman;"';
                                break;
                            // capital alphabet
                            case 'A':
                                $temp .= ' style="list-style-type: upper-alpha;"';
                                break;
                            // lowercase alphabet
                            case 'a':
                                $temp .= ' style="list-style-type: lower-alpha;"';
                                break;
                            // standard numeric
                            case '1':
                                $temp .= ' style="list-style-type: decimal;"';
                                break;
                        }

                        $temp .= '>';
                        break;
                    // unordered list
                    case 'list:u':
                        $nextLI = false;
                        $temp .= '<ul>';
                        break;
                    // bold
                    case 'b':
                        $temp .= '<span style="font-weight: bold;">';
                        break;
                    // italic
                    case 'i':
                        $temp .= '<span style="font-style: italic;">';
                        break;
                    // underline
                    case 'u':
                        $temp .= '<span style="text-decoration: underline;">';
                        break;
                    // font face
                    case 'font':
                        $temp .= '<span style="font-family: '.$attribute.';">';
                        break;
                    // font size
                    case 'size':
                        $temp .= '<span style="font-size: '.$attribute.'px;">';
                        break;
                    // font color
                    case 'color':
                        $temp .= '<span style="color: '.$attribute.';">';
                        break;
                    // subscript
                    case 'sub':
                        $temp .= '<sub>';
                        break;
                    // superscript
                    case 'sup':
                        $temp .= '<sup>';
                        break;
                    // img has no ending tag in (X)HTML
                    case 'img':
                        $content = '';

                        // skips closing character
                        self::expect(']');

                        // reads tag content
                        while( strtolower( self::getActual(6) ) != '[/img]')
                        {
                            $content .= self::getCurrent();
                        }

                        // appends image tag
                        $temp .= '<img src="'.$content.'" alt="'.$content.'"/>';

                        // skips closing tag
                        // last closing element will be skipped after the loop
                        self::expect('[/img');

                        // removes current tag from opened array
                        array_pop($opened);

                        break;
                    // URL link
                    case 'url':
                        $temp .= '<a class="outLink" href="'.$attribute.'">';
                        break;
                    // mail link
                    case 'mail':
                    case 'email':
                        $temp .= '<a class="outLink" href="mailto:'.$attribute.'">';
                        break;
                    // quoted text
                    case 'quote':
                        $temp .= '<div style="border: 1px solid #000000; padding: 10px; background-color: #FAFCFE; color: #050301;">'.($attribute ? '<span style="font-weight: bold;">'.$attribute.'</span>:'."\n\n" : '');
                        break;
                    // coded text
                    case 'code':
                        $temp .= '<div style="border: 1px solid #000000; padding: 10px; font-family: Courier, monospace; white-space: nowrap; background-color: #FAFCFE; color: #057E01;">';

                        // skips closing character
                        self::expect(']');

                        // skips all BB codes until ending tag
                        while( strtolower( self::getActual(7) ) != '[/code]')
                        {
                            $temp .= self::getCurrent();
                        }

                        // skips closing tag
                        // last closing element will be skipped after the loop
                        self::expect('[/code');

                        // removes current tag from opened array
                        array_pop($opened);

                        // closes text block
                        $temp .= '</div>';

                        break;
                    // PHP code
                    case 'php':
                        $temp .= '<div style="border: 1px solid #000000; padding: 10px; font-family: Courier, monospace; white-space: nowrap; background-color: #FAFCFE;">';

                        $content = '';

                        // skips closing character
                        self::expect(']');

                        // reads tag content
                        while( strtolower( self::getActual(6) ) != '[/php]')
                        {
                            $content .= self::getCurrent();
                        }

                        // highlighting PHP syntax
                        $temp .= str_replace('&nbsp;', '&#160;', highlight_string( html_entity_decode($content), true) );

                        // skips closing tag
                        // last closing element will be skipped after the loop
                        self::expect('[/php');

                        // removes current tag from opened array
                        array_pop($opened);

                        // closes text block
                        $temp .= '</div>';

                        break;
                    // aligned text
                    case 'align':
                        $temp .= '<div style="text-align: '.$attribute.';">';
                        break;
                    // centered text
                    case 'center':
                        $temp .= '<div style="text-align: center;">';
                        break;
                    // list item
                    case '*':
                        $temp .= ($nextLI ? '</li>' : '') . '<li>';

                        // next list item will be closed
                        $nextLI = true;

                        // * has no closing tag
                        array_pop($opened);
                        break;
                    // unsupported tags will be appended to results
                    default:
                        $temp .= '['.$name.($attribute ? ':' : $attribute).']';
                }
            }

            // skips opening tag begin
            self::expect(']');
        }

        // closes all opened tags
        foreach($opened as $open)
        {
            // here will be no default as unsupported tags ware already aded in while
            switch($name)
            {
                // ordered list
                case 'list:o':
                    $temp .= '</ol>';
                    break;
                // unordered list
                case 'list:u':
                    $temp .= '</ul>';
                    break;
                // end of span tag for certain BB codes
                case 'b':
                case 'i':
                case 'u':
                case 'font':
                case 'size':
                case 'color':
                    $temp .= '</span>';
                    break;
                // subscript
                case 'sub':
                    $temp .= '</sub>';
                    break;
                // superscript
                case 'sup':
                    $temp .= '</sup>';
                    break;
                // link
                case 'url':
                case 'mail':
                    $temp .= '</a>';
                    break;
                // ending of blocks of text
                case 'quote':
                case 'align':
                case 'center':
                    $temp .= '</div>';
                    break;
            }
        }

        return $temp;
    }
}

?>
