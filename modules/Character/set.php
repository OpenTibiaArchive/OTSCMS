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

// gets id from URL or form
$profile = InputData::read('profile');

// loads current profile
$row = new CMS_Profile( (int) InputData::read('id') );

// updates rows which are not empty
foreach($profile as $key => $value)
{
    // only non-empty fields are saved
    $row[$key] = $value === '' ? null : $value;
}

// saves update
$row->save();

// outputs message
$message = $template->createComponent('Message');
$message['message'] = $language['Modules.Character.Updated'];

// prepares module parameters
$names = explode('.', $row['name']);
InputData::write('gender', $names[0]);
InputData::write('vocation', $names[1]);

// moves to profiles settings
OTSCMS::call('Character', 'settings');

?>
