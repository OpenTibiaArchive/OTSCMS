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

// gets parent's id from URL or form
// pre-loads HTTP data
$post = InputData::read('bb');

// checks if the posted character belongs to user's account
$author = new OTS_Player($post['from']);

if(!$author->loaded || $author->account->id != User::$number)
{
    throw new HandledException('NotOwner');
}

$post['name'] = trim($post['name']);

// checks if the topic user try to reply isn't closed
// only if he/she is posting a reply, not new topic
if(!$post['istopic'])
{
    // loads topic informations
    $closed = new CMS_Post( (int) $post['upperid']);

    // mainly checks topic status
    if($closed['closed'])
    {
        $message = $template->createComponent('Message');
        $message['message'] = $language['Modules.Topic.Closed'];
        return;
    }
}
// othwerise title cann't be empty
elseif( empty($post['name']) )
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Topic.EmptyName'];
    return;
}

// creates new post
$insert = new CMS_Post();
$insert['name'] = htmlspecialchars($post['name']);
$insert['istopic'] = $post['istopic'];
$insert['upperid'] = $post['upperid'];
$insert['closed'] = 0;
$insert['pinned'] = 0;
$insert['content'] = $post['content'];
$insert['poster'] = $author->id;
$insert['date_time'] = time();

// checks if there are any instructions from administrator to do with this topics
if( User::hasAccess(3) && isset($post['after']) )
{
    // gets the value for posted instruction
    switch($post['after'])
    {
        // pined
        case '1':
            $insert['pinned'] = 1;
            break;
        // closed
        case '2':
            $insert['closed'] = 1;
            break;
        // pined and closed
        case '3':
            $insert['closed'] = 1;
            $insert['pinned'] = 1;
            break;
    }
}

// saves new post
$insert->save();

InputData::write('id', $insert['id']);
OTSCMS::call('Topic', 'view');

?>
