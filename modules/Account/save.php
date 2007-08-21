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

// pre-loads HTTP data
$user = InputData::read('user');

// checks avatar size
if( !empty($user['avatar']) )
{
    // detects image type
    if( preg_match('/\.gif$/i', $user['avatar']) )
    {
        // loads GIF
        $image = @imagecreatefromgif($user['avatar']);
    }
    if( preg_match('/\.png$/i', $user['avatar']) )
    {
        // loads PNG
        $image = @imagecreatefrompng($user['avatar']);
    }
    if( preg_match('/\.(jpg|jpeg)$/i', $user['avatar']) )
    {
        // loads JPEG
        $image = @imagecreatefromjpeg($user['avatar']);
    }

    // checks if image was loaded
    if(!$image)
    {
        // if not then resets avatar
        $user['avatar'] = '';
        $message = $template->createComponent('Message');
        $message['message'] = $language['Modules.Account.WrongAvatar'];
    }
    // checks the size of avatar
    elseif( imagesx($image) > $config['forum.avatar.max_x'] || imagesy($image) > $config['forum.avatar.max_y'])
    {
        // if it's bigger then maximum allowed size then return error
        $message = $template->createComponent('Message');
        $message['message'] = $language['Modules.Account.WrongAvatar'];
    }
}

// updates profile
$account = $ots->createObject('Account');
$account->load(User::$number);
$account->setCustomField('signature', $user['signature']);
$account->setCustomField('website', $user['website']);

// if avatar was fine
if( !isset($message) )
{
    $account->setCustomField('avatar', $user['avatar']);
}

// displays text so the user could see that it succeeded
$message = $template->createComponent('Message');
$message['message'] = $language['Modules.Account.ProfileUpdated'];

// there is nothing to display
// redirects internaly to profile edition page
OTSCMS::call('Accounts', 'account');

?>
