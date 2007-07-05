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

$pm = InputData::read('bb');

// loads characters
$from = new OTS_Player($pm['from']);
$to = new OTS_Player($pm['to']);

// couldn't find character(s), or message addressed to author-self
if(!($from['account_id'] && $to['account_id']) || $from['account_id'] == $to['account_id'])
{
    $message = $template->createComponent('PM');
    $message['message'] = $language['Modules.PM.Error'];
    return;
}

// author doesn't belong to user
if($from['account_id'] != User::$number)
{
    throw new NoAccessException();
}

// inserting message
$insert = new CMS_PM();
$insert['name'] = $pm['name'];
$insert['content'] = $pm['content'];
$insert['from'] = $from['id'];
$insert['to'] = $to['id'];
$insert['read'] = 0;
$insert['date_time'] = time();
$insert->save();

// successfully sent
OTSCMS::call('PM', 'sent');

?>
