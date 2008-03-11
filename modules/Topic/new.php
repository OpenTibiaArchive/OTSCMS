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

// gets board/topic id from URL or form
if( InputData::read('boardid') )
{
    // if it's board ID then we have to create new topic
    $id = (int) InputData::read('boardid');
    $newTopic = 1;
}
else
{
    // else we just posts a reply
    $id = (int) InputData::read('topicid');
    $newTopic = 0;
}

// checks if there is any post to quote
if( InputData::read('quoteid') )
{
    // loads post to quote
    $post = new CMS_Post( (int) InputData::read('quoteid') );
    $post = '[quote]'.$post['content'].'[/quote]';
}
else
{
    // else sets quote text to empty
    $post = '';
}

$account = new OTS_Account(User::$number);

// bb message editor
$form = $template->createComponent('BBEditor');
$form['action'] = 'admin/module=Topic&command=insert&bb[upperid]=' . $id . '&bb[istopic]=' . $newTopic;
$form['fields'] = array('content' => $post);
$form['characters'] = $account->playersList;
$form->module = 'Topic';
$form->adminActions = $newTopic && User::hasAccess(3);

?>
