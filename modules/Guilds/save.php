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

$guild = new OTS_Guild( (int) InputData::read('id') );
$access = Toolbox::guildAccess($guild);

// if not a gamemaster checks if user is a leader
if( !User::hasAccess(3) && $access < 2)
{
    throw new NoAccessException();
}

$data = InputData::read('guild');

// checks icon sizes
if( !empty($data['icon']) )
{
    // detects image type
    if( preg_match('/\.gif$/i', $data['icon']) )
    {
        // loads GIF
        $image = @imagecreatefromgif($data['icon']);
    }
    if( preg_match('/\.png$/i', $data['icon']) )
    {
        // loads PNG
        $image = @imagecreatefrompng($data['icon']);
    }
    if( preg_match('/\.(jpg|jpeg)$/i', $data['icon']) )
    {
        // loads JPEG
        $image = @imagecreatefromjpeg($data['icon']);
    }

    // checks if image was loaded
    if(!$image)
    {
        // if not then resets avatar
        $data['icon'] = $guild->getCustomField('icon');
        $message = $template->createComponent('Message');
        $message['message'] = $language['Modules.Account.WrongAvatar'];
    }
    // checks the size of avatar
    elseif( imagesx($image) > $config['forum.avatar.max_x'] || imagesy($image) > $config['forum.avatar.max_y'])
    {
        // if it's bigger then maximum allowed size then return error
        $data['icon'] = $guild->getCustomField('icon');
        $message = $template->createComponent('Message');
        $message['message'] = $language['Modules.Account.WrongAvatar'];
    }
}

// updates guild info
$guild->setCustomField('content', $data['content']);
$guild->setCustomField('icon', $data['icon']);

// moves to guild management
OTSCMS::call('Guilds', 'manage');

?>
