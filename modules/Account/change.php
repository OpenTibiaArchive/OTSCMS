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

// loads HTTP data in correct order
$oldpassword = InputData::read('oldpassword');
$newpassword = InputData::read('newpassword');
$newpassword2 = InputData::read('newpassword2');

// checks if old password matches real password and new passwords matches
// thats for sure that user know what he typed as he/she dont see it durning typing
if(($config['system.md5'] ? md5($oldpassword) : $oldpassword) != Session::read('userpassword') || $newpassword != $newpassword2)
{
    throw new HandledException('WrongPassword');
}

$newpassword = $config['system.md5'] ? md5($newpassword) : $newpassword;

// updates password
$account = new OTS_Account(User::$number);
$account['password'] = $newpassword;
$account->save();

// and session so the user doesn't have to relog
Session::write('userpassword', $newpassword);

// checks if user uses cookie
if( Cookies::read('userpassword') )
{
    // and if so changes it also
    Cookies::write('userpassword', Session::read('userpassword') );
}

OTSCMS::call('Account', 'account');

?>
