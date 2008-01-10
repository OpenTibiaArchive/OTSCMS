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

$template->addJavaScript('forum');

// pre-loads HTTP data
// if it wan't set the it will become 0 - the top forum
$id = (int) InputData::read('id');

// no trace for root board is required
if($id)
{
    $trace = $template->createComponent('ForumTrace');
    $trace['id'] = $id;
}

// sub boards
$subBoards = $template->createComponent('TableList');
$subBoards['id'] = 'boardsTable';
$subBoards->addField('name', $language['Modules.Forum.Boards']);
$subBoards->addField('posts', $language['Modules.Forum.Posts']);
$subBoards->addField('topics', $language['Modules.Forum.Topics']);
$subBoards->addField('lastPost', $language['Modules.Forum.LastPost']);

// admin options
if( User::hasAccess(3) )
{
    $subBoards->addAction('edit', $language['main.admin.EditSubmit']);
    $subBoards->addAction('remove', $language['main.admin.DeleteSubmit']);
    $subBoards->module = 'Forum';
}

$subBoards->idPrefix = 'boardID_';

$subs = array();

// composes list
foreach( $db->query('SELECT `id`, `name`, `content` FROM [boards] WHERE `upperid` = ' . $id) as $sub)
{
    // composes forum link
    $root = XMLToolbox::createDocumentFragment();
    $a = XMLToolbox::createElement('a');
    $a->setAttribute('href', 'forum/' . $sub['id']);
    $a->addContent($sub['name']);

    $root->addContents($a, XMLToolbox::createElement('br'), $sub['content']);

    // last post on given board
    $lastPost = ForumToolbox::getLastForumPost($sub['id']);

    if($lastPost)
    {
        // composes block
        $last = XMLToolbox::createDocumentFragment();

        $a = XMLToolbox::createElement('a');
        $img = XMLToolbox::createElement('img');
        $a->setAttribute('href', 'posts/' . $lastPost['id']);
        $img->setAttribute('src', $template['baseHref'] . 'images/arrow.png');
        $img->setAttribute('alt', $language['Modules.Forum.LastPost']);
        $a->addContent($img);
        $last->addContents($a, date($config['site.date_format'], $lastPost['date_time']), XMLToolbox::createElement('br'), $language['Modules.Forum.by'] . ' ');

        $a = XMLToolbox::createElement('a');
        $a->setAttribute('href', 'characters/' . urlencode($lastPost['poster']) );
        $a->addContent($lastPost['poster']);
        $last->addContent($a);
    }
    // no posts message
    else
    {
        $last = $language['Modules.Forum.NoPosts'];
    }

    $count = $db->query('SELECT COUNT(DISTINCT [posts].`id`) AS `count` FROM [posts], (SELECT `id` FROM [posts] WHERE `istopic` = 1 AND `upperid` = ' . $sub['id'] . ') AS `topics` WHERE ([posts].`istopic` = 1 AND [posts].`upperid` = ' . $sub['id'] . ') OR ([posts].`istopic` = 0 AND [posts].`upperid` = `topics`.`id`)')->fetch();
    $subs[] = array('id' => $sub['id'], 'name' => $root, 'posts' => $count['count'], 'topics' => ForumToolbox::countTopics($sub['id']), 'lastPost' => $last);
}

$subBoards['list'] = $subs;

// admin management
if( User::hasAccess(3) )
{
    // new sub-board form
    $form = $template->createComponent('AdminForm');
    $form['action'] = 'admin/module=Forum&command=insert&board[upperid]=' . (int) $id;
    $form['submit'] = $language['main.admin.InsertSubmit'];
    $form['id'] = 'forumForm';

    // form fields
    $form->addField('board[name]', ComponentAdminForm::FieldText, $language['Modules.Forum.Name']);
    $form->addField('board[content]', ComponentAdminForm::FieldText, $language['Modules.Forum.Content']);
}

// in top forum there cann't be topics
if($id)
{
    $page = InputData::read('page');
    $page = isset($page) ? (int) $page : 1;

    // pagination if required
    $pages = $template->createComponent('Pages');
    $pages['page'] = $page;
    $pages['link'] = 'forum/' . $id;

    // counts of pages
    $count = $db->query('SELECT COUNT(`id`) / ' . $config['forum.limit'] . ' AS `count` FROM [posts] WHERE `istopic` = 1 AND `upperid` = ' . $id)->fetch();
    $pages['pages'] = ceil($count['count']);

    // posting bar
    if(User::$logged)
    {
        $bar = $template->createComponent('ForumBar');
        $bar['boardid'] = $id;
    }

    // board topics
    $topics = $template->createComponent('BoardTopics');
    $topics['id'] = $id;
    $topics['page'] = $page;
    $topics->admin = User::hasAccess(3);

    // bottom pagination links
    $template->appendComponent($pages);
}

?>
