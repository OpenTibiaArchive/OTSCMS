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

// voting handling
$template->addJavaScript('poll');

// gets id from URL or form
$poll = new CMS_Poll( (int) InputData::read('id') );

// checks if the id is correct
if(!$poll['name'])
{
    OTSCMS::call('Poll', 'list');
    return;
}

// prepares voting form
$form = $template->createComponent('PollVoting');
$form['name'] = $poll['name'];
$form['content'] = $poll['content'];
$form['canVote'] = !Toolbox::haveVoted($poll['id']);

$options = array();

// loads poll options
$votes = $db->prepare('SELECT COUNT(`name`) AS `count` FROM [votes] WHERE `name` = :name');
foreach( $db->query('SELECT `id`, `name` FROM [options] WHERE `poll` = ' . $poll['id']) as $option)
{
    $votes->execute( array(':name' => $option['id']) );
    $options[] = $option + $votes->fetch();
}

$form['options'] = $options;

?>
