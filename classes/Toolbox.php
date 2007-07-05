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
    Some usefull global routines.
*/

class Toolbox
{
    // redirects browser and ends script execution
    public static function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    // unsigned integer result of ip2long()
    public static function ip2long($ip)
    {
        return sprintf('%u', ip2long($ip) );
    }

    // checks if image for given object is placed
    // supported extensions are .gif, .png, .jpg, .jpeg
    public static function imageExists($file)
    {
        // constructs directory path
        $config = OTSCMS::getResource('Config');
        $path = $config['directories.images'] . $file . '.';

        // checks for .png file
        if( file_exists($path.'png') )
        {
            return '.png';
        }

        // checks for .gif file
        if( file_exists($path.'gif') )
        {
            return '.gif';
        }

        // checks for .jpg file
        if( file_exists($path.'jpg') )
        {
            return '.jpg';
        }

        // checks for .jpeg file
        if( file_exists($path.'jpeg') )
        {
            return '.jpeg';
        }

        // if we comes here it means that image doesn't exist
        return false;
    }

    // returns list of subdirectories of given directory
    public static function subDirs($path)
    {
        $list = array();

        $dir = new DirectoryIterator($path);
        foreach($dir as $file)
        {
            // finding all subdirectories except . and .. symbols
            if( $dir->isDir() && !$dir->isDot() )
            {
                $list[] = $dir->getFilename();
            }
        }

        return $list;
    }

    /*
        online statistics bases on brave's "Players online" script
    */

    // returns server status
    public static function getPlayersCount(CMS_Online $server)
    {
        // connects to server
        // gives maximum 5 seconds to connect
        $socket = @fsockopen($server['content'], $server['port'], $errorCode, $errorString, 5);

        // if connected then checking statistics
        if($socket)
        {
            // sets 5 second timeout for reading and writing
            stream_set_timeout($socket, 5);

            // sends packet with request
            // 06 - length of packet, 255, 255 is the comamnd identifier, 'info' is a request
            fwrite($socket, chr(6).chr(0).chr(255).chr(255).'info');

            // reads respond
            // donno why, but while( !foef($socket) ) doesnt work here
            // 2048 bytes should be ok, but you can change it if it's not enought
            $data = fread($socket, 2048);

            // closing connection to current server
            fclose($socket);

            // sometimes server returns empty info
            if( empty($data) )
            {
                // returns offline text
                $language = OTSCMS::getResource('Language');
                return $language['Modules.Online.offline'];
            }

            // finding online statistics in respond (respond is in XML format)
            $xml = DOMDocument::loadXML($data);
            $data = $xml->getElementsByTagName('players')->item(0);
            $data = array( $data->getAttribute('online'), $data->getAttribute('max') );

            // checks if current online players are higher number then previous
            if($data[0] > $server['maximum'])
            {
                // saves new count
                $server['maximum'] = $data[0];
                $server->save();
            }

            // formating results
            return $data[0]. (($server['maximum'] != $data[0] && $server['maximum'] != $data[1]) ? ' ('.$server['maximum'].')' : '') .'/'.$data[1];
        }
        // returns null when server is offline
        else
        {
            // returns offline text
            $language = OTSCMS::getResource('Language');
            return $language['Modules.Online.offline'];
        }
    }

    // checks if user already voted in that poll
    public static function haveVoted($id)
    {
        // at all it doesn't belong to that function, but we also always have to check that so we can put it once here
        if(!User::$logged)
        {
            return true;
        }

        // checks if user voted to any option of such poll
        $vote = OTSCMS::getResource('DB')->query('SELECT COUNT([votes].`name`) AS `count` FROM [options], [votes] WHERE [options].`poll` = ' . $id . ' AND [options].`id` = [votes].`name` AND [votes].`content` = ' . User::$number)->fetch();

        return $vote['count'] > 0;
    }

    // dumps record set into associative array
    public static function dumpRecords(PDOStatement $result)
    {
        $array = array();

        // $result must have `key` and `value` field aliases
        foreach($result as $row)
        {
            $array[ $row['key'] ] = $row['value'];
        }

        return $array;
    }

    // returns translation of given language from given string
    public static function languagePart($string, $language)
    {
        // finds given language translation
        foreach( explode('<![_lang_]!>', $string) as $string)
        {
            if( substr($string, 0, strlen($language) + 4) == '__' . $language . '__')
            {
                // 've found it
                return substr($string, strlen($language) + 4);
            }
        }
    }

    // returns user's access level in given guild
    public static function guildAccess($guild, $account)
    {
        // fetches the highest user access level in given guild
        $access = OTSCMS::getResource('DB')->query('SELECT MAX(`level`) AS `access` FROM [guild_members] WHERE `guild_id` = ' . $guild . ' AND `account` = ' . $account)->fetch();
        return $access['access'];
    }
}

?>