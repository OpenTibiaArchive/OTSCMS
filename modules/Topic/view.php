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

// loads post
$post = new CMS_Post( (int) InputData::read('id') );

// checks if the post is a topic
if(!$post['istopic'])
{
    // and if not then load the topic
    $post = new CMS_Post($post['upperid']);
}

// trace to forum
$trace = $template->createComponent('ForumTrace');
$trace['id'] = $post['upperid'];

$page = InputData::read('page');
$page = isset($page) ? (int) $page : 1;

// pagination if required
$pages = $template->createComponent('Pages');
$pages['page'] = $page;
$pages['link'] = '/posts/' . $post['id'];

    // counts of pages
$count = $db->query('SELECT COUNT(`id`) / ' . $config['forum.limit'] . ' AS `count` FROM [posts] WHERE (`istopic` = 0 AND `upperid` = ' . $post['id'] . ') OR `id` = ' . $post['id'])->fetch();
$pages['pages'] = ceil($count['count']);

// posting bar
if(User::$logged)
{
    $bar = $template->createComponent('ForumBar');
    $bar['boardid'] = $post['upperid'];

    // if it's possible to post replies
    if(!$post['closed'])
    {
        $bar['topicid'] = $post['id'];
    }
}

// topic page
$topic = $template->createComponent('Topic');
$topic['id'] = $post['id'];
$topic['closed'] = $post['closed'];
$topic['pinned'] = $post['pinned'];
$topic['page'] = $page;
$topic->admin = User::hasAccess(3);
$topic->user = User::$logged;

// bottom pagination links
$template->appendComponent($pages);

?>
