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
        // reads server status
        $status = POT::getInstance()->serverStatus($server['content'], $server['port']);

        // offline message
        if(!$status)
        {
            $language = OTSCMS::getResource('Language');
            return $language['Modules.Online.offline'];
        }
        // online status processing
        else
        {
            // finding online statistics in respond
            $online = $status->getOnlinePlayers();
            $max = $status->getMaxPlayers();

            // checks if current online players are higher number then previous
            if($online > $server['maximum'])
            {
                // saves new count
                $server['maximum'] = $online;
                $server->save();
            }

            // formating results
            return $online. (($server['maximum'] != $online && $server['maximum'] != $max) ? ' (' . $server['maximum'] . ')' : '') . '/' . $max;
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
    public static function guildAccess(OTS_Guild $guild)
    {
        // fetches the highest user access level in given guild
        $access = OTSCMS::getResource('DB')->query('SELECT MAX({guild_ranks}.`level`) AS `access` FROM {guild_ranks}, {players} WHERE {guild_ranks}.`guild_id` = ' . $guild->getId() . ' AND {guild_ranks}.`id` = {players}.`rank_id` AND {players}.`account_id` = ' . User::$number)->fetch();
        return $access['access'];
    }
}

?>
