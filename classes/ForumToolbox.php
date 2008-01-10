<?php
/*
    This file is part of OTSCMS (http://www.otscms.com/) project.

    Copyright (C) 2005 - 2008 Wrzasq (wrzasq@gmail.com)

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
    Usefull forum-related routines.
*/

class ForumToolbox
{
    // often used queries
    private static $subForums;

    // prepares main statements
    public static function init()
    {
        $db = OTSCMS::getResource('DB');

        self::$subForums = $db->prepare('SELECT `id` FROM [boards] WHERE `upperid` = :id');
    }

    // returns number of topics in forum
    public static function countTopics($id)
    {
        // default count
        $count = OTSCMS::getResource('DB')->query('SELECT COUNT(`id`) AS `count` FROM [posts] WHERE `istopic` = 1 AND `upperid` = ' . $id)->fetch();
        $count = $count['count'];

        // loads sub forums
        self::$subForums->execute( array(':id' => $id) );
        foreach(self::$subForums as $sub)
        {
            $count += self::countTopics($sub['id']);
        }

        // returns number of topics in subforums plus number of topics in current forum
        return $count;
    }

    // returns record of last post in topic
    public static function getLastTopicPost($id)
    {
        // loads post
        return OTSCMS::getResource('DB')->query('SELECT `id`, `date_time`, `poster` FROM [posts_with_authors] WHERE (`istopic` = 0 AND `upperid` = ' . $id . ') OR `id` = ' . $id . ' ORDER BY `date_time` DESC LIMIT 1')->fetch();
    }

    // returns record of last post in forum
    public static function getLastForumPost($id)
    {
        $time = array();

        // loads posts from subforums
        self::$subForums->execute( array(':id' => $id) );
        foreach(self::$subForums as $sub)
        {
            $time[] = self::getLastForumPost($sub['id']);
        }

        // loads post from current forum
        foreach( OTSCMS::getResource('DB')->query('SELECT `id` FROM [posts] WHERE `istopic` = 1 AND `upperid` = ' . $id) as $topic)
        {
            $time[] = self::getLastTopicPost($topic['id']);
        }

        // loads defaultly first post
        @$current = current($time);

        // finds the most recent post
        foreach($time as $post)
        {
            // checks date
            if($current['date_time'] < $post['date_time'])
            {
                $current = $post;
            }
        }

        // returns last post of forum
        return $current;
    }
}

ForumToolbox::init();

?>
